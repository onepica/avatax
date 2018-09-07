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

$tableName = $this->getTable('avatax_records/hs_code');
$columnName = 'description';

if ($conn->tableColumnExists($tableName, $columnName) === false) {
    $installer->run(
        "ALTER TABLE `" . $tableName . "`
    ADD `" . $columnName . "` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'HS code description';
    "
    );
}

$installer->endSetup();
