-- //
ALTER TABLE `carreras` ADD `horario` VARCHAR(16) NOT NULL;
-- //@UNDO
ALTER TABLE `carreras` DROP `horario`;
-- // 
