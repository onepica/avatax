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

$installer->run("CREATE TABLE IF NOT EXISTS `" . $this->getTable('avatax_records/log') . "` (
	`log_id` INT UNSIGNED AUTO_INCREMENT,
	`store_id` SMALLINT (5) UNSIGNED,
	`level` VARCHAR (50),
	`type` VARCHAR (50),
	`request` TEXT,
	`result` TEXT,
	`additional` TEXT,
	`created_at` DATETIME,
	PRIMARY KEY(`log_id`)) COMMENT = 'Used by One Pica AvaTax extension' ENGINE = InnoDB");

$this->endSetup();
