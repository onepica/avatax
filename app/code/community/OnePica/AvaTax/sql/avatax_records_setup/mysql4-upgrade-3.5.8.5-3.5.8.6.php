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

$attributeCode = 'customer_avatax_lc_seller_is_importer';
$attributeComment = 'Avatax Landed Cost Seller Is Importer Of Record';

$tables = array(
    'quote' => 'sales_flat_quote',
);

$ver = Mage::getVersionInfo();

if ($ver['minor'] < 6 || $ver['minor'] == 10) {
    $adapter = $installer->getConnection();
    $sqlCommands = "";

    foreach ($tables as $tableCode => $table) {
        $tableName = $this->getTable($table);

        /* check if column already exists */
        if ($conn->tableColumnExists($tableName, $attributeCode) === false) {
            $sqlCommands .= "ALTER TABLE `" . $tableName . "`
                   ADD COLUMN   `" . $attributeCode . "` int(11) DEFAULT NULL COMMENT '" . $attributeComment . "';
                ";
        }
    }

    $installer->run($sqlCommands);
} else {
    /** @var Mage_Sales_Model_Resource_Setup $setup */
    $setup = Mage::getModel('sales/resource_setup', 'core_setup');

    foreach ($tables as $tableCode => $table) {
        $tableName = $this->getTable($table);

        /* check if column already exists */
        if ($conn->tableColumnExists($tableName, $attributeCode) === false) {
            $setup->addAttribute($tableCode, $attributeCode, array('type' => 'int'));
        }
    }
}

$installer->endSetup();
