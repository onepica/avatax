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

CREATE TABLE `" . $this->getTable('avatax_records/queue') . "` (
    `queue_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `store_id` smallint(5) unsigned DEFAULT NULL,
    `entity_id` int(10) unsigned DEFAULT NULL,
    `entity_increment_id` varchar(50) DEFAULT NULL,
    `type` varchar(50) DEFAULT NULL,
    `status` varchar(50) DEFAULT NULL,
    `attempt` smallint(5) unsigned DEFAULT '0',
    `message` varchar(255) DEFAULT NULL,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`queue_id`),
    KEY `FK_OP_AVATAX_QUEUE_STORE_ID_CORE_STORE_STORE_ID` (`store_id`),
    KEY `IDX_OP_AVATAX_QUEUE_ENTITY_ID` (`entity_id`),
    KEY `IDX_OP_AVATAX_QUEUE_ENTITY_INCREMENT_ID` (`entity_increment_id`),
    KEY `IDX_OP_AVATAX_QUEUE_TYPE` (`type`),
    KEY `IDX_OP_AVATAX_QUEUE_STATUS` (`status`),
    KEY `IDX_OP_AVATAX_QUEUE_ATTEMPT` (`attempt`),
    KEY `IDX_OP_AVATAX_QUEUE_CREATED_AT` (`created_at`),
    KEY `IDX_OP_AVATAX_QUEUE_UPDATED_AT` (`updated_at`),
    CONSTRAINT `FK_OP_AVATAX_QUEUE_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`)
        REFERENCES `" . $this->getTable('core/store') . "` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Used by One Pica AvaTax extension';

");

$this->endSetup();
