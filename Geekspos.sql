-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 20, 2026 at 10:07 PM
-- Server version: 10.11.19-MariaDB-cll-lve
-- PHP Version: 8.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `geekscom_nicole`
--

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ci_sessions`
--

INSERT INTO `ci_sessions` (`id`, `ip_address`, `timestamp`, `data`) VALUES
('01via2jqaggnevv6ivddotkq6jo594s7', '169.58.150.16', 1792544621, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323534343632313b),
('0f3nuk67rk9hforq5usp989m0297rgc7', '169.58.150.16', 1792220745, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232303734353b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('0igftpipa2dqjfc3t0k6nllhqc5ojp7s', '169.58.150.16', 1792223851, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232333835303b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('13i4eeks2oprmphc8hq5bjj80b3fv0fh', '169.58.150.16', 1792217085, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231373038353b),
('1lbno0hp8djp6voqkf4tuqu5cvd48ird', '169.58.150.16', 1792544600, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323534343630303b),
('1va7oo4e58abj8jks8skphsbti74so77', '169.58.150.16', 1792221158, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232313135383b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('2chmosvvg6r121adge230d068n4ub9f0', '169.58.150.16', 1792221513, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232313531333b),
('2o9vm7sha2r0rk3btvt7jvp1n83bu67f', '169.58.150.16', 1792544613, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323534343631333b),
('333egb5utdaqfm07s44ldm54ligro06l', '186.12.181.81', 1792220345, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232303334353b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('3q4s1r0cfbqst7fn8l6c5cvll34joael', '169.58.150.16', 1792223719, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232333731393b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('4332m986h7e40kcgreb0q8sphb7vq4pv', '186.12.181.81', 1792224495, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232343436393b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('47viobqb6ch8er58d6dsp1rvd7erce1a', '169.58.150.16', 1792223879, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232333837383b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('66mjiats2lrru2kv4uqvaljfbtedlafp', '169.58.150.16', 1792219452, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231393435323b),
('6s1tgeb8ska7hh806f19koo19kienjkr', '169.58.150.16', 1792222665, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232323636343b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('7h32htamfstmgk61qf96gjpq4lmed7mc', '169.58.150.16', 1792220772, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232303737303b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('7uud48rkfrve3pie0dvodki0lpn8d4g5', '186.12.181.81', 1792221874, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232313837343b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('8an2auj5cuo31cp01pg5kuufhql2acod', '169.58.150.16', 1792223900, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232333839393b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('8djv5gd1hb5ck1ekgqucr6tjojbkd2lq', '169.58.150.16', 1792221200, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232313139393b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('8ubid8fvsuid9lnklpik6n4tdbrh9jee', '186.12.181.81', 1792223327, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232333332373b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('9h4svugdtdjd1qi82bo209oo0sltvfob', '169.58.150.16', 1792544620, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323534343632303b),
('9hhhk59lt2licubospoc2iuneharqs67', '169.58.150.16', 1792218875, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231383837353b),
('ak5fp30qifspq17machg0omi885d8sct', '169.58.150.16', 1792222789, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232323738393b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('bodq164j8nljufhrk1qr6cihb0u3lob3', '169.58.150.16', 1792219453, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231393435333b),
('c9fg2dlnjfedto7ojuedrk4jqilr4sus', '186.12.181.81', 1792220705, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232303730353b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('d58dbor3enar4d8ehargb3eedc31h1r1', '169.58.150.16', 1792222934, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232323932363b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('do0r8upmktg4jqv0a662hiapm90aq3ra', '169.58.150.16', 1792218875, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231383837353b),
('e51lm39rpahhneuj3id0p741vi5tojd7', '169.58.150.16', 1792544600, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323534343630303b),
('e77n6l4rgcijmhvuep9rdar0liu0ak4o', '169.58.150.16', 1792223335, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232333333333b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('e83452h6bev75oo617tkl98663upgd9l', '169.58.150.16', 1792218876, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231383837363b),
('es2kdf5ev94oqm0s0iihlms0t1hs2dp3', '169.58.150.16', 1792222834, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232323833343b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('evnienrh0u1f5oi9kf788kupr1knam79', '186.12.181.81', 1792221351, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232313335313b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('ffh07du36lfdj56oies2c8nsc6jmmmcm', '169.58.150.16', 1792221513, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232313531333b),
('fh5lp8u97mfjjsk9ukfr4qvmcd0oneip', '169.58.150.16', 1792544600, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323534343539393b),
('fttid55hb5kn85mkmccnetngg3gaboo7', '186.12.181.81', 1792544663, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323534343631363b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('ghe4i9pldppcsau47a0etr5obimfl4ok', '169.58.150.16', 1792219787, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231393738363b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('h8gl8jrvj944ns44p1rl83k2vsijrkv6', '169.58.150.16', 1792544621, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323534343632303b),
('hk4m5fa57tvrge5naa8g50t3ngq19qca', '169.58.150.16', 1792217085, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231373038353b),
('ht1q76c2ra61t22fl70kuea42scc86do', '169.58.150.16', 1792222751, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232323735313b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('i59bejampeeufbrfeqieekt04211a673', '169.58.150.16', 1792544613, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323534343631333b),
('ic0p1g9bq03k2lap7lqir0tfugu04g82', '169.58.150.16', 1792219768, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231393736383b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('j0jjf3k1c4k5n3k31gm80g6582b1m8gu', '169.58.150.16', 1792223201, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232333230303b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('jftrs8i4a2487a7m7vuk408kd9nr5gbt', '169.58.150.16', 1792223022, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232333032313b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('kjf4olgu01463rasofn6ieprofb1r6tv', '169.58.150.16', 1792221993, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232313939333b),
('kunojimakkaotpuhhui3gnml3uc4cik9', '186.12.181.81', 1792223015, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232333031353b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('l74rpv7s4vhjkpk0vgvngjrl3a1djsou', '169.58.150.16', 1792222852, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232323835323b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('l7t8dnl88fgq49t1ec97o0kuuqlqd63s', '169.58.150.16', 1792221175, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232313137353b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('l8l8k7eofdk0c2a8mlij9etkvklgh57q', '186.12.181.81', 1792224469, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232343436393b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('lr87vq7qlgfbh5aa0bigr2l844buu4bo', '186.12.181.81', 1792219787, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231393738373b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('m42h04l8830irdgv1hqsm2hfa9k1nokp', '169.58.150.16', 1792544603, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323534343630333b),
('miifhr2duseqcanc2a9cog625aekjn0h', '169.58.150.16', 1792222815, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232323831333b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('ml80rj0hi8btutk50omg19t5oed8nfm2', '169.58.150.16', 1792221187, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232313138363b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('nlgcnh897ap0mbndcrqcvom3v52c6k2u', '186.12.181.81', 1792217887, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231373838373b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('o6piikk21eo59quhdmeoa34leq79ve7h', '186.12.181.81', 1792219091, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231393039313b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('ofmc7qdkrvld669k4u0u9rij731kutui', '169.58.150.16', 1792218899, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231383839393b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('ogl9jli7u5ns6u1cif38u2415pio4o04', '169.58.150.16', 1792223974, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232333937333b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('okfrbb5t8j9le9ti0ugijq0tcqucle7q', '186.12.181.81', 1792223643, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232333634333b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('qr0r0pmm9949chs7i7e9p2j88dh4f1o9', '169.58.150.16', 1792223355, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232333335343b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('r7510fve7l7v1kqmvs13iupbog2a1fh8', '169.58.150.16', 1792221513, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232313531333b),
('rd8qe4g50ore0410ka5bke40as7tho80', '186.12.181.81', 1792219396, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231393339363b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('rmmp7mlfqugo9nrknfb46t4it3b78s6m', '169.58.150.16', 1792222886, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232323838353b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('rrvmlkh1ututmnd8l4p1hervn3f21jie', '169.58.150.16', 1792219453, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231393435333b),
('s0p4qdbn4eb88aa7qnve9soel2at1a82', '169.58.150.16', 1792221993, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232313939333b),
('scb586kpjvmdbe31vkhh69d2pqmpomqv', '169.58.150.16', 1792544611, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323534343631313b),
('t67kkbpnfrq8nhpemt46tpsg1g2f16c1', '169.58.150.16', 1792219453, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231393435333b),
('tbtdala7r9ejgi13ee6utv3bed2c9esr', '186.12.181.81', 1792222533, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232323533333b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('td7kb38g1enq726aopjcpsh3g18djarl', '169.58.150.16', 1792217085, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231373038353b),
('tfifp26g5j5arnje5k4oiu2k2s6ouajq', '186.12.181.81', 1792221045, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323232313034353b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('tg7ea7vfp1grb1alructphtid90gletk', '169.58.150.16', 1792217099, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231373039393b),
('u19ko2li5m4huua3g5o5f660nk8arg3s', '169.58.150.16', 1792218875, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231383837353b),
('u77qt6754aki5cneoj376n51d0sogspq', '169.58.150.16', 1792219476, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323231393437353b69647c733a323a223131223b757365726e616d657c733a353a2261646d696e223b656d61696c7c733a32313a226b616d626972653230303740676d61696c2e636f6d223b735f726f6c657c733a333a22725f35223b6c6f67676564696e7c623a313b),
('uc0ptfajus2f8o69lhgtlgr9u4hg6pgr', '169.58.150.16', 1792544604, 0x5f5f63695f6c6173745f726567656e65726174657c693a313739323534343630343b);

-- --------------------------------------------------------

--
-- Table structure for table `geopos_accounts`
--

CREATE TABLE `geopos_accounts` (
  `id` int(5) NOT NULL,
  `acn` varchar(35) NOT NULL,
  `holder` varchar(100) NOT NULL,
  `adate` datetime NOT NULL,
  `lastbal` decimal(16,2) DEFAULT 0.00,
  `code` varchar(30) DEFAULT NULL,
  `loc` int(4) DEFAULT NULL,
  `account_type` enum('Assets','Expenses','Income','Liabilities','Equity','Basic') NOT NULL DEFAULT 'Basic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_accounts`
--

INSERT INTO `geopos_accounts` (`id`, `acn`, `holder`, `adate`, `lastbal`, `code`, `loc`, `account_type`) VALUES
(1, '123456', 'Sales Account', '2018-01-01 00:00:00', 339000.00, 'Default Sales Account', 0, 'Basic');

-- --------------------------------------------------------

--
-- Table structure for table `geopos_attendance`
--

