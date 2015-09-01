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
/* @var $this Mage_Core_Model_Resource_Setup */
$adapter = $this->getConnection();

// check if current process is install from scratch or update from 2.4.3.3-stable
$queueColumns = array_keys($adapter->describeTable($this->getTable('avatax_records/queue')));
if (!in_array('queue_id', $queueColumns)) {
    $installer->run("

    ALTER TABLE `" . $this->getTable('avatax_records/log') . "`
	CHANGE COLUMN `log_id` `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	CHANGE COLUMN `store_id` `store_id` smallint(5) unsigned DEFAULT NULL,
	CHANGE COLUMN `level` `level` varchar(50) DEFAULT NULL,
    CHANGE COLUMN `type` `type` varchar(50) DEFAULT NULL,
    CHANGE COLUMN `request` `request` text,
    CHANGE COLUMN `result` `result` text,
    CHANGE COLUMN `additional` `additional` text,
    CHANGE COLUMN `created_at` `created_at` datetime DEFAULT NULL,
    ADD KEY `FK_OP_AVATAX_LOG_STORE_ID_CORE_STORE_STORE_ID` (`store_id`),
    ADD KEY `IDX_OP_AVATAX_LOG_LEVEL` (`level`),
    ADD KEY `IDX_OP_AVATAX_LOG_TYPE` (`type`),
    ADD KEY `IDX_OP_AVATAX_LOG_CREATED_AT` (`created_at`),
    ADD CONSTRAINT `FK_OP_AVATAX_LOG_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`)
        REFERENCES `" . $this->getTable('core/store') . "` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;

    ALTER TABLE `" . $this->getTable('avatax_records/queue') . "`
    CHANGE COLUMN `id` `queue_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    CHANGE COLUMN `store_id` `store_id` smallint(5) unsigned DEFAULT NULL,
    CHANGE COLUMN `entity_id` `entity_id` int(10) unsigned DEFAULT NULL,
    CHANGE COLUMN `entity_increment_id` `entity_increment_id` varchar(50) DEFAULT NULL,
    CHANGE COLUMN `type` `type` varchar(50) DEFAULT NULL,
    CHANGE COLUMN `status` `status` varchar(50) DEFAULT NULL,
    CHANGE COLUMN `attempt` `attempt` smallint(5) unsigned DEFAULT '0',
    CHANGE COLUMN `message` `message` varchar(255) DEFAULT NULL,
    CHANGE COLUMN `created_at` `created_at` datetime DEFAULT NULL,
    CHANGE COLUMN `updated_at` `updated_at` datetime DEFAULT NULL,
    ADD KEY `FK_OP_AVATAX_QUEUE_STORE_ID_CORE_STORE_STORE_ID` (`store_id`),
    ADD KEY `IDX_OP_AVATAX_QUEUE_ENTITY_ID` (`entity_id`),
    ADD KEY `IDX_OP_AVATAX_QUEUE_ENTITY_INCREMENT_ID` (`entity_increment_id`),
    ADD KEY `IDX_OP_AVATAX_QUEUE_TYPE` (`type`),
    ADD KEY `IDX_OP_AVATAX_QUEUE_STATUS` (`status`),
    ADD KEY `IDX_OP_AVATAX_QUEUE_ATTEMPT` (`attempt`),
    ADD KEY `IDX_OP_AVATAX_QUEUE_CREATED_AT` (`created_at`),
    ADD KEY `IDX_OP_AVATAX_QUEUE_UPDATED_AT` (`updated_at`),
    ADD CONSTRAINT `FK_OP_AVATAX_QUEUE_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`)
        REFERENCES `" . $this->getTable('core/store') . "` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;

    ");
}

$this->endSetup();
