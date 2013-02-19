<?php
/**
 * OnePica_AvaTax
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0), a
 * copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 * @copyright  Copyright (c) 2009 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */


$installer = $this;

$this->startSetup();

$installer->run("CREATE TABLE IF NOT EXISTS `" . $this->getTable('avatax_records/queue') . "` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`store_id` SMALLINT(5) UNSIGNED NULL DEFAULT NULL,
	`entity_id` INT(10) UNSIGNED NOT NULL,
	`entity_increment_id` VARCHAR(50) NOT NULL,
	`type` ENUM('Invoice','Credit memo') NOT NULL,
	`status` ENUM('Pending','Retry pending','Failed','Complete','Unbalanced') NOT NULL DEFAULT 'pending',
	`attempt` TINYINT UNSIGNED NOT NULL DEFAULT '0',
	`message` VARCHAR (255) NULL DEFAULT NULL,
	`created_at` DATETIME NOT NULL,
	`updated_at` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
) COMMENT = 'Used by One Pica AvaTax extension' ENGINE = InnoDB");

$this->endSetup();