CREATE TABLE `geopos_attendance` (
  `id` int(11) NOT NULL,
  `emp` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `adate` date NOT NULL,
  `tfrom` time NOT NULL,
  `tto` time NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `actual_hours` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_attendance`
--

INSERT INTO `geopos_attendance` (`id`, `emp`, `created`, `adate`, `tfrom`, `tto`, `note`, `actual_hours`) VALUES
(1, 11, '2026-10-17 09:02:57', '2026-10-17', '07:02:54', '07:02:57', 'Self Attendance', 3);

-- --------------------------------------------------------

--
-- Table structure for table `geopos_bank_ac`
--

CREATE TABLE `geopos_bank_ac` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `bank` varchar(50) NOT NULL,
  `acn` varchar(50) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `note` varchar(2000) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `enable` enum('Yes','No') NOT NULL DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_config`
--

CREATE TABLE `geopos_config` (
  `id` int(11) NOT NULL,
  `type` int(3) NOT NULL,
  `val1` varchar(50) NOT NULL,
  `val2` varchar(200) NOT NULL,
  `val3` varchar(100) NOT NULL,
  `val4` varchar(100) NOT NULL,
  `rid` int(11) NOT NULL,
  `other` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_currencies`
--

CREATE TABLE `geopos_currencies` (
  `id` int(4) NOT NULL,
  `code` varchar(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `symbol` varchar(3) DEFAULT NULL,
  `rate` decimal(10,5) NOT NULL,
  `thous` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `dpoint` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `decim` int(2) NOT NULL,
  `cpos` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_currencies`
--

INSERT INTO `geopos_currencies` (`id`, `code`, `symbol`, `rate`, `thous`, `dpoint`, `decim`, `cpos`) VALUES
(1, 'Gs.', 'Gs.', 0.00000, '.', '.', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `geopos_customers`
--

CREATE TABLE `geopos_customers` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `region` varchar(30) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `postbox` varchar(20) DEFAULT NULL,
  `email` varchar(60) DEFAULT NULL,
  `picture` varchar(100) NOT NULL DEFAULT 'example.png',
  `gid` int(5) NOT NULL DEFAULT 1,
  `company` varchar(100) DEFAULT NULL,
  `taxid` varchar(100) DEFAULT NULL,
  `name_s` varchar(100) DEFAULT NULL,
  `phone_s` varchar(100) DEFAULT NULL,
  `email_s` varchar(100) DEFAULT NULL,
  `address_s` varchar(100) DEFAULT NULL,
  `city_s` varchar(100) DEFAULT NULL,
  `region_s` varchar(100) DEFAULT NULL,
  `country_s` varchar(100) DEFAULT NULL,
  `postbox_s` varchar(100) DEFAULT NULL,
  `balance` decimal(16,2) DEFAULT 0.00,
  `loc` int(11) DEFAULT 0,
  `docid` varchar(255) DEFAULT NULL,
  `custom1` varchar(255) DEFAULT NULL,
  `discount_c` decimal(16,2) DEFAULT NULL,
  `reg_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_customers`
--

INSERT INTO `geopos_customers` (`id`, `name`, `phone`, `address`, `city`, `region`, `country`, `postbox`, `email`, `picture`, `gid`, `company`, `taxid`, `name_s`, `phone_s`, `email_s`, `address_s`, `city_s`, `region_s`, `country_s`, `postbox_s`, `balance`, `loc`, `docid`, `custom1`, `discount_c`, `reg_date`) VALUES
(1, 'Walk-in Client', '0987654321', NULL, NULL, NULL, NULL, NULL, 'example@example.com', 'example.png', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `geopos_custom_data`
--

CREATE TABLE `geopos_custom_data` (
  `id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `module` int(3) NOT NULL,
  `data` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_custom_fields`
--

CREATE TABLE `geopos_custom_fields` (
  `id` int(11) NOT NULL,
  `f_module` int(3) NOT NULL,
  `f_type` varchar(30) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `placeholder` varchar(30) DEFAULT NULL,
  `value_data` text NOT NULL,
  `f_view` int(2) NOT NULL,
  `other` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_cust_group`
--

CREATE TABLE `geopos_cust_group` (
  `id` int(10) NOT NULL,
  `title` varchar(60) DEFAULT NULL,
  `summary` varchar(250) DEFAULT NULL,
  `disc_rate` decimal(9,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_cust_group`
--

INSERT INTO `geopos_cust_group` (`id`, `title`, `summary`, `disc_rate`) VALUES
(1, 'Default Group', 'Default Group', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `geopos_documents`
--

CREATE TABLE `geopos_documents` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `filename` varchar(50) DEFAULT NULL,
  `cdate` date NOT NULL,
  `permission` int(1) DEFAULT NULL,
  `cid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `rid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_draft`
--

CREATE TABLE `geopos_draft` (
  `id` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `invoicedate` date NOT NULL,
  `invoiceduedate` date NOT NULL,
  `subtotal` decimal(16,2) DEFAULT 0.00,
  `shipping` decimal(16,2) DEFAULT 0.00,
  `ship_tax` decimal(16,2) DEFAULT NULL,
  `ship_tax_type` enum('incl','excl','off') DEFAULT 'off',
  `discount` decimal(16,2) DEFAULT 0.00,
  `tax` decimal(16,2) DEFAULT 0.00,
  `total` decimal(16,2) DEFAULT 0.00,
  `pmethod` varchar(14) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `status` enum('paid','due','canceled','partial') NOT NULL DEFAULT 'due',
  `csd` int(5) NOT NULL DEFAULT 0,
  `eid` int(4) NOT NULL,
  `pamnt` decimal(16,2) DEFAULT 0.00,
  `items` decimal(10,2) NOT NULL,
  `taxstatus` enum('yes','no','cgst','igst') NOT NULL DEFAULT 'yes',
  `discstatus` tinyint(1) NOT NULL,
  `format_discount` enum('%','flat','bflat','b_p') NOT NULL DEFAULT '%',
  `refer` varchar(20) DEFAULT NULL,
  `term` int(3) NOT NULL,
  `multi` int(4) DEFAULT NULL,
  `i_class` int(1) NOT NULL DEFAULT 0,
  `loc` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_draft_items`
--

CREATE TABLE `geopos_draft_items` (
  `id` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT 0,
  `product` varchar(255) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `qty` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price` decimal(16,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(16,2) DEFAULT 0.00,
  `discount` decimal(16,2) DEFAULT 0.00,
  `subtotal` decimal(16,2) DEFAULT 0.00,
  `totaltax` decimal(16,2) DEFAULT 0.00,
  `totaldiscount` decimal(16,2) DEFAULT 0.00,
  `product_des` text DEFAULT NULL,
  `i_class` int(1) NOT NULL DEFAULT 0,
  `unit` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_employees`
--

CREATE TABLE `geopos_employees` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `name` varchar(50) NOT NULL,
  `address` varchar(100) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `region` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `postbox` varchar(20) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `phonealt` varchar(15) DEFAULT NULL,
  `picture` varchar(50) NOT NULL DEFAULT 'example.png',
  `sign` varchar(100) DEFAULT 'sign.png',
  `joindate` datetime NOT NULL DEFAULT current_timestamp(),
  `dept` int(11) DEFAULT NULL,
  `degis` int(11) DEFAULT NULL,
  `salary` decimal(16,2) DEFAULT 0.00,
  `clock` int(1) DEFAULT NULL,
  `clockin` int(11) DEFAULT NULL,
  `clockout` int(11) DEFAULT NULL,
  `c_rate` decimal(16,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_employees`
--

INSERT INTO `geopos_employees` (`id`, `username`, `name`, `address`, `city`, `region`, `country`, `postbox`, `phone`, `phonealt`, `picture`, `sign`, `joindate`, `dept`, `degis`, `salary`, `clock`, `clockin`, `clockout`, `c_rate`) VALUES
(11, 'admin', 'BusinessOwner', 'Test Street', 'Test City', 'Test Region', 'Test Country', '123456', '12345678', '0', 'example.png', 'sign.png', '2026-10-17 08:04:35', NULL, NULL, 0.00, 0, 0, 1792220577, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `geopos_events`
--

CREATE TABLE `geopos_events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#3a87ad',
  `start` datetime NOT NULL,
  `end` datetime DEFAULT NULL,
  `allDay` varchar(50) NOT NULL DEFAULT 'true',
  `rel` int(2) NOT NULL DEFAULT 0,
  `rid` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_gateways`
--

CREATE TABLE `geopos_gateways` (
  `id` int(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `enable` enum('Yes','No') NOT NULL,
  `key1` varchar(255) NOT NULL,
  `key2` varchar(255) DEFAULT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `dev_mode` enum('true','false') NOT NULL,
  `ord` int(5) NOT NULL,
  `surcharge` decimal(16,2) NOT NULL,
  `extra` varchar(40) NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `geopos_gateways`
--

INSERT INTO `geopos_gateways` (`id`, `name`, `enable`, `key1`, `key2`, `currency`, `dev_mode`, `ord`, `surcharge`, `extra`) VALUES
(1, 'Stripe', 'Yes', 'sk_test_secratekey', 'stripe_public_key', 'USD', 'true', 1, 0.00, 'none'),
(2, 'Authorize.Net', 'Yes', 'TRANSACTIONKEY', 'LOGINID', 'AUD', 'true', 2, 0.00, 'none'),
(3, 'Pin Payments', 'Yes', 'TEST', 'none', 'AUD', 'true', 3, 0.00, 'none'),
(4, 'PayPal', 'Yes', 'MyPayPalClientId', 'MyPayPalSecret', 'USD', 'true', 4, 0.00, 'none'),
(5, 'SecurePay', 'Yes', 'ABC0001', 'abc123', 'AUD', 'true', 5, 0.00, 'none'),
(6, '2Checkout', 'Yes', 'Publishable Key', 'Private Key', 'USD', 'true', 6, 0.00, 'seller_id'),
(7, 'PayU Money', 'Yes', 'MERCHANT_KEY', 'MERCHANT_SALT', 'USD', 'true', 7, 0.00, 'none'),
(8, 'RazorPay', 'Yes', 'Key Id', 'Key Secret', 'INR', 'true', 8, 0.00, 'none');

-- --------------------------------------------------------

--
-- Table structure for table `geopos_goals`
--

CREATE TABLE `geopos_goals` (
  `id` int(1) NOT NULL,
  `income` bigint(20) NOT NULL,
  `expense` bigint(20) NOT NULL,
  `sales` bigint(20) NOT NULL,
  `netincome` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_goals`
--

INSERT INTO `geopos_goals` (`id`, `income`, `expense`, `sales`, `netincome`) VALUES
(1, 999999, 999999, 999999, 999999);

-- --------------------------------------------------------

--
-- Table structure for table `geopos_hrm`
--

CREATE TABLE `geopos_hrm` (
  `id` int(11) NOT NULL,
  `typ` int(2) NOT NULL,
  `rid` int(11) NOT NULL,
  `val1` varchar(255) DEFAULT NULL,
  `val2` varchar(255) DEFAULT NULL,
  `val3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_invoices`
--

CREATE TABLE `geopos_invoices` (
  `id` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `invoicedate` date NOT NULL,
  `invoiceduedate` date NOT NULL,
  `subtotal` decimal(16,2) DEFAULT 0.00,
  `shipping` decimal(16,2) DEFAULT 0.00,
  `ship_tax` decimal(16,2) DEFAULT NULL,
  `ship_tax_type` enum('incl','excl','off') DEFAULT 'off',
  `discount` decimal(16,2) DEFAULT 0.00,
  `discount_rate` decimal(10,2) DEFAULT 0.00,
  `tax` decimal(16,2) DEFAULT 0.00,
  `total` decimal(16,2) DEFAULT 0.00,
  `pmethod` varchar(14) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `status` enum('paid','due','canceled','partial') NOT NULL DEFAULT 'due',
  `csd` int(5) NOT NULL DEFAULT 0,
  `eid` int(4) NOT NULL,
  `pamnt` decimal(16,2) DEFAULT 0.00,
  `items` decimal(10,2) NOT NULL,
  `taxstatus` enum('yes','no','incl','cgst','igst') NOT NULL DEFAULT 'yes',
  `discstatus` tinyint(1) NOT NULL,
  `format_discount` enum('%','flat','b_p','bflat') NOT NULL DEFAULT '%',
  `refer` varchar(20) DEFAULT NULL,
  `order_name` varchar(100) DEFAULT NULL,
  `order_type` varchar(20) DEFAULT NULL,
  `card_txn` varchar(50) DEFAULT NULL,
  `term` int(3) NOT NULL,
  `multi` int(4) DEFAULT NULL,
  `i_class` int(1) NOT NULL DEFAULT 0,
  `loc` int(4) NOT NULL,
  `r_time` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_invoices`
--

INSERT INTO `geopos_invoices` (`id`, `tid`, `invoicedate`, `invoiceduedate`, `subtotal`, `shipping`, `ship_tax`, `ship_tax_type`, `discount`, `discount_rate`, `tax`, `total`, `pmethod`, `notes`, `status`, `csd`, `eid`, `pamnt`, `items`, `taxstatus`, `discstatus`, `format_discount`, `refer`, `order_name`, `order_type`, `card_txn`, `term`, `multi`, `i_class`, `loc`, `r_time`) VALUES
(5, 1001, '2026-10-17', '2026-10-17', 89000.00, 0.00, 0.00, 'incl', 0.00, 0.00, 0.00, 89000.00, 'Cash', '', 'paid', 1, 11, 89000.00, 1.00, 'yes', 1, '%', '', '', 'Pasa a buscar', '', 1, NULL, 1, 0, NULL),
(7, 1002, '2026-10-17', '2026-10-17', 90000.00, 0.00, 0.00, 'off', 0.00, 0.00, 0.00, 90000.00, 'Cash', NULL, 'paid', 1, 11, 90000.00, 1.00, 'no', 0, '', '', 'Prueba', 'En local', '', 0, 0, 1, 0, NULL),
(8, 1003, '2026-10-17', '2026-10-17', 35000.00, 0.00, 0.00, 'incl', 0.00, 0.00, 0.00, 35000.00, 'Cash', '', 'paid', 1, 11, 35000.00, 1.00, 'yes', 1, '%', '', '', 'En local', '', 1, NULL, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `geopos_invoice_items`
--

CREATE TABLE `geopos_invoice_items` (
  `id` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT 0,
  `product` varchar(255) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `qty` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price` decimal(16,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(16,2) DEFAULT 0.00,
  `discount` decimal(16,2) DEFAULT 0.00,
  `subtotal` decimal(16,2) DEFAULT 0.00,
  `totaltax` decimal(16,2) DEFAULT 0.00,
  `totaldiscount` decimal(16,2) DEFAULT 0.00,
  `product_des` text DEFAULT NULL,
  `i_class` int(1) NOT NULL DEFAULT 0,
  `unit` varchar(5) DEFAULT NULL,
  `serial` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_invoice_items`
--

INSERT INTO `geopos_invoice_items` (`id`, `tid`, `pid`, `product`, `code`, `qty`, `price`, `tax`, `discount`, `subtotal`, `totaltax`, `totaldiscount`, `product_des`, `i_class`, `unit`, `serial`) VALUES
(1, 3, 0, 'Producto Test', 'TST01', 1.00, 10000.00, 0.00, 0.00, 10000.00, 0.00, 0.00, '', 1, 'Unida', ''),
(2, 4, 1, 'Producto Test', 'TST01', 1.00, 25000.00, 0.00, 0.00, 25000.00, 0.00, 0.00, '', 1, 'Und', ''),
(3, 5, 26, 'Pizza Gigante Familiar 4 sabores-PROMO-FAMILIAR', 'PROMO-FAMILIAR', 1.00, 89000.00, 0.00, 0.00, 89000.00, 0.00, 0.00, NULL, 1, 'Und', ''),
(4, 6, 10, 'Pizza Mozzarella Gigante', 'PIZ-MOZZ-G', 1.00, 90000.00, 0.00, 0.00, 90000.00, 0.00, 0.00, '', 1, 'Und', ''),
(5, 7, 10, 'Pizza Mozzarella Gigante', 'PIZ-MOZZ-G', 1.00, 90000.00, 0.00, 0.00, 90000.00, 0.00, 0.00, '', 1, 'Und', ''),
(6, 8, 2, 'Bronx Combo (Doble)-BURG-BRONX', 'BURG-BRONX', 1.00, 35000.00, 0.00, 0.00, 35000.00, 0.00, 0.00, NULL, 1, 'Und', '');

-- --------------------------------------------------------

--
-- Table structure for table `geopos_locations`
--

CREATE TABLE `geopos_locations` (
  `id` int(3) NOT NULL,
  `cname` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `region` varchar(40) DEFAULT NULL,
  `country` varchar(40) DEFAULT NULL,
  `postbox` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `taxid` varchar(40) DEFAULT NULL,
  `logo` varchar(50) DEFAULT 'logo.png',
  `cur` int(4) NOT NULL,
  `ware` int(11) DEFAULT 0,
  `ext` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_log`
--

CREATE TABLE `geopos_log` (
  `id` int(11) NOT NULL,
  `note` mediumtext NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `user` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_log`
--

INSERT INTO `geopos_log` (`id`, `note`, `created`, `user`) VALUES
(1, '[Logged In] kambire2007@gmail.com', '2026-10-17 06:07:26', ''),
(2, '[Logged In] kambire2007@gmail.com', '2026-10-17 06:34:59', ''),
(3, '[Logged In] kambire2007@gmail.com', '2026-10-17 06:44:35', ''),
(4, '[Logged In] kambire2007@gmail.com', '2026-10-17 06:49:28', ''),
(5, '[Logged In] kambire2007@gmail.com', '2026-10-17 06:49:46', ''),
(6, '[Employee ClockIn]  ID 11', '2026-10-17 07:02:54', 'admin'),
(7, '[Employee ClockOut]  ID 11', '2026-10-17 07:02:57', 'admin'),
(8, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:05:45', ''),
(9, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:06:11', ''),
(10, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:12:38', ''),
(11, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:12:55', ''),
(12, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:13:06', ''),
(13, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:13:20', ''),
(14, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:37:45', ''),
(15, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:39:11', ''),
(16, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:39:49', ''),
(17, '[Payment Invoice 3]  Transaction-1 - 10000 ', '2026-10-17 07:39:49', 'admin'),
(18, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:40:14', ''),
(19, '[Payment Invoice 4]  Transaction-2 - 25000 ', '2026-10-17 07:40:15', 'admin'),
(20, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:40:34', ''),
(21, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:40:52', ''),
(22, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:41:26', ''),
(23, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:42:07', ''),
(24, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:43:41', ''),
(25, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:46:41', ''),
(26, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:48:53', ''),
(27, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:49:14', ''),
(28, '[Payment Invoice 5]  Transaction-3 - 89000 ', '2026-10-17 07:54:39', 'admin'),
(29, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:55:19', ''),
(30, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:57:30', ''),
(31, '[Payment Invoice 6]  Transaction-4 - 90000 ', '2026-10-17 07:57:31', 'admin'),
(32, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:57:58', ''),
(33, '[Payment Invoice 7]  Transaction-5 - 90000 ', '2026-10-17 07:57:59', 'admin'),
(34, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:58:19', ''),
(35, '[Logged In] kambire2007@gmail.com', '2026-10-17 07:59:33', ''),
(36, '[Payment Invoice 8]  Transaction-6 - 35000 ', '2026-10-17 08:07:56', 'admin'),
(37, '[Logged In] kambire2007@gmail.com', '2026-10-21 01:01:11', ''),
(38, '[Logged Out] admin', '2026-10-21 01:03:35', ''),
(39, '[Logged In] kambire2007@gmail.com', '2026-10-21 01:04:21', '');

-- --------------------------------------------------------

--
-- Table structure for table `geopos_login_attempts`
--

CREATE TABLE `geopos_login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(39) DEFAULT '0',
  `timestamp` datetime DEFAULT NULL,
  `login_attempts` tinyint(2) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_metadata`
--

CREATE TABLE `geopos_metadata` (
  `id` int(11) NOT NULL,
  `type` int(3) NOT NULL,
  `rid` int(11) NOT NULL,
  `col1` varchar(255) DEFAULT NULL,
  `col2` varchar(255) DEFAULT NULL,
  `d_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `geopos_metadata`
--

INSERT INTO `geopos_metadata` (`id`, `type`, `rid`, `col1`, `col2`, `d_date`) VALUES
(1, 9, 1, '0', NULL, '2026-10-17'),
(2, 9, 1, '0', NULL, '2026-10-17'),
(3, 9, 2, '0', NULL, '2026-10-17'),
(4, 9, 3, '10000', NULL, '2026-10-17'),
(5, 9, 4, '25000', NULL, '2026-10-17'),
(6, 9, 5, '35600', NULL, '2026-10-17'),
(7, 9, 6, '36000', NULL, '2026-10-17'),
(8, 9, 7, '36000', NULL, '2026-10-17'),
(9, 9, 8, '14000', NULL, '2026-10-17');

-- --------------------------------------------------------

--
-- Table structure for table `geopos_milestones`
--

CREATE TABLE `geopos_milestones` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `sdate` date NOT NULL,
  `edate` date NOT NULL,
  `exp` text NOT NULL,
  `color` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_movers`
--

CREATE TABLE `geopos_movers` (
  `id` int(11) NOT NULL,
  `d_type` int(3) NOT NULL,
  `rid1` int(11) NOT NULL,
  `rid2` int(11) NOT NULL,
  `rid3` int(11) NOT NULL,
  `d_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `note` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_notes`
--

CREATE TABLE `geopos_notes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `cdate` date NOT NULL,
  `last_edit` datetime NOT NULL DEFAULT current_timestamp(),
  `cid` int(11) NOT NULL DEFAULT 0,
  `fid` int(11) NOT NULL DEFAULT 0,
  `rid` int(11) NOT NULL DEFAULT 0,
  `ntype` int(2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_pms`
--

CREATE TABLE `geopos_pms` (
  `id` int(11) UNSIGNED NOT NULL,
  `sender_id` int(11) UNSIGNED NOT NULL,
  `receiver_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `date_sent` datetime DEFAULT NULL,
  `date_read` datetime DEFAULT NULL,
  `pm_deleted_sender` int(1) NOT NULL,
  `pm_deleted_receiver` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_premissions`
--

CREATE TABLE `geopos_premissions` (
  `id` int(11) NOT NULL,
  `module` enum('Sales','Stock','Crm','Project','Accounts','Miscellaneous','Employees','Assign Project','Customer Profile','Reports','Delete','POS','Sales Edit','Stock Edit') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `r_1` int(1) NOT NULL,
  `r_2` int(1) NOT NULL,
  `r_3` int(1) NOT NULL,
  `r_4` int(1) NOT NULL,
  `r_5` int(1) NOT NULL,
  `r_6` int(1) NOT NULL,
  `r_7` int(1) NOT NULL,
  `r_8` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `geopos_premissions`
--

INSERT INTO `geopos_premissions` (`id`, `module`, `r_1`, `r_2`, `r_3`, `r_4`, `r_5`, `r_6`, `r_7`, `r_8`) VALUES
(1, 'Sales', 0, 1, 1, 1, 1, 0, 0, 0),
(2, 'Stock', 1, 0, 1, 1, 1, 0, 0, 0),
(3, 'Crm', 0, 0, 1, 1, 1, 0, 0, 0),
(4, 'Project', 0, 0, 0, 1, 1, 1, 0, 0),
(5, 'Accounts', 0, 0, 0, 1, 1, 0, 0, 0),
(6, 'Miscellaneous', 0, 0, 0, 1, 1, 0, 0, 0),
(7, 'Assign Project', 0, 1, 0, 0, 1, 0, 0, 0),
(8, 'Customer Profile', 0, 0, 0, 0, 1, 0, 0, 0),
(9, 'Employees', 0, 0, 0, 0, 1, 0, 0, 0),
(10, 'Reports', 0, 0, 0, 1, 1, 0, 0, 0),
(11, 'Delete', 1, 1, 1, 1, 1, 1, 1, 1),
(12, 'POS', 1, 1, 1, 1, 1, 1, 1, 1),
(13, 'Sales Edit', 1, 1, 1, 1, 1, 1, 1, 1),
(14, 'Stock Edit', 1, 1, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `geopos_products`
--

CREATE TABLE `geopos_products` (
  `pid` int(11) NOT NULL,
  `pcat` int(3) NOT NULL DEFAULT 1,
  `warehouse` int(11) NOT NULL DEFAULT 1,
  `product_name` varchar(80) NOT NULL,
  `product_code` varchar(30) DEFAULT NULL,
  `product_price` decimal(16,2) DEFAULT 0.00,
  `fproduct_price` decimal(16,2) DEFAULT 0.00,
  `taxrate` decimal(16,2) DEFAULT 0.00,
  `disrate` decimal(16,2) DEFAULT 0.00,
  `qty` decimal(10,2) NOT NULL,
  `product_des` text DEFAULT NULL,
  `alert` int(11) DEFAULT NULL,
  `unit` varchar(4) DEFAULT NULL,
  `image` varchar(120) DEFAULT 'default.png',
  `barcode` varchar(40) DEFAULT NULL,
  `merge` int(2) NOT NULL,
  `sub` int(11) NOT NULL,
  `vb` int(11) NOT NULL,
  `expiry` date DEFAULT NULL,
  `code_type` varchar(8) DEFAULT 'EAN13',
  `sub_id` int(11) DEFAULT 0,
  `b_id` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_products`
--

INSERT INTO `geopos_products` (`pid`, `pcat`, `warehouse`, `product_name`, `product_code`, `product_price`, `fproduct_price`, `taxrate`, `disrate`, `qty`, `product_des`, `alert`, `unit`, `image`, `barcode`, `merge`, `sub`, `vb`, `expiry`, `code_type`, `sub_id`, `b_id`) VALUES
(1, 2, 1, 'Cheese Combo (Doble)', 'BURG-CHEESE', 35000.00, 21000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'BURG-CHEESE', 0, 0, 0, NULL, 'C128A', 0, 0),
(2, 2, 1, 'Bronx Combo (Doble)', 'BURG-BRONX', 35000.00, 21000.00, 0.00, 0.00, 99.00, '', 10, 'Und', '', 'BURG-BRONX', 0, 0, 0, NULL, 'C128A', 0, 0),
(3, 2, 1, 'New York Combo (Doble)', 'BURG-NY', 35000.00, 21000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'BURG-NY', 0, 0, 0, NULL, 'C128A', 0, 0),
(4, 2, 1, 'Strike Combo (Triple)', 'BURG-STRIKE', 50000.00, 30000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'BURG-STRIKE', 0, 0, 0, NULL, 'C128A', 0, 0),
(5, 2, 1, 'Menu Kids', 'BURG-KIDS', 35000.00, 21000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'BURG-KIDS', 0, 0, 0, NULL, 'C128A', 0, 0),
(6, 3, 1, 'Desme Cheese', 'DESM-CHEESE', 45000.00, 27000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'DESM-CHEESE', 0, 0, 0, NULL, 'C128A', 0, 0),
(7, 3, 1, 'Desme Bacon', 'DESM-BACON', 45000.00, 27000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'DESM-BACON', 0, 0, 0, NULL, 'C128A', 0, 0),
(8, 3, 1, 'Desme Classic', 'DESM-CLASSIC', 45000.00, 27000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'DESM-CLASSIC', 0, 0, 0, NULL, 'C128A', 0, 0),
(9, 4, 1, 'Pizza Mozzarella Mediana', 'PIZ-MOZZ-M', 50000.00, 30000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PIZ-MOZZ-M', 0, 0, 0, NULL, 'C128A', 0, 0),
(10, 4, 1, 'Pizza Mozzarella Gigante', 'PIZ-MOZZ-G', 90000.00, 54000.00, 0.00, 0.00, 98.00, '', 10, 'Und', '', 'PIZ-MOZZ-G', 0, 0, 0, NULL, 'C128A', 0, 0),
(11, 4, 1, 'Pizza Catupiry Mediana', 'PIZ-CATU-M', 50000.00, 30000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PIZ-CATU-M', 0, 0, 0, NULL, 'C128A', 0, 0),
(12, 4, 1, 'Pizza Catupiry Gigante', 'PIZ-CATU-G', 90000.00, 54000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PIZ-CATU-G', 0, 0, 0, NULL, 'C128A', 0, 0),
(13, 4, 1, 'Pizza Napoletana Mediana', 'PIZ-NAPO-M', 50000.00, 30000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PIZ-NAPO-M', 0, 0, 0, NULL, 'C128A', 0, 0),
(14, 4, 1, 'Pizza Napoletana Gigante', 'PIZ-NAPO-G', 90000.00, 54000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PIZ-NAPO-G', 0, 0, 0, NULL, 'C128A', 0, 0),
(15, 4, 1, 'Pizza Pepperoni Mediana', 'PIZ-PEPP-M', 60000.00, 36000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PIZ-PEPP-M', 0, 0, 0, NULL, 'C128A', 0, 0),
(16, 4, 1, 'Pizza Pepperoni Gigante', 'PIZ-PEPP-G', 100000.00, 60000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PIZ-PEPP-G', 0, 0, 0, NULL, 'C128A', 0, 0),
(17, 4, 1, 'Pizza Bacon BBQ Mediana', 'PIZ-BBQ-M', 60000.00, 36000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PIZ-BBQ-M', 0, 0, 0, NULL, 'C128A', 0, 0),
(18, 4, 1, 'Pizza Bacon BBQ Gigante', 'PIZ-BBQ-G', 100000.00, 60000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PIZ-BBQ-G', 0, 0, 0, NULL, 'C128A', 0, 0),
(19, 4, 1, 'Pizza Pollo Catupiry Mediana', 'PIZ-POLLO-M', 60000.00, 36000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PIZ-POLLO-M', 0, 0, 0, NULL, 'C128A', 0, 0),
(20, 4, 1, 'Pizza Pollo Catupiry Gigante', 'PIZ-POLLO-G', 100000.00, 60000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PIZ-POLLO-G', 0, 0, 0, NULL, 'C128A', 0, 0),
(21, 5, 1, 'Papa Regular', 'PAPA-REG', 12000.00, 7200.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PAPA-REG', 0, 0, 0, NULL, 'C128A', 0, 0),
(22, 5, 1, 'Papa Grande', 'PAPA-GDE', 16000.00, 9600.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PAPA-GDE', 0, 0, 0, NULL, 'C128A', 0, 0),
(23, 5, 1, 'Papa con Cheddar y Bacon', 'PAPA-CHED', 28000.00, 16800.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PAPA-CHED', 0, 0, 0, NULL, 'C128A', 0, 0),
(24, 6, 1, 'Combo Doble', 'PROMO-DOBLE', 75000.00, 45000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PROMO-DOBLE', 0, 0, 0, NULL, 'C128A', 0, 0),
(25, 6, 1, 'Combo Triple', 'PROMO-TRIPLE', 99000.00, 59400.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PROMO-TRIPLE', 0, 0, 0, NULL, 'C128A', 0, 0),
(26, 6, 1, 'Pizza Gigante Familiar 4 sabores', 'PROMO-FAMILIAR', 89000.00, 53400.00, 0.00, 0.00, 99.00, '', 10, 'Und', '', 'PROMO-FAMILIAR', 0, 0, 0, NULL, 'C128A', 0, 0),
(27, 6, 1, 'Promo 2 Pizzas Medianas Borde', 'PROMO-2PIZZA', 89000.00, 53400.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PROMO-2PIZZA', 0, 0, 0, NULL, 'C128A', 0, 0),
(28, 6, 1, 'Hamburguesa Corrida (Rodizio)', 'PROMO-RODIZIO', 75000.00, 45000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PROMO-RODIZIO', 0, 0, 0, NULL, 'C128A', 0, 0),
(29, 7, 1, 'Lunes 2 Bronx + Papas', 'PROMO-LUN-B', 50000.00, 30000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PROMO-LUN-B', 0, 0, 0, NULL, 'C128A', 0, 0),
(30, 7, 1, 'Lunes 2 Pizzas Med Mozzarella', 'PROMO-LUN-P', 69000.00, 41400.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PROMO-LUN-P', 0, 0, 0, NULL, 'C128A', 0, 0),
(31, 7, 1, 'Martes 2 New York + Papas', 'PROMO-MAR-B', 50000.00, 30000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PROMO-MAR-B', 0, 0, 0, NULL, 'C128A', 0, 0),
(32, 7, 1, 'Martes 2 Pizzas Med Catupiry', 'PROMO-MAR-P', 79000.00, 47400.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PROMO-MAR-P', 0, 0, 0, NULL, 'C128A', 0, 0),
(33, 7, 1, 'Miercoles 2 Cheese + Papas', 'PROMO-MIE-B', 50000.00, 30000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PROMO-MIE-B', 0, 0, 0, NULL, 'C128A', 0, 0),
(34, 7, 1, 'Miercoles 2 Pizzas Med Pepperoni', 'PROMO-MIE-P', 79000.00, 47400.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PROMO-MIE-P', 0, 0, 0, NULL, 'C128A', 0, 0),
(35, 7, 1, 'Jueves 2x1 Times Square', 'PROMO-JUE-B', 35000.00, 21000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PROMO-JUE-B', 0, 0, 0, NULL, 'C128A', 0, 0),
(36, 7, 1, 'Jueves 2 Pizzas Med Bacon BBQ', 'PROMO-JUE-P', 79000.00, 47400.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'PROMO-JUE-P', 0, 0, 0, NULL, 'C128A', 0, 0),
(37, 8, 1, 'Tacos Suadero', 'TACO-SUAD', 50000.00, 30000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'TACO-SUAD', 0, 0, 0, NULL, 'C128A', 0, 0),
(38, 8, 1, 'Tacos Alambre', 'TACO-ALAM', 50000.00, 30000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'TACO-ALAM', 0, 0, 0, NULL, 'C128A', 0, 0),
(39, 8, 1, 'Tacos Campechano', 'TACO-CAMP', 50000.00, 30000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'TACO-CAMP', 0, 0, 0, NULL, 'C128A', 0, 0),
(40, 8, 1, 'Dorilocos', 'DORILOCOS', 30000.00, 18000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'DORILOCOS', 0, 0, 0, NULL, 'C128A', 0, 0),
(41, 8, 1, 'Birriamen', 'BIRRIAMEN', 30000.00, 18000.00, 0.00, 0.00, 100.00, '', 10, 'Und', '', 'BIRRIAMEN', 0, 0, 0, NULL, 'C128A', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `geopos_product_cat`
--

CREATE TABLE `geopos_product_cat` (
  `id` int(3) NOT NULL,
  `title` varchar(100) NOT NULL,
  `extra` varchar(255) DEFAULT NULL,
  `c_type` int(2) DEFAULT 0,
  `rel_id` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_product_cat`
--

INSERT INTO `geopos_product_cat` (`id`, `title`, `extra`, `c_type`, `rel_id`) VALUES
(1, 'General', 'General Cat', 0, 0),
(2, 'Combos Smashburger', 'Combos Smashburger', 0, 0),
(3, 'Desmechados', 'Desmechados', 0, 0),
(4, 'Pizzas', 'Pizzas', 0, 0),
(5, 'Papas', 'Papas', 0, 0),
(6, 'Promociones', 'Promociones', 0, 0),
(7, 'Promos del Dia', 'Promos del Dia', 0, 0),
(8, 'Tacos y Antojitos', 'Tacos y Antojitos', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `geopos_product_serials`
--

CREATE TABLE `geopos_product_serials` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `serial` varchar(200) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_projects`
--

CREATE TABLE `geopos_projects` (
  `id` int(11) NOT NULL,
  `p_id` varchar(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` enum('Waiting','Pending','Terminated','Finished','Progress') NOT NULL DEFAULT 'Pending',
  `priority` enum('Low','Medium','High','Urgent') NOT NULL DEFAULT 'Medium',
  `progress` int(3) NOT NULL,
  `cid` int(11) NOT NULL,
  `sdate` date NOT NULL,
  `edate` date NOT NULL,
  `tag` varchar(255) DEFAULT NULL,
  `phase` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `worth` decimal(16,2) NOT NULL DEFAULT 0.00,
  `ptype` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_project_meta`
--

CREATE TABLE `geopos_project_meta` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `meta_key` int(11) NOT NULL,
  `meta_data` varchar(200) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `key3` varchar(20) DEFAULT NULL,
  `key4` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_promo`
--

CREATE TABLE `geopos_promo` (
  `id` int(11) NOT NULL,
  `code` varchar(15) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `valid` date NOT NULL,
  `active` int(1) NOT NULL,
  `note` varchar(100) NOT NULL,
  `reflect` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `available` int(11) NOT NULL,
  `location` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_purchase`
--

CREATE TABLE `geopos_purchase` (
  `id` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `invoicedate` date NOT NULL,
  `invoiceduedate` date NOT NULL,
  `subtotal` decimal(16,2) DEFAULT 0.00,
  `shipping` decimal(16,2) DEFAULT 0.00,
  `ship_tax` decimal(16,2) DEFAULT NULL,
  `ship_tax_type` enum('incl','excl','off') DEFAULT 'off',
  `discount` decimal(16,2) DEFAULT 0.00,
  `tax` decimal(16,2) DEFAULT 0.00,
  `total` decimal(16,2) DEFAULT 0.00,
  `pmethod` varchar(14) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `status` enum('paid','due','canceled','partial') DEFAULT 'due',
  `csd` int(5) NOT NULL DEFAULT 0,
  `eid` int(4) NOT NULL,
  `pamnt` decimal(16,2) DEFAULT 0.00,
  `items` decimal(10,2) NOT NULL,
  `taxstatus` enum('yes','no','incl','cgst','igst') DEFAULT 'yes',
  `discstatus` tinyint(1) NOT NULL,
  `format_discount` enum('%','flat','b_p','bflat') DEFAULT NULL,
  `refer` varchar(20) DEFAULT NULL,
  `term` int(3) NOT NULL,
  `loc` int(4) NOT NULL,
  `multi` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_purchase_items`
--

CREATE TABLE `geopos_purchase_items` (
  `id` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `product` varchar(255) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `qty` decimal(10,2) NOT NULL,
  `price` decimal(16,2) DEFAULT 0.00,
  `tax` decimal(16,2) DEFAULT 0.00,
  `discount` decimal(16,2) DEFAULT 0.00,
  `subtotal` decimal(16,2) DEFAULT 0.00,
  `totaltax` decimal(16,2) DEFAULT 0.00,
  `totaldiscount` decimal(16,2) DEFAULT 0.00,
  `product_des` text DEFAULT NULL,
  `unit` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_quotes`
--

CREATE TABLE `geopos_quotes` (
  `id` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `invoicedate` date NOT NULL,
  `invoiceduedate` date NOT NULL,
  `subtotal` decimal(16,2) DEFAULT 0.00,
  `shipping` decimal(16,2) DEFAULT 0.00,
  `ship_tax` decimal(16,2) DEFAULT NULL,
  `ship_tax_type` enum('incl','excl','off') DEFAULT 'off',
  `discount` decimal(16,2) DEFAULT 0.00,
  `tax` decimal(16,2) DEFAULT 0.00,
  `total` decimal(16,2) DEFAULT 0.00,
  `pmethod` varchar(14) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `status` enum('pending','accepted','rejected','customer_approved') DEFAULT 'pending',
  `csd` int(5) NOT NULL DEFAULT 0,
  `eid` int(4) NOT NULL,
  `pamnt` decimal(16,2) NOT NULL,
  `items` decimal(10,2) NOT NULL,
  `taxstatus` enum('yes','no','incl','cgst','igst') DEFAULT 'yes',
  `discstatus` tinyint(1) NOT NULL,
  `format_discount` enum('%','flat','b_p','bflat') DEFAULT '%',
  `refer` varchar(20) DEFAULT NULL,
  `term` int(3) NOT NULL,
  `proposal` text DEFAULT NULL,
  `multi` int(4) DEFAULT NULL,
  `loc` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_quotes_items`
--

CREATE TABLE `geopos_quotes_items` (
  `id` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `product` varchar(255) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `qty` decimal(16,2) NOT NULL,
  `price` decimal(16,2) DEFAULT 0.00,
  `tax` decimal(16,2) DEFAULT 0.00,
  `discount` decimal(16,2) DEFAULT 0.00,
  `subtotal` decimal(16,2) DEFAULT 0.00,
  `totaltax` decimal(16,2) DEFAULT 0.00,
  `totaldiscount` decimal(16,2) DEFAULT 0.00,
  `product_des` text DEFAULT NULL,
  `unit` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_register`
--

CREATE TABLE `geopos_register` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `o_date` datetime NOT NULL,
  `c_date` datetime NOT NULL,
  `cash` decimal(16,2) NOT NULL,
  `card` decimal(16,2) NOT NULL,
  `bank` decimal(16,2) NOT NULL,
  `cheque` decimal(16,2) NOT NULL,
  `r_change` decimal(16,2) NOT NULL,
  `active` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_register`
--

INSERT INTO `geopos_register` (`id`, `uid`, `o_date`, `c_date`, `cash`, `card`, `bank`, `cheque`, `r_change`, `active`) VALUES
(1, 11, '2026-10-17 06:18:05', '2026-10-17 06:18:52', 0.00, 0.00, 0.00, 0.00, 0.00, 0),
(2, 11, '2026-10-17 07:10:43', '2026-10-17 07:12:55', 100000.00, 0.00, 0.00, 0.00, 0.00, 0),
(3, 11, '2026-10-17 07:13:07', '2026-10-17 07:13:20', 150000.00, 0.00, 0.00, 0.00, 0.00, 0),
(4, 11, '2026-10-17 07:16:10', '2026-10-17 07:27:21', 100000.00, 0.00, 0.00, 0.00, 0.00, 0),
(5, 11, '2026-10-17 07:35:41', '2026-10-17 07:39:15', 100000.00, 0.00, 0.00, 0.00, 0.00, 0),
(6, 11, '2026-10-17 07:43:42', '2026-10-17 07:43:42', 0.00, 0.00, 0.00, 0.00, 0.00, 0),
(7, 11, '2026-10-17 07:46:41', '2026-10-17 07:46:41', 0.00, 0.00, 0.00, 0.00, 0.00, 0),
(8, 11, '2026-10-17 07:48:20', '2026-10-17 07:48:55', 0.00, 0.00, 0.00, 0.00, 0.00, 0),
(9, 11, '2026-10-17 07:49:14', '2026-10-17 07:49:15', 0.00, 0.00, 0.00, 0.00, 0.00, 0),
(10, 11, '2026-10-17 07:49:52', '2026-10-17 07:50:33', 0.00, 0.00, 0.00, 0.00, 0.00, 0),
(11, 11, '2026-10-17 07:51:46', '2026-10-17 07:57:31', 179000.00, 0.00, 0.00, 0.00, 0.00, 0),
(12, 11, '2026-10-17 07:57:59', '0000-00-00 00:00:00', 125000.00, 0.00, 0.00, 0.00, 0.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `geopos_reports`
--

CREATE TABLE `geopos_reports` (
  `id` int(11) NOT NULL,
  `month` varchar(10) DEFAULT NULL,
  `year` int(4) NOT NULL,
  `invoices` int(11) NOT NULL,
  `sales` decimal(16,2) DEFAULT 0.00,
  `items` decimal(10,2) NOT NULL,
  `income` decimal(16,2) DEFAULT 0.00,
  `expense` decimal(16,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_restkeys`
--

CREATE TABLE `geopos_restkeys` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `key` varchar(40) DEFAULT NULL,
  `level` int(2) NOT NULL,
  `ignore_limits` tinyint(1) NOT NULL DEFAULT 0,
  `is_private_key` tinyint(1) NOT NULL DEFAULT 0,
  `ip_addresses` text DEFAULT NULL,
  `date_created` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_restkeys`
--

INSERT INTO `geopos_restkeys` (`id`, `user_id`, `key`, `level`, `ignore_limits`, `is_private_key`, `ip_addresses`, `date_created`) VALUES
(2, 0, '090665ea8d6294998e6829ad', 0, 0, 0, NULL, '2026-10-17');

-- --------------------------------------------------------

--
-- Table structure for table `geopos_smtp`
--

CREATE TABLE `geopos_smtp` (
  `id` int(11) NOT NULL,
  `host` varchar(100) NOT NULL,
  `port` int(11) NOT NULL,
  `auth` enum('true','false') NOT NULL,
  `auth_type` enum('none','tls','ssl') NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `sender` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_smtp`
--

INSERT INTO `geopos_smtp` (`id`, `host`, `port`, `auth`, `auth_type`, `username`, `password`, `sender`) VALUES
(1, 'smtp.com', 587, 'true', 'none', 'support@example.com', '123456', 'support@example.com');

-- --------------------------------------------------------

--
-- Table structure for table `geopos_stock_r`
--

CREATE TABLE `geopos_stock_r` (
  `id` int(11) NOT NULL,
  `tid` int(8) NOT NULL,
  `invoicedate` date NOT NULL,
  `invoiceduedate` date NOT NULL,
  `subtotal` decimal(16,2) DEFAULT 0.00,
  `shipping` decimal(16,2) DEFAULT 0.00,
  `ship_tax` decimal(16,2) DEFAULT NULL,
  `ship_tax_type` enum('incl','excl','off') DEFAULT 'off',
  `discount` decimal(16,2) DEFAULT 0.00,
  `tax` decimal(16,2) DEFAULT 0.00,
  `total` decimal(16,2) DEFAULT 0.00,
  `pmethod` varchar(14) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `status` enum('pending','accepted','rejected','partial','canceled') DEFAULT 'pending',
  `csd` int(5) NOT NULL DEFAULT 0,
  `eid` int(4) NOT NULL,
  `pamnt` decimal(16,2) DEFAULT 0.00,
  `items` decimal(10,0) NOT NULL,
  `taxstatus` enum('yes','no','incl','cgst','igst') DEFAULT 'yes',
  `discstatus` tinyint(1) NOT NULL,
  `format_discount` enum('%','flat','bflat','b_p') DEFAULT NULL,
  `refer` varchar(20) DEFAULT NULL,
  `term` int(3) NOT NULL,
  `loc` int(4) NOT NULL,
  `i_class` int(1) NOT NULL DEFAULT 0,
  `multi` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_stock_r_items`
--

CREATE TABLE `geopos_stock_r_items` (
  `id` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `product` varchar(255) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `qty` decimal(10,2) NOT NULL,
  `price` decimal(16,2) DEFAULT 0.00,
  `tax` decimal(16,2) DEFAULT 0.00,
  `discount` decimal(16,2) DEFAULT 0.00,
  `subtotal` decimal(16,2) DEFAULT 0.00,
  `totaltax` decimal(16,2) DEFAULT 0.00,
  `totaldiscount` decimal(16,2) DEFAULT 0.00,
  `product_des` text DEFAULT NULL,
  `unit` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_supplier`
--

CREATE TABLE `geopos_supplier` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `region` varchar(30) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `postbox` varchar(20) DEFAULT NULL,
  `email` varchar(60) DEFAULT NULL,
  `picture` varchar(100) NOT NULL DEFAULT 'example.png',
  `gid` int(5) NOT NULL DEFAULT 1,
  `company` varchar(100) DEFAULT NULL,
  `taxid` varchar(100) DEFAULT NULL,
  `loc` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_system`
--

CREATE TABLE `geopos_system` (
  `id` int(1) NOT NULL,
  `cname` char(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(30) NOT NULL,
  `region` varchar(40) NOT NULL,
  `country` varchar(30) NOT NULL,
  `postbox` varchar(15) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(40) NOT NULL,
  `taxid` varchar(20) NOT NULL,
  `tax` int(11) NOT NULL,
  `currency` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci NOT NULL,
  `currency_format` int(1) NOT NULL,
  `prefix` varchar(5) NOT NULL,
  `dformat` int(1) NOT NULL,
  `zone` varchar(25) NOT NULL,
  `logo` varchar(30) NOT NULL,
  `lang` varchar(20) DEFAULT 'english',
  `foundation` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_system`
--

INSERT INTO `geopos_system` (`id`, `cname`, `address`, `city`, `region`, `country`, `postbox`, `phone`, `email`, `taxid`, `tax`, `currency`, `currency_format`, `prefix`, `dformat`, `zone`, `logo`, `lang`, `foundation`) VALUES
(1, 'ABC Company', '412 Example South Street,', 'Los Angeles', 'FL', 'USA', '123', '410-987-89-60', 'support@ultimatekode.com', '23442', -1, 'Gs', 0, 'SRN', 1, 'Etc/Greenwich', 'logo.png', 'spanish', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `geopos_terms`
--

CREATE TABLE `geopos_terms` (
  `id` int(4) NOT NULL,
  `title` varchar(100) NOT NULL,
  `type` int(1) NOT NULL,
  `terms` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_terms`
--

INSERT INTO `geopos_terms` (`id`, `title`, `type`, `terms`) VALUES
(1, 'Payment On Receipt', 0, '<p>1. 10% discount if payment received within ten days otherwise payment 30 days\n            after invoice date<br></p>');

-- --------------------------------------------------------

--
-- Table structure for table `geopos_tickets`
--

CREATE TABLE `geopos_tickets` (
  `id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `cid` int(11) NOT NULL,
  `status` enum('Solved','Processing','Waiting') NOT NULL,
  `section` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_tickets_th`
--

CREATE TABLE `geopos_tickets_th` (
  `id` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `cid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `cdate` datetime NOT NULL,
  `attach` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_todolist`
--

CREATE TABLE `geopos_todolist` (
  `id` int(11) NOT NULL,
  `tdate` date NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `status` enum('Due','Done','Progress') NOT NULL DEFAULT 'Due',
  `start` date NOT NULL,
  `duedate` date NOT NULL,
  `description` text DEFAULT NULL,
  `eid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `related` int(11) NOT NULL,
  `priority` enum('Low','Medium','High','Urgent') NOT NULL,
  `rid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_transactions`
--

CREATE TABLE `geopos_transactions` (
  `id` int(11) NOT NULL,
  `acid` int(11) NOT NULL,
  `account` varchar(200) NOT NULL,
  `type` enum('Income','Expense','Transfer') NOT NULL,
  `cat` varchar(200) NOT NULL,
  `debit` decimal(16,2) DEFAULT 0.00,
  `credit` decimal(16,2) DEFAULT 0.00,
  `payer` varchar(200) DEFAULT NULL,
  `payerid` int(11) NOT NULL DEFAULT 0,
  `method` varchar(200) DEFAULT NULL,
  `date` date NOT NULL,
  `tid` int(11) NOT NULL DEFAULT 0,
  `eid` int(11) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `ext` int(1) DEFAULT 0,
  `loc` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_transactions`
--

INSERT INTO `geopos_transactions` (`id`, `acid`, `account`, `type`, `cat`, `debit`, `credit`, `payer`, `payerid`, `method`, `date`, `tid`, `eid`, `note`, `ext`, `loc`) VALUES
(1, 1, 'Sales Account', 'Income', 'Sales', 0.00, 10000.00, 'Walk-in Client', 1, 'Cash', '2026-10-17', 3, 11, '#1001-Cash', 0, 0),
(2, 1, 'Sales Account', 'Income', 'Sales', 0.00, 25000.00, 'Walk-in Client', 1, 'Card Swipe', '2026-10-17', 4, 11, '#1002-Card Swipe', 0, 0),
(3, 1, 'Sales Account', 'Income', 'Sales', 0.00, 89000.00, 'Walk-in Client', 1, 'Cash', '2026-10-17', 5, 11, '#1001-Cash', 0, 0),
(4, 1, 'Sales Account', 'Income', 'Sales', 0.00, 90000.00, 'Walk-in Client', 1, 'Cash', '2026-10-17', 6, 11, '#1002-Cash', 0, 0),
(5, 1, 'Sales Account', 'Income', 'Sales', 0.00, 90000.00, 'Walk-in Client', 1, 'Cash', '2026-10-17', 7, 11, '#1002-Cash', 0, 0),
(6, 1, 'Sales Account', 'Income', 'Sales', 0.00, 35000.00, 'Walk-in Client', 1, 'Cash', '2026-10-17', 8, 11, '#1003-Cash', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `geopos_trans_cat`
--

CREATE TABLE `geopos_trans_cat` (
  `id` int(11) NOT NULL,
  `name` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_trans_cat`
--

INSERT INTO `geopos_trans_cat` (`id`, `name`) VALUES
(1, 'Income'),
(2, 'Expenses'),
(3, 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `geopos_units`
--

CREATE TABLE `geopos_units` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `code` varchar(5) NOT NULL,
  `type` int(1) NOT NULL,
  `sub` int(1) NOT NULL,
  `rid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geopos_users`
--

CREATE TABLE `geopos_users` (
  `id` int(11) UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL,
  `pass` varchar(64) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `banned` tinyint(1) DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `forgot_exp` text DEFAULT NULL,
  `remember_time` datetime DEFAULT NULL,
  `remember_exp` text DEFAULT NULL,
  `verification_code` text DEFAULT NULL,
  `totp_secret` varchar(16) DEFAULT NULL,
  `ip_address` text DEFAULT NULL,
  `roleid` int(1) NOT NULL,
  `picture` varchar(50) DEFAULT NULL,
  `loc` int(11) NOT NULL,
  `lang` char(15) NOT NULL DEFAULT 'english'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_users`
--

INSERT INTO `geopos_users` (`id`, `email`, `pass`, `username`, `banned`, `last_login`, `last_activity`, `date_created`, `forgot_exp`, `remember_time`, `remember_exp`, `verification_code`, `totp_secret`, `ip_address`, `roleid`, `picture`, `loc`, `lang`) VALUES
(11, 'kambire2007@gmail.com', '8a751e1e3e66916f2edfc00853417c099abe83746332f049c399414472510387', 'admin', 0, '2026-10-21 01:04:21', '2026-10-21 01:04:21', '2026-10-17 08:04:34', NULL, '2026-10-24 00:00:00', 'X4amG9nQzAUjsRul', '', NULL, '186.12.181.81', 5, 'example.png', 0, 'english');

-- --------------------------------------------------------

--
-- Table structure for table `geopos_warehouse`
--

CREATE TABLE `geopos_warehouse` (
  `id` int(3) NOT NULL,
  `title` varchar(100) NOT NULL,
  `extra` varchar(255) DEFAULT NULL,
  `loc` int(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `geopos_warehouse`
--

INSERT INTO `geopos_warehouse` (`id`, `title`, `extra`, `loc`) VALUES
(1, 'Main WareHouse', 'The Main WareHouse', 0);

-- --------------------------------------------------------

--
-- Table structure for table `univarsal_api`
--

CREATE TABLE `univarsal_api` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `key1` varchar(255) DEFAULT NULL,
  `key2` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `method` varchar(10) DEFAULT NULL,
  `other` text DEFAULT NULL,
  `active` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `univarsal_api`
--

INSERT INTO `univarsal_api` (`id`, `name`, `key1`, `key2`, `url`, `method`, `other`, `active`) VALUES
(1, 'Goo.gl URL Shortner', 'yourkey', '0', '0', '0', '0', 0),
(2, 'Twilio SMS API', 'ac', 'key', '+11234567', '0', '0', 1),
(3, 'Company Support', '1', '1', 'support@gmail.com', NULL, '<p>Your footer</p>', 1),
(4, 'Currency', ',', '.', '0', 'r', 'Off', 0),
(5, 'Exchange', 'key1v', 'key2', 'USD', NULL, NULL, 0),
(6, 'New Invoice Notification', '[{Company}] Invoice #{BillNumber} Generated', NULL, NULL, NULL, '<p>Dear\n            Client,\r\n</p><p>We are contacting you in regard to a payment received for invoice # {BillNumber} that has\n            been created on your account. You may find the invoice with below link.\r\n\r\nView\n            Invoice\r\n{URL}\r\n\r\nWe look forward to conducting future business with you.\r\n\r\nKind\n            Regards,\r\nTeam\r\n{CompanyDetails}</p>', NULL),
(7, 'Invoice Payment Reminder', '[{Company}] Invoice #{BillNumber} Payment Reminder', NULL, NULL, NULL, '<p>Dear\n            Client,</p><p>We are contacting you in regard to a payment reminder of invoice # {BillNumber} that has been\n            created on your account. You may find the invoice with below link. Please pay the balance of {Amount} due by\n            {DueDate}.</p><p>\r\n\r\n<b>View Invoice</b></p><p><span style=\"font-size: 1rem;\">{URL}\r\n</span></p><p>\n            <span style=\"font-size: 1rem;\">\r\nWe look forward to conducting future business with you.</span></p><p>\n            <span style=\"font-size: 1rem;\">\r\n\r\nKind Regards,\r\n</span></p><p><span style=\"font-size: 1rem;\">\r\nTeam\r\n</span>\n        </p><p><span style=\"font-size: 1rem;\">\r\n{CompanyDetails}</span></p>', NULL),
(8, 'Invoice Refund Proceeded', '{Company} Invoice #{BillNumber} Refund Proceeded', NULL, NULL, NULL, '<p>Dear\n            Client,</p><p>\r\nWe are contacting you in regard to a refund request processed for invoice # {BillNumber}\n            that has been created on your account. You may find the invoice with below link. Please pay the balance of\n            {Amount} by {DueDate}.\r\n</p><p>\r\nView Invoice\r\n</p><p>{URL}\r\n</p><p>\r\nWe look forward to\n            conducting future business with you.\r\n</p><p>\r\nKind Regards,\r\n</p><p>\n            \r\nTeam\r\n\r\n{CompanyDetails}</p>', NULL),
(9, 'Invoice payment Received', '{Company} Payment Received for Invoice #{BillNumber}', NULL, NULL, NULL, '<p>\n            Dear Client,\r\n</p><p>We are contacting you in regard to a payment received for invoice # {BillNumber} that\n            has been created on your account. You can find the invoice with below link.\r\n</p><p>\r\nView Invoice</p>\n        <p>\r\n{URL}\r\n</p><p>\r\nWe look forward to conducting future business with you.\r\n</p><p>\r\nKind\n            Regards,\r\n</p><p>\r\nTeam\r\n</p><p>\r\n{CompanyDetails}</p>', NULL),
(10, 'Invoice Overdue Notice', '{Company} Invoice #{BillNumber} Generated for you', NULL, NULL, NULL, '<p>Dear\n            Client,</p><p>\r\nWe are contacting you in regard to an Overdue Notice for invoice # {BillNumber} that has\n            been created on your account. You may find the invoice with below link.\r\nPlease pay the balance of\n            {Amount} due by {DueDate}.\r\n</p><p>View Invoice\r\n</p><p>{URL}\r\n</p><p>\r\nWe look forward to\n            conducting future business with you.\r\n</p><p>\r\nKind Regards,\r\n</p><p>\r\nTeam</p><p>\n            \r\n\r\n{CompanyDetails}</p>', NULL),
(11, 'Quote Proposal', '{Company} Quote #{BillNumber} Generated for you', NULL, NULL, NULL, '<p>Dear Client,</p>\n        <p>\r\nWe are contacting you in regard to a new quote # {BillNumber} that has been created on your account. You\n            may find the quote with below link.\r\n</p><p>\r\nView Invoice\r\n</p><p>{URL}\r\n</p><p>\r\nWe look forward\n            to conducting future business with you.</p><p>\r\n\r\nKind Regards,</p><p>\r\n\r\nTeam</p><p>\n            \r\n\r\n{CompanyDetails}</p>', NULL),
(12, 'Purchase Order Request', '{Company} Purchase Order #{BillNumber} Requested', NULL, NULL, NULL, '<p>Dear\n            Client,\r\n</p><p>We are contacting you in regard to a new purchase # {BillNumber} that has been requested\n            on your account. You may find the order with below link. </p><p>\r\n\r\nView Invoice\r\n</p><p>{URL}</p><p>\n            \r\n\r\nWe look forward to conducting future business with you.</p><p>\r\n\r\nKind Regards,\r\n</p><p>\n            \r\nTeam</p><p>\r\n\r\n{CompanyDetails}</p>', NULL),
(13, 'Stock Return Mail', '{Company} New purchase return # {BillNumber}', NULL, NULL, NULL, 'Dear Client,\r\n\r\nWe are contacting you in regard to a new purchase return # {BillNumber} that has been requested on your account. You may find the order with below link.\r\n\r\nView Invoice\r\n\r\n{URL}\r\n\r\nWe look forward to conducting future business with you.\r\n\r\nKind Regards,\r\n\r\nTeam\r\n\r\n{CompanyDetails}', NULL),
(14, 'Customer Registration', '{Company}  Customer Registration - {NAME}', NULL, NULL, NULL, 'Dear Customer,\r\nThank You for registration, please confirm the registration by the following URL {REG_URL}\r\nRegards', NULL),
(15, ' Customer Password Reset', '{Company}  Customer Password Reset- {NAME}', NULL, NULL, NULL, 'Dear Customer,\r\nPlease reset the password by the following URL {RESET_URL}\r\nRegards', NULL),
(16, 'Customer Registration by Employee', '{Company}  Customer Registration - {NAME}', '0', '0', '0', 'Dear Customer,\r\nThank You for registration.\r\nLogin URL: {URL}\r\nLogin Email: {EMAIL}\r\nPassword: {PASSWORD}\r\n\r\nRegards\r\n{CompanyDetails}', 0),
(30, 'New Invoice Notification', NULL, NULL, NULL, NULL, 'Dear Customer, new invoice  # {BillNumber} generated. {URL} Regards', NULL),
(31, 'Invoice Payment Reminder', NULL, NULL, NULL, NULL, 'Dear Customer, Please make payment of invoice  # {BillNumber}. {URL} Regards', NULL),
(32, 'Invoice Refund Proceeded', NULL, NULL, NULL, NULL, 'Dear Customer, Refund generated of invoice # {BillNumber}. {URL} Regards', NULL),
(33, 'Invoice payment Received', NULL, NULL, NULL, NULL, 'Dear Customer, Payment received of invoice # {BillNumber}. {URL} Regards', NULL),
(34, 'Invoice Overdue Notice', NULL, NULL, NULL, NULL, 'Dear Customer, Dear Customer,Payment is overdue of invoice # {BillNumber}. {URL} Regards', NULL),
(35, 'Quote Proposal', NULL, NULL, NULL, NULL, 'Dear Customer, Dear Customer, a quote created for you # {BillNumber}. {URL} Regards', NULL),
(36, 'Purchase Order Request', NULL, NULL, NULL, NULL, 'Dear Customer, Dear, a purchased order for you # {BillNumber}. {URL} Regards', NULL),
(51, 'QT#', 'PO#', 'SUB#', 'SR#', 'TRN#', 'SRN#', 1),
(52, 'ThermalPrint', '0', NULL, NULL, NULL, 'POS#', 0),
(53, 'ConfPort', 'Public Key', '0', 'Private Key', NULL, NULL, 1),
(54, 'online_payment', '1', 'USD', '1', '1', NULL, 1),
(55, 'CronJob', '99293768', 'rec_email', 'email', 'rec_due', 'recemail', NULL),
(56, 'Auto Email SMS', 'email', 'sms', NULL, NULL, NULL, NULL),
(60, 'Warehouse', '1', NULL, NULL, NULL, NULL, NULL),
(61, 'Discount & Shipping', '%', '10.00', 'incl', NULL, '% Discount After TAX', NULL),
(62, 'AutoAttendance', '1', '0', '0', '0', '0', NULL),
(63, 'Zero Stock Billing', '1', '0', '0', '0', '0', 0),
(64, 'FrontEndSection', '0', '0', '0', '0', NULL, 0),
(65, 'Dual Entry', '0', '1', '0', '0', '0', 0),
(66, 'Email Alert', '0', '0', 'sample@email.com', '0', '0', 0),
(67, 'billing_settings', '0', '0', NULL, NULL, NULL, NULL),
(69, 'pos_settings', '1', NULL, NULL, NULL, NULL, NULL),
(70, 'DB-B-150', '4a99003dd3aa27b87fabfa153f1ab06a2ff08c3d', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `users_id` int(11) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `var_key` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `is_deleted` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `user_type` varchar(255) DEFAULT NULL,
  `cid` int(11) DEFAULT NULL,
  `lang` varchar(25) NOT NULL DEFAULT 'english',
  `code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`users_id`, `user_id`, `var_key`, `status`, `is_deleted`, `name`, `password`, `email`, `profile_pic`, `user_type`, `cid`, `lang`, `code`) VALUES
(1, '1', NULL, 'active', '0', 'Walk-in Client', '$2y$10$TKfTVltchv/jxZIouy8i8O7rSzVvtdx4Y5wNRodK5RNyTBJkEoIAW', 'example@example.com', NULL, 'Member', 1, 'english', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ci_sessions_timestamp` (`timestamp`);

--
-- Indexes for table `geopos_accounts`
--
ALTER TABLE `geopos_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `acn` (`acn`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `geopos_attendance`
--
ALTER TABLE `geopos_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emp` (`emp`);

--
-- Indexes for table `geopos_bank_ac`
--
ALTER TABLE `geopos_bank_ac`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_config`
--
ALTER TABLE `geopos_config`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `geopos_currencies`
--
ALTER TABLE `geopos_currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_customers`
--
ALTER TABLE `geopos_customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gid` (`gid`);

--
-- Indexes for table `geopos_custom_data`
--
ALTER TABLE `geopos_custom_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fid` (`field_id`,`rid`);

--
-- Indexes for table `geopos_custom_fields`
--
ALTER TABLE `geopos_custom_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `f_module` (`f_module`);

--
-- Indexes for table `geopos_cust_group`
--
ALTER TABLE `geopos_cust_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_documents`
--
ALTER TABLE `geopos_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_draft`
--
ALTER TABLE `geopos_draft`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eid` (`eid`),
  ADD KEY `csd` (`csd`),
  ADD KEY `invoice` (`tid`) USING BTREE,
  ADD KEY `i_class` (`i_class`),
  ADD KEY `loc` (`loc`);

--
-- Indexes for table `geopos_draft_items`
--
ALTER TABLE `geopos_draft_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice` (`tid`),
  ADD KEY `i_class` (`i_class`);

--
-- Indexes for table `geopos_employees`
--
ALTER TABLE `geopos_employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_events`
--
ALTER TABLE `geopos_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rel` (`rel`),
  ADD KEY `rid` (`rid`);

--
-- Indexes for table `geopos_gateways`
--
ALTER TABLE `geopos_gateways`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_hrm`
--
ALTER TABLE `geopos_hrm`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_invoices`
--
ALTER TABLE `geopos_invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eid` (`eid`),
  ADD KEY `csd` (`csd`),
  ADD KEY `invoice` (`tid`) USING BTREE,
  ADD KEY `i_class` (`i_class`),
  ADD KEY `loc` (`loc`);

--
-- Indexes for table `geopos_invoice_items`
--
ALTER TABLE `geopos_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice` (`tid`),
  ADD KEY `i_class` (`i_class`);

--
-- Indexes for table `geopos_locations`
--
ALTER TABLE `geopos_locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_log`
--
ALTER TABLE `geopos_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_login_attempts`
--
ALTER TABLE `geopos_login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_metadata`
--
ALTER TABLE `geopos_metadata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`),
  ADD KEY `rid` (`rid`);

--
-- Indexes for table `geopos_milestones`
--
ALTER TABLE `geopos_milestones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_movers`
--
ALTER TABLE `geopos_movers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `d_type` (`d_type`,`rid1`,`rid2`,`rid3`);

--
-- Indexes for table `geopos_notes`
--
ALTER TABLE `geopos_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_pms`
--
ALTER TABLE `geopos_pms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `full_index` (`id`,`sender_id`,`receiver_id`,`date_read`);

--
-- Indexes for table `geopos_premissions`
--
ALTER TABLE `geopos_premissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_products`
--
ALTER TABLE `geopos_products`
  ADD PRIMARY KEY (`pid`),
  ADD KEY `pcat` (`pcat`),
  ADD KEY `warehouse` (`warehouse`);

--
-- Indexes for table `geopos_product_cat`
--
ALTER TABLE `geopos_product_cat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_product_serials`
--
ALTER TABLE `geopos_product_serials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_projects`
--
ALTER TABLE `geopos_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `p_id` (`p_id`);

--
-- Indexes for table `geopos_project_meta`
--
ALTER TABLE `geopos_project_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`),
  ADD KEY `meta_key` (`meta_key`),
  ADD KEY `key3` (`key3`);

--
-- Indexes for table `geopos_promo`
--
ALTER TABLE `geopos_promo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_2` (`code`),
  ADD KEY `code` (`code`);

--
-- Indexes for table `geopos_purchase`
--
ALTER TABLE `geopos_purchase`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice` (`tid`),
  ADD KEY `eid` (`eid`),
  ADD KEY `csd` (`csd`);

--
-- Indexes for table `geopos_purchase_items`
--
ALTER TABLE `geopos_purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice` (`tid`);

--
-- Indexes for table `geopos_quotes`
--
ALTER TABLE `geopos_quotes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice` (`tid`),
  ADD KEY `eid` (`eid`),
  ADD KEY `csd` (`csd`);

--
-- Indexes for table `geopos_quotes_items`
--
ALTER TABLE `geopos_quotes_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice` (`tid`);

--
-- Indexes for table `geopos_register`
--
ALTER TABLE `geopos_register`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `active` (`active`);

--
-- Indexes for table `geopos_reports`
--
ALTER TABLE `geopos_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_restkeys`
--
ALTER TABLE `geopos_restkeys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_smtp`
--
ALTER TABLE `geopos_smtp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_stock_r`
--
ALTER TABLE `geopos_stock_r`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice` (`tid`),
  ADD KEY `eid` (`eid`),
  ADD KEY `csd` (`csd`);

--
-- Indexes for table `geopos_stock_r_items`
--
ALTER TABLE `geopos_stock_r_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice` (`tid`) KEY_BLOCK_SIZE=1024 USING BTREE;

--
-- Indexes for table `geopos_supplier`
--
ALTER TABLE `geopos_supplier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gid` (`gid`);

--
-- Indexes for table `geopos_system`
--
ALTER TABLE `geopos_system`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_terms`
--
ALTER TABLE `geopos_terms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_tickets`
--
ALTER TABLE `geopos_tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_tickets_th`
--
ALTER TABLE `geopos_tickets_th`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tid` (`tid`),
  ADD KEY `cid` (`cid`),
  ADD KEY `eid` (`eid`);

--
-- Indexes for table `geopos_todolist`
--
ALTER TABLE `geopos_todolist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_transactions`
--
ALTER TABLE `geopos_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loc` (`loc`),
  ADD KEY `acid` (`acid`),
  ADD KEY `eid` (`eid`),
  ADD KEY `tid` (`tid`);

--
-- Indexes for table `geopos_trans_cat`
--
ALTER TABLE `geopos_trans_cat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_units`
--
ALTER TABLE `geopos_units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `geopos_users`
--
ALTER TABLE `geopos_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `geopos_warehouse`
--
ALTER TABLE `geopos_warehouse`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `univarsal_api`
--
ALTER TABLE `univarsal_api`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`users_id`),
  ADD KEY `code` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `geopos_accounts`
--
ALTER TABLE `geopos_accounts`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `geopos_attendance`
--
ALTER TABLE `geopos_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `geopos_bank_ac`
--
ALTER TABLE `geopos_bank_ac`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_config`
--
ALTER TABLE `geopos_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_currencies`
--
ALTER TABLE `geopos_currencies`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `geopos_customers`
--
ALTER TABLE `geopos_customers`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `geopos_custom_data`
--
ALTER TABLE `geopos_custom_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_custom_fields`
--
ALTER TABLE `geopos_custom_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_cust_group`
--
ALTER TABLE `geopos_cust_group`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `geopos_documents`
--
ALTER TABLE `geopos_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_draft`
--
ALTER TABLE `geopos_draft`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `geopos_draft_items`
--
ALTER TABLE `geopos_draft_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_employees`
--
ALTER TABLE `geopos_employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `geopos_events`
--
ALTER TABLE `geopos_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_gateways`
--
ALTER TABLE `geopos_gateways`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `geopos_hrm`
--
ALTER TABLE `geopos_hrm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_invoices`
--
ALTER TABLE `geopos_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `geopos_invoice_items`
--
ALTER TABLE `geopos_invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `geopos_locations`
--
ALTER TABLE `geopos_locations`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_log`
--
ALTER TABLE `geopos_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `geopos_login_attempts`
--
ALTER TABLE `geopos_login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `geopos_metadata`
--
ALTER TABLE `geopos_metadata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `geopos_milestones`
--
ALTER TABLE `geopos_milestones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_movers`
--
ALTER TABLE `geopos_movers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_notes`
--
ALTER TABLE `geopos_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_pms`
--
ALTER TABLE `geopos_pms`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_premissions`
--
ALTER TABLE `geopos_premissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `geopos_products`
--
ALTER TABLE `geopos_products`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `geopos_product_cat`
--
ALTER TABLE `geopos_product_cat`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `geopos_product_serials`
--
ALTER TABLE `geopos_product_serials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_projects`
--
ALTER TABLE `geopos_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_project_meta`
--
ALTER TABLE `geopos_project_meta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_promo`
--
ALTER TABLE `geopos_promo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_purchase`
--
ALTER TABLE `geopos_purchase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_purchase_items`
--
ALTER TABLE `geopos_purchase_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_quotes`
--
ALTER TABLE `geopos_quotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_quotes_items`
--
ALTER TABLE `geopos_quotes_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_register`
--
ALTER TABLE `geopos_register`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `geopos_reports`
--
ALTER TABLE `geopos_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_restkeys`
--
ALTER TABLE `geopos_restkeys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `geopos_stock_r`
--
ALTER TABLE `geopos_stock_r`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_stock_r_items`
--
ALTER TABLE `geopos_stock_r_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_supplier`
--
ALTER TABLE `geopos_supplier`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_terms`
--
ALTER TABLE `geopos_terms`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `geopos_tickets`
--
ALTER TABLE `geopos_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_tickets_th`
--
ALTER TABLE `geopos_tickets_th`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_todolist`
--
ALTER TABLE `geopos_todolist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_transactions`
--
ALTER TABLE `geopos_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `geopos_trans_cat`
--
ALTER TABLE `geopos_trans_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `geopos_units`
--
ALTER TABLE `geopos_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geopos_users`
--
ALTER TABLE `geopos_users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `geopos_warehouse`
--
ALTER TABLE `geopos_warehouse`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `univarsal_api`
--
ALTER TABLE `univarsal_api`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `users_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
