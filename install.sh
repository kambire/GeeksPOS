#!/usr/bin/env bash
# ==============================================================================
#  ██████╗ ███████╗███████╗██╗  ██╗███████╗██████╗  ██████╗ ███████╗
# ██╔════╝ ██╔════╝██╔════╝██║ ██╔╝██╔════╝██╔══██╗██╔═══██╗██╔════╝
# ██║  ███╗█████╗  █████╗  █████═╝ ███████╗██████╔╝██║   ██║███████╗
# ██║   ██║██╔══╝  ██╔══╝  ██╔═██╗ ╚════██║██╔═══╝ ██║   ██║╚════██║
# ╚██████╔╝███████╗███████╗██║ ╚██╗███████║██║     ╚██████╔╝███████║
#  ╚═════╝ ╚══════╝╚══════╝╚═╝  ╚═╝╚══════╝╚═╝      ╚═════╝ ╚══════╝
#
# Enterprise Edition Installer & Platform Provisioner for Ubuntu Server
# (C) 2026 GeeksPOS Systems - Todos los derechos reservados.
# ==============================================================================

set -eo pipefail

# Archivo de registro detallado para auditoría
LOG_FILE="/var/log/geekspos_install.log"
INSTALL_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TOTAL_STEPS=8
CURRENT_STEP=0

# Colores y Estilos ANSI
ESC="\033["
RESET="${ESC}0m"
BOLD="${ESC}1m"
DIM="${ESC}2m"
ITALIC="${ESC}3m"
UNDERLINE="${ESC}4m"

# Paleta Corporativa
C_PRIMARY="${ESC}38;5;39m"    # Azul Corporativo
C_SECONDARY="${ESC}38;5;75m"  # Celeste
C_SUCCESS="${ESC}38;5;42m"    # Verde Esmeralda
C_WARNING="${ESC}38;5;214m"   # Ámbar
C_DANGER="${ESC}38;5;196m"    # Rojo
C_PURPLE="${ESC}38;5;141m"   # Púrpura
C_GRAY="${ESC}38;5;244m"     # Gris
C_WHITE="${ESC}38;5;255m"    # Blanco puro
BG_PRIMARY="${ESC}48;5;24m"

# Manejador de Errores con Diagnóstico
handle_error() {
    local line_num="$1"
    tput cnorm 2>/dev/null || true
    echo ""
    echo -e "${C_DANGER}${BOLD}╭─────────────────────────────────────────────────────────────────────────────╮${RESET}"
    echo -e "${C_DANGER}${BOLD}│ ✖ ERROR CRÍTICO EN LA INSTALACIÓN (Línea: ${line_num})                        │${RESET}"
    echo -e "${C_DANGER}${BOLD}╰─────────────────────────────────────────────────────────────────────────────╯${RESET}"
    echo -e "  ${C_GRAY}Revise el registro detallado en:${RESET} ${C_WHITE}${BOLD}${LOG_FILE}${RESET}"
    echo -e "  ${C_GRAY}Últimas 10 líneas de error:${RESET}"
    echo -e "${C_DANGER}"
    tail -n 10 "${LOG_FILE}" 2>/dev/null || echo "No se pudo leer el archivo de log."
    echo -e "${RESET}"
    exit 1
}

trap 'handle_error $LINENO' ERR
trap 'tput cnorm 2>/dev/null || true; exit 1' INT TERM

# Funciones de Registro en Pantalla y Archivo
log_raw() {
    echo "$@" >> "${LOG_FILE}" 2>&1
}

step_header() {
    CURRENT_STEP=$((CURRENT_STEP + 1))
    echo ""
    echo -e "  ${C_PRIMARY}${BOLD}[${CURRENT_STEP}/${TOTAL_STEPS}]${RESET} ${C_WHITE}${BOLD}$1${RESET}"
    echo -e "  ${C_GRAY}───────────────────────────────────────────────────────────────────────────${RESET}"
    log_raw "=== [PASO ${CURRENT_STEP}/${TOTAL_STEPS}] $1 ==="
}

