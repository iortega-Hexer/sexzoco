--
-- Structure de la table `ps_pfg`
--

-- ATTENTION, si modification des tables, ne pas oublier le duplicate

DROP TABLE IF EXISTS `ps_pfg`;
CREATE TABLE IF NOT EXISTS `ps_pfg` (
  `id_pfg` int(11) NOT NULL AUTO_INCREMENT,
  `send_mail_to` varchar(255) NOT NULL,
  `action_sender` enum('form','message') DEFAULT NULL,
  `action_admin` enum('form','message') DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  `is_only_connected` tinyint(1) NOT NULL,
  `accessible` tinyint(1) NOT NULL,
  `recaptcha_public` varchar(255) DEFAULT NULL,
  `recaptcha_private` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_pfg`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `ps_pfg_submissions`
--

DROP TABLE IF EXISTS `ps_pfg_submissions`;
CREATE TABLE IF NOT EXISTS `ps_pfg_submissions` (
  `id_submission` int(11) NOT NULL AUTO_INCREMENT,
  `id_pfg` int(11) NOT NULL,
  `entry` text NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_submission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `ps_pfg_fields`
--

DROP TABLE IF EXISTS `ps_pfg_fields`;
CREATE TABLE IF NOT EXISTS `ps_pfg_fields` (
  `id_field` int(11) NOT NULL AUTO_INCREMENT,
  `id_pfg` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `required` tinyint(1) NOT NULL,
  `classname` varchar(255) DEFAULT NULL,
  `style` varchar(255) DEFAULT NULL,
  `extra` varchar(255) DEFAULT NULL,
  `related` enum('email','subject','newsletter') DEFAULT NULL,
  `position` tinyint(4) DEFAULT NULL DEFAULT '0',
  PRIMARY KEY (`id_field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `ps_pfg_field_lang`
--

DROP TABLE IF EXISTS `ps_pfg_fields_lang`;
CREATE TABLE IF NOT EXISTS `ps_pfg_fields_lang` (
  `id_field_lang` int(11) NOT NULL AUTO_INCREMENT,
  `id_field` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `values` text NOT NULL,
  PRIMARY KEY (`id_field_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `ps_pfg_lang`
--

DROP TABLE IF EXISTS `ps_pfg_lang`;
CREATE TABLE IF NOT EXISTS `ps_pfg_lang` (
  `id_pfg_lang` int(11) NOT NULL AUTO_INCREMENT,
  `id_pfg` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subject_sender` VARCHAR( 255 ) NULL DEFAULT NULL,
  `subject_admin` VARCHAR( 255 ) NULL DEFAULT NULL,
  `header` text NOT NULL COMMENT 'Message to display before the form',
  `footer` text NOT NULL COMMENT 'Message to display after the form',
  `success` text NOT NULL COMMENT 'Success message after submission',
  `send_label` varchar(255) NULL,
  `message_sender` text COMMENT 'Message to send to the sender, if the parameters is set to message',
  `message_admin` text COMMENT 'Message to send to the admins, if the parameters is set to message',
  `unauth_redirect_url` varchar(255) NULL,
  PRIMARY KEY (`id_pfg_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `ps_pfg_shop`
--

DROP TABLE IF EXISTS `ps_pfg_shop`;
CREATE TABLE IF NOT EXISTS `ps_pfg_shop` (
  `id_pfg` int(11) NOT NULL,
  `id_shop` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
