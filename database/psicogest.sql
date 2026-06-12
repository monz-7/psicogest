-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-06-2026 a las 02:07:46
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `psicogest`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `credential`
--

CREATE TABLE `credential` (
  `uuid_credential` char(36) NOT NULL,
  `username` varchar(36) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','paciente','psicologo') NOT NULL,
  `status` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `credential`
--

INSERT INTO `credential` (`uuid_credential`, `username`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
('33680cd3-59d3-4e1a-a6ce-67e1f0e6c360', 'cc1002564872', 'bruno.casta@ccm.com', '$2y$10$D.D2SB1peINpych5PhmjDuAgVDM4/olwIY4YhBdwj0OQ5rnFlzy2G', 'psicologo', 'activo', '2026-06-05 10:52:04', '2026-06-05 10:52:04'),
('730c6e49-4021-4fd4-b3bb-2977da4b4283', 'cc1018965231', 'santiago.botero@ccm.com', '$2y$10$5GoRIUVRSRDgqGdk1vF0EO6zlYhaEk.8HgU0rLbGRJiazV6wnS5M.', 'psicologo', 'activo', '2026-06-05 10:52:04', '2026-06-11 18:38:08'),
('7d969cee-8e0f-4b40-8c0f-3d196892202c', 'cc45123978', 'amelia.perez@ccm.com', '$2y$10$89S0mjXD1OEmXIG0YdWBkerzP9.oL6SL3W.CsaeGqyl2JBxKi8/ju', 'psicologo', 'activo', '2026-06-05 10:52:04', '2026-06-11 17:23:14'),
('8cecff6d-8a7c-4f46-91f4-d815ca9978c5', 'paPN4561230', 'juan.paciente@correo.com', '$2y$10$PZb5DzaQJqt5T06wN4xw/.dDWWtPawc3IZX/EACLGesYfQN4sUyRe', 'paciente', 'activo', '2026-06-05 11:05:11', '2026-06-11 19:06:50'),
('9e04f4cc-7785-4c56-96b2-371a4101569f', 'admin', 'admin@psicogest.com', '$2y$10$ulUEoqdcG.QFoYKMC0BtaOwNmQ07xfJrwZZORNSBgy7whH88o4r12', 'admin', 'activo', '2026-06-05 10:25:57', '2026-06-05 10:25:57'),
('cb6983da-fe7f-4ca8-81d7-5f8e570e2267', 'cc1006584972', 'mariana.paciente@correo.com', '$2y$10$LhSo8a/dNb1ZrWKGwaTTTO2pPm9JdrflCpX4Q4Vmj1ps3s/ckULQC', 'paciente', 'activo', '2026-06-05 17:14:51', '2026-06-11 19:05:34'),
('fde67efe-f061-419a-ac9a-2f9043071ddd', 'cc18963254', 'bmaria.suaza@ccm.com', '$2y$10$Cv1PBSsv5jUti.eWZcH4QOfC9i6hqJAzkf82L5GE0JXoxFCVUIVrK', 'psicologo', 'activo', '2026-06-05 10:52:04', '2026-06-11 19:03:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `patient`
--

CREATE TABLE `patient` (
  `uuid_patient` char(36) NOT NULL,
  `uuid_user_profile` char(36) NOT NULL,
  `birth_date` date NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `patient`
--

INSERT INTO `patient` (`uuid_patient`, `uuid_user_profile`, `birth_date`, `created_at`, `updated_at`) VALUES
('da527b98-9027-49b6-a31f-b39c2af7cf78', '9042d7da-dacf-4022-956f-5af3a2f60ec6', '1996-03-02', '2026-06-05 17:18:21', '2026-06-05 17:18:21'),
('f511a31f-258b-4117-8e63-d3d6d534f541', 'a741e9e5-a15c-4854-99c8-8759abe34094', '1998-12-03', '2026-06-05 11:07:14', '2026-06-10 14:41:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `psychologist`
--

CREATE TABLE `psychologist` (
  `uuid_psychologist` char(36) NOT NULL,
  `uuid_user_profile` char(36) NOT NULL,
  `license_number` varchar(50) NOT NULL,
  `session_duration` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `psychologist`
--

INSERT INTO `psychologist` (`uuid_psychologist`, `uuid_user_profile`, `license_number`, `session_duration`, `created_at`, `updated_at`) VALUES
('155e64ac-3858-44fc-8f02-f26081303275', '205fbf07-6aaa-4e0e-8d94-35395440bf72', 'TP-1561165', 50, '2026-06-05 10:57:55', '2026-06-05 10:57:55'),
('310bd03a-11e1-42ef-b25e-ba984cefebb6', 'a692fb6b-d968-412d-855b-3b87ad3ea24f', 'TP-1156540', 45, '2026-06-05 10:57:55', '2026-06-11 19:07:23'),
('7ba15fd8-95dd-47d4-a846-d0ff0d505dc5', '00efe4d4-8896-4e36-a3c4-75833b017da1', 'TP-1561167', 60, '2026-06-05 10:57:55', '2026-06-05 10:57:55'),
('fa1a8676-ceed-4c1c-93a3-6bc877380814', 'e609f7be-c5d8-44aa-823e-17fdcf0fcf96', 'TP-1561164', 50, '2026-06-05 10:57:55', '2026-06-10 15:02:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `psychologist_specialty`
--

CREATE TABLE `psychologist_specialty` (
  `uuid_psychologist` char(36) NOT NULL,
  `uuid_specialty` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `psychologist_specialty`
--

INSERT INTO `psychologist_specialty` (`uuid_psychologist`, `uuid_specialty`) VALUES
('155e64ac-3858-44fc-8f02-f26081303275', 'c9f211af-da87-4629-8dba-b78227496851'),
('155e64ac-3858-44fc-8f02-f26081303275', 'eb0b2f29-c4f2-4aee-a41d-f44d04926914'),
('155e64ac-3858-44fc-8f02-f26081303275', 'f2ee4107-5fbd-49bd-a101-98b14782e21e'),
('310bd03a-11e1-42ef-b25e-ba984cefebb6', '284a646f-f660-48d8-960a-c3355b6e8165'),
('310bd03a-11e1-42ef-b25e-ba984cefebb6', 'e731a111-a4b6-43a4-82af-7c928ff4fce0'),
('310bd03a-11e1-42ef-b25e-ba984cefebb6', 'f2ee4107-5fbd-49bd-a101-98b14782e21e'),
('7ba15fd8-95dd-47d4-a846-d0ff0d505dc5', '201b1563-5dc9-458b-8bd1-71458f343081'),
('7ba15fd8-95dd-47d4-a846-d0ff0d505dc5', 'f2ee4107-5fbd-49bd-a101-98b14782e21e'),
('fa1a8676-ceed-4c1c-93a3-6bc877380814', 'a0c64975-c73c-4dce-a2ba-3519b76c0f97'),
('fa1a8676-ceed-4c1c-93a3-6bc877380814', 'cca7ae69-95e1-4a60-b916-de4dc9decb8d'),
('fa1a8676-ceed-4c1c-93a3-6bc877380814', 'f2ee4107-5fbd-49bd-a101-98b14782e21e');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `specialty`
--

CREATE TABLE `specialty` (
  `uuid_specialty` char(36) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `specialty`
--

INSERT INTO `specialty` (`uuid_specialty`, `name`) VALUES
('f2ee4107-5fbd-49bd-a101-98b14782e21e', 'Clinica'),
('201b1563-5dc9-458b-8bd1-71458f343081', 'Cognitivo-Conductual'),
('c9f211af-da87-4629-8dba-b78227496851', 'Familiar y de Pareja'),
('eb0b2f29-c4f2-4aee-a41d-f44d04926914', 'Humanista'),
('a0c64975-c73c-4dce-a2ba-3519b76c0f97', 'Infancia y Adolescencia'),
('cca7ae69-95e1-4a60-b916-de4dc9decb8d', 'Neuropsicologia'),
('284a646f-f660-48d8-960a-c3355b6e8165', 'Psicoanalitica y Psicodinamica'),
('e731a111-a4b6-43a4-82af-7c928ff4fce0', 'Sexual');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_profile`
--

CREATE TABLE `user_profile` (
  `uuid_user_profile` char(36) NOT NULL,
  `uuid_credential` char(36) NOT NULL,
  `names` varchar(100) NOT NULL,
  `surnames` varchar(100) NOT NULL,
  `doc_type` enum('cc','ce','ti','pa','ot') NOT NULL,
  `doc_number` varchar(30) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `user_profile`
--

INSERT INTO `user_profile` (`uuid_user_profile`, `uuid_credential`, `names`, `surnames`, `doc_type`, `doc_number`, `phone_number`, `country`, `created_at`, `updated_at`) VALUES
('00efe4d4-8896-4e36-a3c4-75833b017da1', '730c6e49-4021-4fd4-b3bb-2977da4b4283', 'Santiago', 'Botero Calle', 'cc', '1018965231', '+573156985210', 'Colombia', '2026-06-05 10:55:47', '2026-06-08 09:05:57'),
('107c6193-6265-4474-840b-5e17b9d1639b', '9e04f4cc-7785-4c56-96b2-371a4101569f', 'Admin', 'Sistema', 'cc', '1000000000', '+573000000000', 'Colombia', '2026-06-05 10:28:35', '2026-06-08 09:06:05'),
('205fbf07-6aaa-4e0e-8d94-35395440bf72', 'fde67efe-f061-419a-ac9a-2f9043071ddd', 'Beatriz María', 'Suaza Rua', 'cc', '18963254', '+573123551233', 'Colombia', '2026-06-05 10:55:47', '2026-06-11 19:05:48'),
('9042d7da-dacf-4022-956f-5af3a2f60ec6', 'cb6983da-fe7f-4ca8-81d7-5f8e570e2267', 'Mariana', 'Robledo Suaza', 'cc', '1006584972', '+573112654978', 'Colombia', '2026-06-05 17:17:13', '2026-06-11 18:09:19'),
('a692fb6b-d968-412d-855b-3b87ad3ea24f', '33680cd3-59d3-4e1a-a6ce-67e1f0e6c360', 'Bruno', 'Casta Brentt', 'cc', '1002564872', '+573002369845', 'Colombia', '2026-06-05 10:55:47', '2026-06-08 09:06:38'),
('a741e9e5-a15c-4854-99c8-8759abe34094', '8cecff6d-8a7c-4f46-91f4-d815ca9978c5', 'Juan', 'Montero López', 'pa', 'PN4561230', '+5072123456', 'Panamá', '2026-06-05 11:06:21', '2026-06-11 19:07:04'),
('e609f7be-c5d8-44aa-823e-17fdcf0fcf96', '7d969cee-8e0f-4b40-8c0f-3d196892202c', 'Amelia', 'Peréz Molina', 'cc', '45123978', '+573012659847', 'Colombia', '2026-06-05 10:55:47', '2026-06-10 15:02:25');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `credential`
--
ALTER TABLE `credential`
  ADD PRIMARY KEY (`uuid_credential`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`uuid_patient`),
  ADD UNIQUE KEY `uuid_user_profile` (`uuid_user_profile`);

--
-- Indices de la tabla `psychologist`
--
ALTER TABLE `psychologist`
  ADD PRIMARY KEY (`uuid_psychologist`),
  ADD UNIQUE KEY `uuid_user_profile` (`uuid_user_profile`),
  ADD UNIQUE KEY `license_number` (`license_number`);

--
-- Indices de la tabla `psychologist_specialty`
--
ALTER TABLE `psychologist_specialty`
  ADD PRIMARY KEY (`uuid_psychologist`,`uuid_specialty`),
  ADD KEY `fk_pss_specialty` (`uuid_specialty`);

--
-- Indices de la tabla `specialty`
--
ALTER TABLE `specialty`
  ADD PRIMARY KEY (`uuid_specialty`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`uuid_user_profile`),
  ADD UNIQUE KEY `doc_number` (`doc_number`),
  ADD UNIQUE KEY `uuid_credential` (`uuid_credential`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `patient`
--
ALTER TABLE `patient`
  ADD CONSTRAINT `fk_patient_user_profile` FOREIGN KEY (`uuid_user_profile`) REFERENCES `user_profile` (`uuid_user_profile`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `psychologist`
--
ALTER TABLE `psychologist`
  ADD CONSTRAINT `fk_psychologist_user_profile` FOREIGN KEY (`uuid_user_profile`) REFERENCES `user_profile` (`uuid_user_profile`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `psychologist_specialty`
--
ALTER TABLE `psychologist_specialty`
  ADD CONSTRAINT `fk_pss_psychologist` FOREIGN KEY (`uuid_psychologist`) REFERENCES `psychologist` (`uuid_psychologist`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pss_specialty` FOREIGN KEY (`uuid_specialty`) REFERENCES `specialty` (`uuid_specialty`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `fk_user_profile_credential` FOREIGN KEY (`uuid_credential`) REFERENCES `credential` (`uuid_credential`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
