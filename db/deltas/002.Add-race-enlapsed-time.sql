-- //
ALTER TABLE `race` ADD `time_enlapsed` VARCHAR(16) NULL DEFAULT NULL AFTER `raced`;
-- //@UNDO
ALTER TABLE `race` DROP `time_enlapsed`;
-- // 