# Spinner con animación moderna y captura de salida
run_task() {
    local task_name="$1"
    shift
    local cmd=("$@")

    local spin_chars=('⠋' '⠙' '⠹' '⠸' '⠼' '⠴' '⠦' '⠧' '⠇' '⠏')
    local spin_idx=0

    # Iniciar comando en segundo plano
    tput civis 2>/dev/null || true
    ( "${cmd[@]}" ) >> "${LOG_FILE}" 2>&1 &
    local pid=$!

    while kill -0 "$pid" 2>/dev/null; do
        printf "\r    ${C_SECONDARY}${spin_chars[$spin_idx]}${RESET}  %-58s" "${task_name}..."
        spin_idx=$(( (spin_idx + 1) % 10 ))
        sleep 0.08
    done

    wait "$pid"
    local exit_code=$?
    tput cnorm 2>/dev/null || true

    if [ $exit_code -eq 0 ]; then
        printf "\r    ${C_SUCCESS}✔${RESET}  ${C_WHITE}%-58s${RESET} [ ${C_SUCCESS}${BOLD}OK${RESET} ]\n" "${task_name}"
        log_raw "[OK] ${task_name}"
    else
        printf "\r    ${C_DANGER}✖${RESET}  ${C_WHITE}%-58s${RESET} [ ${C_DANGER}${BOLD}FALLÓ${RESET} ]\n" "${task_name}"
        log_raw "[FAILED] ${task_name} (Exit code: ${exit_code})"
        return $exit_code
    fi
}

# Banner Corporativo de Bienvenida
render_banner() {
    clear 2>/dev/null || true
    echo -e "${C_PRIMARY}${BOLD}"
    cat << "EOF"
  ██████╗ ███████╗███████╗██╗  ██╗███████╗██████╗  ██████╗ ███████╗
 ██╔════╝ ██╔════╝██╔════╝██║ ██╔╝██╔════╝██╔══██╗██╔═══██╗██╔════╝
 ██║  ███╗█████╗  █████╗  █████═╝ ███████╗██████╔╝██║   ██║███████╗
 ██║   ██║██╔══╝  ██╔══╝  ██╔═██╗ ╚════██║██╔═══╝ ██║   ██║╚════██║
 ╚██████╔╝███████╗███████╗██║ ╚██╗███████║██║     ╚██████╔╝███████║
  ╚═════╝ ╚══════╝╚══════╝╚═╝  ╚═╝╚══════╝╚═╝      ╚═════╝ ╚══════╝
EOF
    echo -e "${RESET}"
    echo -e "  ${C_WHITE}${BOLD}Plataforma Empresarial POS & Automatización de IA con WhatsApp${RESET}"
    echo -e "  ${C_GRAY}Versión: 2.5.0 LTS | Target: Ubuntu Server 20.04 / 22.04 / 24.04${RESET}"
    echo -e "  ${C_GRAY}Registro de instalación en:${RESET} ${C_SECONDARY}${LOG_FILE}${RESET}"
    echo ""
}

