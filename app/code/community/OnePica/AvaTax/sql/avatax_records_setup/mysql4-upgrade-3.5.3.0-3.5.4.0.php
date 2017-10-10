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
 * @copyright  Copyright (c) 2015 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/** @var $this Mage_Core_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();

$colQuoteId = 'quote_id';
$colQuoteAddressId = 'quote_address_id';

$logTableName = $this->getTable('avatax_records/log');
$logCols = $installer->getConnection()->describeTable($logTableName);
if (!array_key_exists($colQuoteId, $logCols) && !array_key_exists($colQuoteAddressId, $logCols)) {
    $installer->run(
        "ALTER TABLE `" . $this->getTable('avatax_records/log') . "` 
            ADD `" . $colQuoteId . "` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Quote id',
            ADD `" . $colQuoteAddressId . "` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Quote address id';"
    );
}

$queueTableName = $this->getTable('avatax_records/queue');
$queueCols = $installer->getConnection()->describeTable($queueTableName);
if (!array_key_exists($colQuoteId, $queueCols) && !array_key_exists($colQuoteAddressId, $queueCols)) {
    $installer->run(
        "ALTER TABLE `" . $queueTableName . "` 
            ADD `" . $colQuoteId . "` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Quote id',
            ADD `" . $colQuoteAddressId . "` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Quote address id';"
    );
}

/* add avatax_quote_address_id to sales_flat_order_address */
$salesOrderAddressTableName = $this->getTable('sales/order_address');
$salesOrderAddressCols = $installer->getConnection()->describeTable($salesOrderAddressTableName);
if (!array_key_exists('avatax_quote_address_id', $salesOrderAddressCols)) {
    $installer->run(
        "ALTER TABLE `" . $salesOrderAddressTableName . "` 
            ADD `avatax_quote_address_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Quote address id';"
    );
}

$installer->endSetup();
