-- Rename table to more appropriate name.
RENAME TABLE `PREFIX_adveg_guard_logs` TO `PREFIX_adveg_email_logs`;

-- Remove useless date_upd column.
ALTER TABLE `PREFIX_adveg_email_logs` DROP COLUMN `date_upd`;

-- Create new log tables
CREATE TABLE `PREFIX_adveg_message_logs` (
  `id_log` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `phrases` text DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_log`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_adveg_recaptcha_logs` (
  `id_log` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_log`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;