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

$installer->run("

CREATE TABLE `" . $this->getTable('avatax_records/log') . "` (
    `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `store_id` smallint(5) unsigned DEFAULT NULL,
    `level` varchar(50) DEFAULT NULL,
    `type` varchar(50) DEFAULT NULL,
    `request` text,
    `result` text,
    `additional` text,
    `created_at` datetime DEFAULT NULL,
    PRIMARY KEY (`log_id`),
    KEY `FK_OP_AVATAX_LOG_STORE_ID_CORE_STORE_STORE_ID` (`store_id`),
    KEY `IDX_OP_AVATAX_LOG_LEVEL` (`level`),
    KEY `IDX_OP_AVATAX_LOG_TYPE` (`type`),
    KEY `IDX_OP_AVATAX_LOG_CREATED_AT` (`created_at`),
    CONSTRAINT `FK_OP_AVATAX_LOG_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`)
      REFERENCES `" . $this->getTable('core/store') . "` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Used by One Pica AvaTax extension';

");

$this->endSetup();
