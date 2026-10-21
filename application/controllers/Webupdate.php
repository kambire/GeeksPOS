<?php
/**
 * GeeksPOS -  Accounting,  Invoicing  and CRM Application
 * Copyright (c) UltimateKode. All Rights Reserved
 * ***********************************************************************
 *
 *  Email: support@ultimatekode.com
 *  Website: https://www.ultimatekode.com
 *
 *  ************************************************************************
 *  * This software is furnished under a license and may be used and copied
 *  * only  in  accordance  with  the  terms  of such  license and with the
 *  * inclusion of the above copyright notice.
 *  * If you Purchased from Codecanyon, Please read the full License from
 *  * here- http://codecanyon.net/licenses/standard/
 * ***********************************************************************
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Webupdate extends CI_Controller
{
    public function __construct()
    {

        parent::__construct();
        $this->load->library("Aauth");
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        if ($this->aauth->get_user()->roleid < 5) {

            exit('Sorry! You have insufficient permissions to access this section');

        }
        $this->li_a = 'misc_settings';


    }

    public function index()
    {
        $head['title'] = "Update";
        $url = file_get_contents(FCPATH . '/version.json');
        $data = json_decode($url, true);

        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('update/update', $data);
        $this->load->view('fixed/footer');

    }

    public function download_update($ver = 0)
    {
        $url = file_get_contents(FCPATH . '/version.json');
        $version = json_decode($url, true);
        $current_build = (int)$version['build'];
        $this->session->set_userdata('build', $current_build);
        $this->session->set_userdata('step', 0);
        $this->session->set_userdata('upto', false);

        echo 'Current version: ' . $version['version'] . ' (build ' . $current_build . ')' . "\n";
        echo 'Update source: GitHub repository ' . UPDATE_REPO . "\n";
        echo 'Looking for updates...' . "\n";

        $release = $this->find_update_release($current_build);

        if (!$release) {
            echo 'Your application is already up to date.';
            exit;
        }

        $next_build = $release['build'];
        $time_temp = rand(19, 999);
        $this->session->set_userdata('upto', true);
        $this->session->set_userdata('temp_id', $time_temp);

        echo 'Update found: build ' . $next_build . ' (release "' . $release['name'] . '")' . "\n";
        echo 'Downloading update files from GitHub...' . "\n";

        $zipFile = FCPATH . 'userfiles' . DIRECTORY_SEPARATOR . 'update_' . $next_build . '_' . $time_temp . '.zip';
        if (!$this->github_download($release['url'], $zipFile)) {
            $this->session->set_userdata('upto', false);
            echo 'Error: could not download the update package from GitHub.';
            exit;
        }

        echo 'Update files downloaded.' . "\n";
        echo 'Extracting downloaded files...' . "\n";

        $extractPath = FCPATH . 'userfiles' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'up' . $next_build . '_' . $time_temp;
        if (!is_dir($extractPath) && !@mkdir($extractPath, 0777, true)) {
            $this->session->set_userdata('upto', false);
            @unlink($zipFile);
            echo 'Update Process Halted! Cannot create directory ' . $extractPath . ' — please give write permissions to the userfiles/temp directory.';
            exit;
        }
        $zip = new ZipArchive;

        if ($zip->open($zipFile) !== true) {
            $this->session->set_userdata('upto', false);
            @unlink($zipFile);
            echo 'Update Process Halted! Extracting failed (corrupted download or ZipArchive extension missing).';
            exit;
        }
        if (!$zip->extractTo($extractPath)) {
            $zip->close();
            $this->session->set_userdata('upto', false);
            @unlink($zipFile);
            echo 'Update Process Halted! Extracting failed.';
            exit;
        }
        $zip->close();
        @unlink($zipFile);

        if (!is_file($extractPath . DIRECTORY_SEPARATOR . 'files.json')) {
            $this->session->set_userdata('upto', false);
            echo 'Update Process Halted! The update package is invalid (files.json missing).';
            exit;
        }

        echo 'Extracting success.' . "\n";
        echo 'Update downloaded. Ready to install.';
        exit;
    }

    public function install_update()
    {

        $build = $this->session->userdata('build');
        $upto = $this->session->userdata('upto');
        $temp_id = $this->session->userdata('temp_id');
        $build = $build + 1;
        if ($build && $upto) {

            echo 'Updating files in your system... build ' . $build . "\n";
            $updateDir = FCPATH . 'userfiles' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'up' . $build . '_' . $temp_id;
            $files = json_decode(file_get_contents($updateDir . DIRECTORY_SEPARATOR . 'files.json'), true);
            if (!is_array($files)) {
                exit('Update Process Halted! Invalid files.json in the update package.');
            }
            $i = 0;
            $count_f = count($files);
            $b = 0;
            echo 'File backup process started...' . "\n";
            $last_build = $build - 1;
            foreach ($files as $row) {

                echo FCPATH . $row['path'] . $row['file'] . "\n";

                if (@copy(FCPATH . $row['path'] . $row['file'], $updateDir . DIRECTORY_SEPARATOR . $row['path'] . 'bak_v_' . $last_build . '_' . $row['file'])) {
                    $b++;
                    echo 'Ok' . "\n";
                } else {
                    if (!is_dir(FCPATH . $row['path'])) {
                        if (mkdir(FCPATH . $row['path'], 0777, true)) {
                            echo 'New Directory Created..' . FCPATH . $row['path'] . "\n";
                        } else {
                            echo 'Error: New Directory Creation..' . FCPATH . $row['path'] . ' Failed!' . "\n";
                            exit('Update Process Halted! Files backup failed');
                        }

                    } else {
                        $z = $b + 1;

                        if (is_file(FCPATH . $row['path'] . $row['file'])) {
                            echo 'Critical Notice: Files backup failed ... File number ' . $z . "\n";
                            exit('Update Process Halted! Files backup failed');
                        } else {
                            echo 'Ordinary Notice: Files backup failed ... File number ' . $z . "\n";
                        }
                    }


                }
            }
//update files
            $f = 0;
            echo 'File update process started...' . "\n";

            foreach ($files as $row) {

                echo FCPATH . $row['path'] . $row['file'] . "\n";
                if (copy($updateDir . DIRECTORY_SEPARATOR . $row['path'] . $row['file'], FCPATH . $row['path'] . $row['file'])) {

                    echo 'Ok' . "\n";

                    $f++;
                } else {
                    $z = $f + 1;
                    echo 'Files update failed ... File number ' . $z . "\n";
                }
            }

            if ($count_f == $f) {
                $this->session->set_userdata('dbupdate', true);
                $this->session->set_userdata('step', 2);
                echo 'Files Updated!' . "\n";

                // Keep version.json in sync with the installed build
                $vdata = json_decode(file_get_contents(FCPATH . 'version.json'), true);
                $vdata['build'] = (string)$build;
                file_put_contents(FCPATH . 'version.json', json_encode($vdata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                echo 'version.json updated to build ' . $build . '.' . "\n";

            } else {
                echo 'Some Files update failed ... Update Failed!' . "\n";
                exit('Update Process Halted! Files update failed');
            }
        } else {
            exit('Your application is already up to date.');
        }

        exit;
    }

    public function update_db()
    {
        ini_set('memory_limit', '-1');
        $ver = $this->session->userdata('build');
        $upto = $this->session->userdata('upto');
        $temp_id = $this->session->userdata('temp_id');
        $ver = $ver + 1;
        if ($ver && $upto) {
            $bdate = 'backup_' . date('Y_m_d_H_i_s');
            $this->load->dbutil();
            $backup = $this->dbutil->backup();
            $this->load->helper('file');
            write_file(FCPATH . 'userfiles/temp/' . $bdate . '.gz', $backup);
            echo 'Database Update Process Started...' . "\n";

            $sqlFile = FCPATH . 'userfiles' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'up' . $ver . '_' . $temp_id . DIRECTORY_SEPARATOR . 'update_build_' . $ver . '.sql';
            if (!is_file($sqlFile)) {
                echo 'No database update file found for build ' . $ver . '. Nothing to do.' . "\n";
                $this->session->set_userdata('step', 0);
                exit;
            }
            $sql = file_get_contents($sqlFile);
            $mysqli = @new mysqli($this->db->hostname, $this->db->username, $this->db->password, $this->db->database);
            if (mysqli_connect_errno()) {
                echo json_encode(array("success" => false, "message" => $mysqli->connect_error));
                exit();
            }
            $mysqli->multi_query($sql);
            do {
            } while (mysqli_more_results($mysqli) && mysqli_next_result($mysqli));
            if ($mysqli->close()) {
                echo 'Database Update Done...' . "\n";
                $this->session->set_userdata('step', 0);
            }
        } else {
            exit('Your application is already up to date.');
        }
        exit;
    }

    /**
     * Busca en los releases del repositorio de GitHub el paquete de actualización
     * más reciente con un build superior al instalado.
     * Convención: el asset se llama update_<build>.zip
     */
    private function find_update_release($current_build)
    {
        $response = $this->github_get('https://api.github.com/repos/' . UPDATE_REPO . '/releases?per_page=30');
        if ($response === false) {
            return false;
        }
        $releases = json_decode($response, true);
        if (!is_array($releases)) {
            return false;
        }
        $best = false;
        foreach ($releases as $release) {
            if (!empty($release['draft']) || empty($release['assets'])) {
                continue;
            }
            foreach ($release['assets'] as $asset) {
                if (preg_match('/^update_(\d+)\.zip$/i', $asset['name'], $m) && !empty($asset['browser_download_url'])) {
                    $build = (int)$m[1];
                    if ($build > $current_build && (!$best || $build > $best['build'])) {
                        $best = array(
                            'build' => $build,
                            'url' => $asset['browser_download_url'],
                            'name' => ($release['name'] !== '' && $release['name'] !== null) ? $release['name'] : $release['tag_name'],
                        );
                    }
                }
            }
        }
        return $best;
    }

    private function github_get($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/vnd.github+json',
            'User-Agent: GeeksPOS-Updater',
        ));
        $output = curl_exec($ch);
        curl_close($ch);
        return ($output === false) ? false : $output;
    }

    private function github_download($url, $destination)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/octet-stream',
            'User-Agent: GeeksPOS-Updater',
        ));
        $zipResource = fopen($destination, "w");
        if (!$zipResource) {
            return false;
        }
        curl_setopt($ch, CURLOPT_FILE, $zipResource);
        $ok = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($zipResource);
        if ($ok === false || $httpcode != 200 || filesize($destination) == 0) {
            @unlink($destination);
            return false;
        }
        return true;
    }
}
