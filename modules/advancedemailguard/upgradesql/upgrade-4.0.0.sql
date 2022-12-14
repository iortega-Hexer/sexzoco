ALTER TABLE `PREFIX_adveg_email_logs` CHANGE COLUMN `section` `form` varchar(255) DEFAULT NULL;
ALTER TABLE `PREFIX_adveg_message_logs` CHANGE COLUMN `section` `form` varchar(255) DEFAULT NULL;
ALTER TABLE `PREFIX_adveg_message_logs` CHANGE COLUMN `phrases` `text` varchar(255) DEFAULT NULL;
ALTER TABLE `PREFIX_adveg_recaptcha_logs` CHANGE COLUMN `section` `form` varchar(255) DEFAULT NULL;
ALTER TABLE `PREFIX_adveg_email_logs` ADD COLUMN `id_shop` int(11) unsigned NOT NULL AFTER `id_log`;
ALTER TABLE `PREFIX_adveg_message_logs` ADD COLUMN `id_shop` int(11) unsigned NOT NULL AFTER `id_log`;
ALTER TABLE `PREFIX_adveg_recaptcha_logs` ADD COLUMN `id_shop` int(11) unsigned NOT NULL AFTER `id_log`;
ALTER TABLE `PREFIX_adveg_email_logs` ADD COLUMN `success` tinyint(1) unsigned NOT NULL DEFAULT 0 AFTER `form`;
ALTER TABLE `PREFIX_adveg_message_logs` ADD COLUMN `success` tinyint(1) unsigned NOT NULL DEFAULT 0 AFTER `form`;
ALTER TABLE `PREFIX_adveg_recaptcha_logs` ADD COLUMN `success` tinyint(1) unsigned NOT NULL DEFAULT 0 AFTER `form`;
ALTER TABLE `PREFIX_adveg_recaptcha_logs` ADD COLUMN `response` text DEFAULT NULL AFTER `success`;