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

$table = $this->getTable('avatax_records/parameter');

if ($conn->showTableStatus($table) === false) {
    $installer->run(
        "CREATE TABLE `" . $table . "` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `avalara_uom` varchar(100) DEFAULT NULL,
    `avalara_parameter_type` varchar(100) DEFAULT NULL,
    `description` text,
    `country_list` text,
    PRIMARY KEY (`id`),
    KEY `IDX_OP_AVATAX_PARAMETER_AVALARA_UOM` (`avalara_uom`)
) ENGINE=InnoDB COMMENT='Used by One Pica AvaTax extension';
    "
    );
}

$installer->endSetup();