# ==============================================================================
# 1. VERIFICACIÓN DE PRIVILEGIOS Y ENTORNO PREVIO (PREFLIGHT AUDIT)
# ==============================================================================
step_preflight_checks() {
    step_header "Auditoría Previa del Sistema & Verificación de Recursos"

    # Verificar Root
    if [ "${EUID:-$(id -u)}" -ne 0 ]; then
        echo -e "    ${C_DANGER}✖ Permisos insuficientes.${RESET} Debe ejecutar como root:"
        echo -e "      ${C_YELLOW}sudo bash install.sh${RESET}"
        exit 1
    fi
    echo -e "    ${C_SUCCESS}✔${RESET} Privilegios de superusuario root confirmados."

    # Verificar Sistema Operativo
    if [ -f /etc/os-release ]; then
        . /etc/os-release
        OS_NAME="${NAME:-Linux}"
        OS_VER="${VERSION_ID:-}"
        OS_ID="${ID:-}"
        if [[ "$OS_ID" != "ubuntu" && "$OS_ID" != "debian" ]]; then
            echo -e "    ${C_WARNING}⚠ Advertencia:${RESET} Sistema operativo reportado: ${OS_NAME}. Se recomienda Ubuntu Server 20.04+."
        else
            echo -e "    ${C_SUCCESS}✔${RESET} Sistema operativo compatible: ${C_WHITE}${OS_NAME} ${OS_VER}${RESET}"
        fi
    fi

    # Verificar Arquitectura CPU
    ARCH=$(uname -m)
    if [[ "$ARCH" != "x86_64" && "$ARCH" != "aarch64" && "$ARCH" != "arm64" ]]; then
        echo -e "    ${C_DANGER}✖ Arquitectura de CPU no soportada:${RESET} ${ARCH}. Se requiere x86_64 o arm64."
        exit 1
    fi
    echo -e "    ${C_SUCCESS}✔${RESET} Arquitectura de hardware: ${C_WHITE}${ARCH}${RESET}"

    # Verificar Memoria RAM y optimizar Swap si es necesario
    TOTAL_RAM_MB=$(free -m | awk '/^Mem:/{print $2}')
    echo -e "    ${C_SUCCESS}✔${RESET} Memoria RAM detectada: ${C_WHITE}${TOTAL_RAM_MB} MB${RESET}"
    if [ "$TOTAL_RAM_MB" -lt 1800 ]; then
        echo -e "    ${C_WARNING}⚠ Memoria RAM ajustada (<2GB).${RESET} Verificando archivo de Swap..."
        TOTAL_SWAP_MB=$(free -m | awk '/^Swap:/{print $2}')
        if [ "$TOTAL_SWAP_MB" -lt 1024 ]; then
            echo -e "    ${C_SECONDARY}ℹ Creando archivo de Swap de 2GB para estabilidad de MariaDB y Docker...${RESET}"
            fallocate -l 2G /swapfile 2>/dev/null || dd if=/dev/zero of=/swapfile bs=1M count=2048 >> "${LOG_FILE}" 2>&1
            chmod 600 /swapfile
            mkswap /swapfile >> "${LOG_FILE}" 2>&1
            swapon /swapfile >> "${LOG_FILE}" 2>&1
            if ! grep -q '/swapfile' /etc/fstab; then
                echo '/swapfile none swap sw 0 0' >> /etc/fstab
            fi
            echo -e "    ${C_SUCCESS}✔${RESET} Archivo Swap de 2GB activado y persistido en /etc/fstab."
        fi
    fi

    # Verificar Espacio Libre en Disco (Mínimo 8 GB)
    FREE_DISK_GB=$(df -BG "${INSTALL_DIR}" | awk 'NR==2 {print $4}' | tr -d 'G')
    if [ "$FREE_DISK_GB" -lt 5 ]; then
        echo -e "    ${C_DANGER}✖ Espacio en disco insuficiente:${RESET} ${FREE_DISK_GB} GB libres (se requieren mínimo 8 GB)."
        exit 1
    fi
    echo -e "    ${C_SUCCESS}✔${RESET} Espacio disponible en almacenamiento: ${C_WHITE}${FREE_DISK_GB} GB libres${RESET}"

    # Verificar Conflictos de Puertos 80 y 443 en el Host
    local port80_proc=$(ss -tlnp 2>/dev/null | grep -E ':80\s' | awk '{print $NF}' | head -n1 || true)
    local port443_proc=$(ss -tlnp 2>/dev/null | grep -E ':443\s' | awk '{print $NF}' | head -n1 || true)

    if [[ -n "$port80_proc" && ! "$port80_proc" =~ "docker" ]]; then
        echo -e "    ${C_WARNING}⚠ El puerto 80 está en uso por un servicio externo:${RESET} ${port80_proc}"
        if systemctl is-active --quiet apache2 2>/dev/null; then
            echo -e "      ${C_SECONDARY}Deteniendo Apache local para liberar el puerto 80...${RESET}"
            systemctl stop apache2 >> "${LOG_FILE}" 2>&1 || true
            systemctl disable apache2 >> "${LOG_FILE}" 2>&1 || true
        elif systemctl is-active --quiet nginx 2>/dev/null; then
            echo -e "      ${C_SECONDARY}Deteniendo Nginx local para liberar el puerto 80...${RESET}"
            systemctl stop nginx >> "${LOG_FILE}" 2>&1 || true
            systemctl disable nginx >> "${LOG_FILE}" 2>&1 || true
        fi
    fi
}

# ==============================================================================
# 2. INSTALACIÓN DE DEPENDENCIAS DEL SISTEMA
# ==============================================================================
step_install_dependencies() {
    step_header "Aprovisionamiento de Paquetes Base del Sistema Operativo"

    export DEBIAN_FRONTEND=noninteractive
    run_task "Actualización de listas de paquetes apt" apt-get update -y

    run_task "Instalación de utilidades esenciales del kernel y red" apt-get install -y --no-install-recommends \
        curl wget git unzip jq openssl ca-certificates gnupg lsb-release ufw net-tools tar software-properties-common fail2ban
}

