CREATE TABLE `PREFIX_adveg_email_logs` (
  `id_log` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_shop` int(11) unsigned NOT NULL,
  `form` varchar(255) DEFAULT NULL,
  `success` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `email` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_log`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_adveg_message_logs` (
  `id_log` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_shop` int(11) unsigned NOT NULL,
  `form` varchar(255) DEFAULT NULL,
  `success` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `message` text DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_log`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_adveg_recaptcha_logs` (
  `id_log` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_shop` int(11) unsigned NOT NULL,
  `form` varchar(255) DEFAULT NULL,
  `success` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `response` text DEFAULT NULL,
  `score` float(2,1) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_log`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;