-- Add new score column to the recaptcha logs table
ALTER TABLE `PREFIX_adveg_recaptcha_logs` ADD COLUMN `score` float(2,1) DEFAULT NULL AFTER `user_agent`;