# ==============================================================================
# 3. INSTALACIÓN DEL MOTOR DOCKER CE & DOCKER COMPOSE PLUGIN
# ==============================================================================
step_install_docker() {
    step_header "Despliegue del Motor de Contenedores Docker CE Empresarial"

    if command -v docker &> /dev/null && docker compose version &> /dev/null; then
        echo -e "    ${C_SUCCESS}✔${RESET} Motor Docker ya instalado: ${C_WHITE}$(docker --version)${RESET}"
        echo -e "    ${C_SUCCESS}✔${RESET} Docker Compose Plugin disponible: ${C_WHITE}$(docker compose version)${RESET}"
    else
        run_task "Configuración del anillo de claves GPG de Docker" bash -c '
            install -m 0755 -d /etc/apt/keyrings
            curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
            chmod a+r /etc/apt/keyrings/docker.asc
        '

        run_task "Configuración del repositorio oficial de Docker" bash -c '
            CODENAME=$(. /etc/os-release && echo "$VERSION_CODENAME")
            [ -z "$CODENAME" ] && CODENAME="jammy"
            echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu ${CODENAME} stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null
            apt-get update -y
        '

        run_task "Instalación de Docker CE, CLI, Containerd y Compose" apt-get install -y --no-install-recommends \
            docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

        run_task "Habilitación e inicio del servicio systemd de Docker" systemctl enable --now docker
    fi
}

# ==============================================================================
# 4. CONFIGURACIÓN RIGUROSA DEL FIREWALL (UFW)
# ==============================================================================
step_configure_firewall() {
    step_header "Aseguramiento Perimetral del Servidor (Firewall UFW)"

    # Detectar puerto SSH activo de forma resiliente
    local ssh_port
    ssh_port=$(ss -tlnp 2>/dev/null | grep -E 'sshd|ssh' | awk '{print $4}' | awk -F':' '{print $NF}' | head -n1 || true)
    if [ -z "$ssh_port" ]; then
        ssh_port=$(grep -Ei '^\s*Port\s+' /etc/ssh/sshd_config 2>/dev/null | awk '{print $2}' | head -n1 || echo "22")
    fi
    [ -z "$ssh_port" ] && ssh_port="22"

    run_task "Preservación del puerto de administración remota SSH (${ssh_port}/tcp)" ufw allow "${ssh_port}/tcp" comment "SSH Management"

    run_task "Apertura de tráfico HTTP entrante (Puerto 80/tcp)" ufw allow 80/tcp comment "GeeksPOS Web HTTP"
    run_task "Apertura de tráfico HTTPS seguro entrante (Puerto 443/tcp)" ufw allow 443/tcp comment "GeeksPOS Web HTTPS"

    run_task "Establecimiento de políticas por defecto (Deny In / Allow Out)" bash -c '
        ufw default deny incoming >/dev/null 2>&1
        ufw default allow outgoing >/dev/null 2>&1
    '

    run_task "Activación del Firewall UFW en modo de producción" bash -c 'echo "y" | ufw enable >/dev/null 2>&1'

    echo -e "    ${C_SUCCESS}✔${RESET} Firewall activo: ${C_GREEN}${BOLD}Puertos 80 y 443 abiertos${RESET}. Puertos internos (3306, 5678, 8081) aislados."
}

