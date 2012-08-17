-- //
ALTER TABLE `carreras` ADD `edadeje` SMALLINT(4) UNSIGNED NOT NULL DEFAULT '0' AFTER `ejemplar`;
ALTER TABLE `carreras` ADD `edaddesde` TINYINT(4) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `carreras` ADD `edadhasta` TINYINT(4) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `carreras` ADD `sexo` VARCHAR( 4 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `carreras` ADD `ganadasdes` SMALLINT( 16 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `carreras` ADD  `ganadashas` SMALLINT( 16 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `carreras` ADD  `condicion` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE  `carreras` ADD  `apuestas` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE  `carreras` ADD  `tierecord` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE  `carreras` ADD  `totalprem1` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT  '0.00',
ADD  `totalprem2` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT  '0.00',
ADD  `totalprem3` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT  '0.00',
ADD  `totalprem4` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT  '0.00',
ADD  `totalprem5` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT  '0.00';
ALTER TABLE  `carreras` ADD  `sexocab` VARCHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
ADD  `ultimas` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
ADD  `pelo` VARCHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE  `carreras` ADD  `padre` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
ADD  `madre` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
ADD  `abuelo` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
ADD criador VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE  `carreras` ADD  `nacioel` DATE NULL;
ALTER TABLE  `carreras` ADD  `premiotota` DECIMAL( 10, 2 ) UNSIGNED NOT NULL DEFAULT  '0.00',
ADD  `condicion2` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
-- //@UNDO
ALTER TABLE `carreras` DROP `edadeje`,`edaddesde`,
	DROP `edadhasta`,
	DROP `sexo`,
	DROP `ganadasdes`,
	DROP `ganadashas`,
	DROP `condicion`,
	DROP `apuestas`,
	DROP `tierecord`,
	DROP `totalprem1`,
	DROP `totalprem2`,
	DROP `totalprem3`,
	DROP `totalprem4`,
	DROP `totalprem5`;
ALTER TABLE `carreras` DROP  `sexocab`,
	DROP `ultimas`,
	DROP `pelo`;
ALTER TABLE `carreras` DROP  `padre`,
	DROP `madre`,
	DROP `abuelo`,
	DROP `criador`,
	DROP `nacioel`;
ALTER TABLE `carreras` DROP  `premiotota`,
	DROP  `condicion2`;
-- //
