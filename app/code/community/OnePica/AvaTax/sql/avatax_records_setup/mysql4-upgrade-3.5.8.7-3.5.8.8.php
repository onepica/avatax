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

/** @var \Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();
/** @var \Varien_Db_Adapter_Pdo_Mysql $conn */
$conn = $installer->getConnection();

$attributesToAdd = array(
    'avatax_fixed_tax_amount'      => 'Avatax Fixed Tax Amount',
    'base_avatax_fixed_tax_amount' => 'Base Avatax Fixed Tax Amount'
);

$tables = array(
    'quote_address' => 'sales_flat_quote_address',
    'quote_item'    => 'sales_flat_quote_item',
    'order'         => 'sales_flat_order',
    'order_item'    => 'sales_flat_order_item',
    'invoice'         => 'sales_flat_invoice',
    'invoice_item'    => 'sales_flat_invoice_item',
    'creditmemo'      => 'sales_flat_creditmemo',
    'creditmemo_item' => 'sales_flat_creditmemo_item',
);

$ver = Mage::getVersionInfo();

if ($ver['minor'] < 6 || $ver['minor'] == 10) {
    $adapter = $installer->getConnection();
    $sqlCommands = "";

    foreach ($tables as $tableCode => $table) {
        $tableName = $this->getTable($table);

        foreach ($attributesToAdd as $attributeCode => $attributeComment) {
            /* check if column already exists */
            if ($conn->tableColumnExists($tableName, $attributeCode) === false) {
                $sqlCommands .= "ALTER TABLE `" . $tableName . "`
                   ADD COLUMN   `" . $attributeCode . "` decimal(12,4) DEFAULT NULL COMMENT '" . $attributeComment . "';
                ";
            }
        }
    }

    $installer->run($sqlCommands);
} else {
    /** @var Mage_Sales_Model_Resource_Setup $setup */
    $setup = Mage::getModel('sales/resource_setup', 'core_setup');

    foreach ($tables as $tableCode => $table) {
        $tableName = $this->getTable($table);

        foreach ($attributesToAdd as $attributeCode => $attributeComment) {
            /* check if column already exists */
            if ($conn->tableColumnExists($tableName, $attributeCode) === false) {
                $setup->addAttribute($tableCode, $attributeCode, array('type' => 'decimal'));
            }
        }
    }
}

$installer->endSetup();