# ==============================================================================
# 5. GENERACIÓN CRIPTOGRÁFICA DE SECRETOS Y ENTORNO (.env)
# ==============================================================================
step_setup_environment() {
    step_header "Generación Criptográfica de Secretos & Archivo de Entorno (.env)"

    # Detección de IP Pública del servidor
    SERVER_IP=$(curl -4 -s --max-time 3 ifconfig.me 2>/dev/null || curl -s --max-time 3 icanhazip.com 2>/dev/null || hostname -I | awk '{print $1}')
    [ -z "$SERVER_IP" ] && SERVER_IP="127.0.0.1"

    # Generación de contraseñas de alta entropía
    ADMIN_PASS=$(openssl rand -base64 16 | tr -dc 'a-zA-Z0-9!@#$%' | head -c 14)
    DB_APP_PASS=$(openssl rand -hex 16)
    DB_ROOT_PASS=$(openssl rand -hex 16)
    EVO_DB_PASS=$(openssl rand -hex 16)
    EVO_API_KEY=$(openssl rand -hex 16)
    ADMIN_EMAIL="admin@geekspos.com"
    ADMIN_USER="admin"

    cd "${INSTALL_DIR}"

    run_task "Escritura del archivo de variables de entorno de producción (.env)" bash -c "cat <<EOF > .env
# ==============================================================================
# GeeksPOS Enterprise Production Environment
# Generado automáticamente por install.sh el $(date '+%Y-%m-%d %H:%M:%S')
# ==============================================================================

# Puertos Web Expuestos (Firewall 80 y 443)
APP_PORT=80
APP_HTTPS_PORT=443

# Base de Datos MariaDB (Puerto interno 3307 solo en localhost)
DB_HOST=db
DB_NAME=geekscom_nicole
DB_USER=geekscom_nicole
DB_PASS=${DB_APP_PASS}
MYSQL_ROOT_PASSWORD=${DB_ROOT_PASS}
DB_PORT=3307

# GeeksIA Automatización / n8n (Puerto interno 5678 en localhost)
N8N_PORT=5678

# Evolution API WhatsApp (Puerto interno 8081 en localhost)
EVOLUTION_PORT=8081
EVOLUTION_DB_PASS=${EVO_DB_PASS}
EVOLUTION_API_KEY=${EVO_API_KEY}

# GeeksIA: cifrado de claves de IA (AES-256-GCM) y clave REST de plataforma para n8n
GEEKSIA_SECRET_KEY=$(openssl rand -base64 32)
GEEKSPOS_PLATFORM_API_KEY=gk_plat_$(openssl rand -hex 24)
GEEKSPOS_PLATFORM_ALLOW_PUBLIC=0
# API pública de n8n: completar desde el editor de n8n (Settings > n8n API)
N8N_API_KEY=
EOF
chmod 600 .env"

    run_task "Asignación de permisos de escritura a carpetas de caché y logs" bash -c '
        mkdir -p application/cache application/logs userfiles
        chmod -R 775 application/cache application/logs userfiles
        chown -R 33:33 application/cache application/logs userfiles 2>/dev/null || true
    '
}

# ==============================================================================
# 6. DESPLIEGUE Y SINCRONIZACIÓN DE CONTENEDORES DOCKER
# ==============================================================================
step_start_containers() {
    step_header "Construcción y Despliegue de Contenedores Docker"

    cd "${INSTALL_DIR}"

    run_task "Detención limpia de posibles instancias previas" docker compose down --remove-orphans

    run_task "Construcción y arranque de la flota de contenedores" docker compose up -d --build

    # Espera activa y con barra de salud a MariaDB
    local attempts=0
    local max_attempts=50
    tput civis 2>/dev/null || true
    printf "    ${C_SECONDARY}⠋${RESET}  Esperando sincronización de MariaDB e importación inicial de SQL..."

    until docker exec geekspos_db mysqladmin ping -u"geekscom_nicole" -p"${DB_APP_PASS}" --silent 2>/dev/null || \
          docker exec geekspos_db mysqladmin ping -uroot -p"${DB_ROOT_PASS}" --silent 2>/dev/null || \
          [ $attempts -ge $max_attempts ]; do
        sleep 2
        attempts=$((attempts + 1))
    done

    # Esperar a que la tabla geopos_users esté disponible
    local db_ready=0
    attempts=0
    until [ $db_ready -eq 1 ] || [ $attempts -ge 40 ]; do
        if docker exec geekspos_db mysql -u"geekscom_nicole" -p"${DB_APP_PASS}" "geekscom_nicole" -e "SELECT id FROM geopos_users LIMIT 1;" >/dev/null 2>&1; then
            db_ready=1
        else
            sleep 2
            attempts=$((attempts + 1))
        fi
    done
    tput cnorm 2>/dev/null || true

    if [ $db_ready -eq 1 ]; then
        printf "\r    ${C_SUCCESS}✔${RESET}  ${C_WHITE}%-58s${RESET} [ ${C_SUCCESS}${BOLD}OK${RESET} ]\n" "Base de datos MariaDB inicializada y sincronizada"
    else
        printf "\r    ${C_DANGER}✖${RESET}  ${C_WHITE}%-58s${RESET} [ ${C_DANGER}${BOLD}TIMEOUT${RESET} ]\n" "Base de datos demoró en inicializar"
        exit 1
    fi
}

# ==============================================================================
# 7. MIGRACIONES DE ESQUEMA & CREACIÓN DEL SUPERADMINISTRADOR
# ==============================================================================
step_init_admin_and_migrations() {
    step_header "Aplicación de Migraciones Multi-Rubro & Aprovisionamiento de Admin"

    run_task "Migración de esquemas de base de datos (tipo_flujo, horarios_semana)" docker exec -i geekspos_db mysql -u"geekscom_nicole" -p"${DB_APP_PASS}" "geekscom_nicole" << 'EOF'
-- 1. Agregar columna tipo_flujo en geopos_locations si no existe
SET @exist_loc := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='geekscom_nicole' AND TABLE_NAME='geopos_locations' AND COLUMN_NAME='tipo_flujo');
SET @query_loc := IF(@exist_loc = 0, 'ALTER TABLE geopos_locations ADD COLUMN tipo_flujo VARCHAR(50) DEFAULT "restaurante"', 'SELECT 1');
PREPARE stmt_loc FROM @query_loc; EXECUTE stmt_loc; DEALLOCATE PREPARE stmt_loc;

-- 2. Agregar columna tipo_flujo en geopos_whatsapp_config si no existe
SET @exist_wa_flujo := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='geekscom_nicole' AND TABLE_NAME='geopos_whatsapp_config' AND COLUMN_NAME='tipo_flujo');
SET @query_wa_flujo := IF(@exist_wa_flujo = 0, 'ALTER TABLE geopos_whatsapp_config ADD COLUMN tipo_flujo VARCHAR(50) DEFAULT "restaurante"', 'SELECT 1');
PREPARE stmt_wa_flujo FROM @query_wa_flujo; EXECUTE stmt_wa_flujo; DEALLOCATE PREPARE stmt_wa_flujo;

-- 3. Agregar columna horarios_semana en geopos_whatsapp_config si no existe
SET @exist_wa_horarios := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='geekscom_nicole' AND TABLE_NAME='geopos_whatsapp_config' AND COLUMN_NAME='horarios_semana');
SET @query_wa_horarios := IF(@exist_wa_horarios = 0, 'ALTER TABLE geopos_whatsapp_config ADD COLUMN horarios_semana TEXT NULL AFTER dias_atencion', 'SELECT 1');
PREPARE stmt_wa_horarios FROM @query_wa_horarios; EXECUTE stmt_wa_horarios; DEALLOCATE PREPARE stmt_wa_horarios;
EOF

    run_task "Creación de cuenta SuperAdmin con credencial criptográfica aleatoria" docker exec -i geekspos_db mysql -u"geekscom_nicole" -p"${DB_APP_PASS}" "geekscom_nicole" << EOF
-- Crear o sincronizar usuario SuperAdmin (id=11 o username='admin')
INSERT INTO geopos_users (id, email, pass, username, banned, roleid, loc, date_created)
VALUES (11, '${ADMIN_EMAIL}', SHA2(CONCAT(MD5(11), '${ADMIN_PASS}'), 256), '${ADMIN_USER}', 0, 5, 0, NOW())
ON DUPLICATE KEY UPDATE 
    email = '${ADMIN_EMAIL}',
    pass = SHA2(CONCAT(MD5(id), '${ADMIN_PASS}'), 256),
    username = '${ADMIN_USER}',
    banned = 0,
    roleid = 5,
    loc = 0;

-- Sincronizar geopos_employees para asegurar datos de perfil
INSERT INTO geopos_employees (id, username, name, email, roleid, loc, phone, address, city, region, country)
VALUES (11, '${ADMIN_USER}', 'Administrador General', '${ADMIN_EMAIL}', 5, 0, '0981000000', 'Asunción', 'Asunción', 'Central', 'Paraguay')
ON DUPLICATE KEY UPDATE 
    username = '${ADMIN_USER}',
    name = 'Administrador General',
    email = '${ADMIN_EMAIL}',
    roleid = 5,
    loc = 0;
EOF
}

# ==============================================================================
# 8. INSTALACIÓN DE LA HERRAMIENTA CLI GLOBAL "geekspos"
# ==============================================================================
step_install_cli_utility() {
    step_header "Instalación del Comando de Gestión Global 'geekspos'"

    local cli_bin="/usr/local/bin/geekspos"

    run_task "Creación de la utilidad CLI /usr/local/bin/geekspos" bash -c "cat << 'EOF' > ${cli_bin}
#!/usr/bin/env bash
# GeeksPOS Enterprise CLI Platform Manager
set -e
PROJECT_DIR=\"${INSTALL_DIR}\"

usage() {
    echo \"Uso: geekspos {status|logs|restart|stop|start|credentials|backup}\"
    echo \"\"
    echo \"Comandos disponibles:\"
    echo \"  status       Muestra el estado en vivo de los contenedores y recursos\"
    echo \"  logs [srv]   Visualiza registros en vivo (ej: geekspos logs app)\"
    echo \"  restart      Reinicia todos los servicios del sistema\"
    echo \"  stop         Detiene todos los contenedores\"
    echo \"  start        Inicia todos los contenedores\"
    echo \"  credentials  Muestra las credenciales de acceso de forma segura\"
    echo \"  backup       Genera una copia de seguridad instantánea de la base de datos\"
    exit 1
}

[ \$# -eq 0 ] && usage

case \"\$1\" in
    status)
        cd \"\$PROJECT_DIR\"
        echo \"=== ESTADO DE CONTENEDORES GEEKSPOS ===\"
        docker compose ps
        echo \"\"
        echo \"=== ESTADO DEL FIREWALL UFW ===\"
        ufw status verbose | grep -E 'Status|80/tcp|443/tcp|22/tcp' || ufw status
        ;;
    logs)
        cd \"\$PROJECT_DIR\"
        shift
        docker compose logs -f --tail=100 \"\$@\"
        ;;
    restart)
        cd \"\$PROJECT_DIR\"
        docker compose restart
        echo \"✔ Servicios reiniciados correctamente.\"
        ;;
    stop)
        cd \"\$PROJECT_DIR\"
        docker compose stop
        echo \"✔ Servicios detenidos.\"
        ;;
    start)
        cd \"\$PROJECT_DIR\"
        docker compose up -d
        echo \"✔ Servicios iniciados.\"
        ;;
    credentials)
        if [ -f /root/geekspos_credentials.txt ]; then
            cat /root/geekspos_credentials.txt
        else
            cat \"\$PROJECT_DIR/credentials.txt\"
        fi
        ;;
    backup)
        cd \"\$PROJECT_DIR\"
        BACKUP_DIR=\"\$PROJECT_DIR/backups\"
        mkdir -p \"\$BACKUP_DIR\"
        FILE=\"\$BACKUP_DIR/backup_\$(date +%Y%m%d_%H%M%S).sql.gz\"
        . .env 2>/dev/null || true
        docker exec geekspos_db mysqldump -u\"geekscom_nicole\" -p\"\${DB_PASS}\" geekscom_nicole | gzip > \"\$FILE\"
        echo \"✔ Backup generado exitosamente en: \$FILE\"
        ;;
    *)
        usage
        ;;
esac
EOF
chmod +x ${cli_bin}"
}

# ==============================================================================
# DASHBOARD EJECUTIVO FINAL & SALVAGUARDA DE CREDENCIALES
# ==============================================================================
render_final_dashboard() {
    local creds_file="/root/geekspos_credentials.txt"
    local local_creds="${INSTALL_DIR}/credentials.txt"

    cat <<EOF > "${creds_file}"
================================================================================
                    GEEKSPOS ENTERPRISE - DATOS DE ACCESO
================================================================================
Fecha de Despliegue:   $(date '+%Y-%m-%d %H:%M:%S')
Servidor Host IP:      ${SERVER_IP}

ACCESO A LA APLICACIÓN WEB:
  URL HTTP (Puerto 80):    http://${SERVER_IP}/
  URL HTTPS (Puerto 443):  https://${SERVER_IP}/ (SSL Autofirmado Activo)

CREDENCIALES DEL SUPERADMINISTRADOR:
  Panel de Control:        http://${SERVER_IP}/
  Usuario de Acceso:       ${ADMIN_USER}
  Correo Electrónico:      ${ADMIN_EMAIL}
  Contraseña Generada:     ${ADMIN_PASS}

BASE DE DATOS (MARIADB INTERNO):
  Host / Puerto:           127.0.0.1:3307 (Solo acceso local - Bloqueado al exterior)
  Base de Datos:           geekscom_nicole
  Usuario:                 geekscom_nicole
  Contraseña Usuario:      ${DB_APP_PASS}
  Contraseña Root:         ${DB_ROOT_PASS}

GEEKSIA / EVOLUTION API (WHATSAPP):
  API Key WhatsApp:        ${EVO_API_KEY}
  Puerto Interno:          8081 (Solo acceso local)

ESTADO DE SEGURIDAD (FIREWALL UFW):
  Puertos Abiertos:        80/tcp (HTTP), 443/tcp (HTTPS), SSH activo.
  Resto de Puertos:        BLOQUEADOS CONTRA ACCESOS EXTERNOS.
================================================================================
EOF

    chmod 600 "${creds_file}"
    cp "${creds_file}" "${local_creds}"
    chmod 600 "${local_creds}"

    echo ""
    echo -e "${C_PRIMARY}${BOLD}╭─────────────────────────────────────────────────────────────────────────────╮${RESET}"
    echo -e "${C_PRIMARY}${BOLD}│${C_SUCCESS}${BOLD}            🎉 ¡INSTALACIÓN EMPRESARIAL COMPLETADA EXITOSAMENTE!             ${C_PRIMARY}${BOLD}│${RESET}"
    echo -e "${C_PRIMARY}${BOLD}╰─────────────────────────────────────────────────────────────────────────────╯${RESET}"
    echo ""
    echo -e "  ${C_WHITE}${BOLD}ACCESO WEB PRINCIPAL:${RESET}"
    echo -e "    ${C_SECONDARY}🌐 Enlace HTTP:${RESET}       ${C_WHITE}${BOLD}http://${SERVER_IP}/${RESET}"
    echo -e "    ${C_SECONDARY}🔒 Enlace HTTPS:${RESET}      ${C_WHITE}${BOLD}https://${SERVER_IP}/${RESET}"
    echo ""
    echo -e "  ${C_WHITE}${BOLD}CREDENCIALES DE SUPERADMINISTRADOR:${RESET}"
    echo -e "    ${C_GRAY}Usuario:${RESET}              ${C_WHITE}${BOLD}${ADMIN_USER}${RESET}  (o ${C_GRAY}${ADMIN_EMAIL}${RESET})"
    echo -e "    ${C_GRAY}Contraseña:${RESET}           ${C_SUCCESS}${BOLD}${ADMIN_PASS}${RESET}"
    echo ""
    echo -e "  ${C_WHITE}${BOLD}POLÍTICA DE RED & FIREWALL (UFW):${RESET}"
    echo -e "    ${C_SUCCESS}✔${RESET} Puertos Públicos:    ${C_WHITE}80 (HTTP)${RESET} y ${C_WHITE}443 (HTTPS)${RESET} habilitados."
    echo -e "    ${C_SUCCESS}✔${RESET} Puertos Internos:    ${C_WHITE}3307 (DB), 5678 (n8n), 8081 (Evolution)${RESET} aislados a localhost."
    echo ""
    echo -e "  ${C_WHITE}${BOLD}RESPALDO DE CREDENCIALES SEGURO (chmod 600):${RESET}"
    echo -e "    ${C_PURPLE}📂 Archivo Root:${RESET}      ${C_WHITE}/root/geekspos_credentials.txt${RESET}"
    echo -e "    ${C_PURPLE}📂 Archivo Local:${RESET}     ${C_WHITE}${INSTALL_DIR}/credentials.txt${RESET}"
    echo ""
    echo -e "  ${C_WHITE}${BOLD}HERRAMIENTA CLI DISPONIBLE:${RESET}"
    echo -e "    Ejecute en cualquier terminal: ${C_PRIMARY}${BOLD}geekspos status${RESET}  o  ${C_PRIMARY}${BOLD}geekspos logs${RESET}"
    echo ""
    echo -e "${C_GRAY}───────────────────────────────────────────────────────────────────────────────${RESET}"
    echo -e "  ${C_SUCCESS}${BOLD}GeeksPOS & GeeksIA están listos para operar en producción.${RESET}"
    echo ""
}

# Flujo Principal de Ejecución
main() {
    render_banner
    step_preflight_checks
    step_install_dependencies
    step_install_docker
    step_configure_firewall
    step_setup_environment
    step_start_containers
    step_init_admin_and_migrations
    step_install_cli_utility
    render_final_dashboard
}

main "$@"
