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

$hsCodeTableName = $this->getTable('avatax_records/hs_code');
if ($conn->showTableStatus($hsCodeTableName) === false) {
    $installer->run(
        "CREATE TABLE `" . $hsCodeTableName . "` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `hs_code` varchar(10) UNIQUE NOT NULL,
            PRIMARY KEY (`id`),
            KEY `IDX_OP_AVATAX_HS_CODE` (`hs_code`)
        ) ENGINE=InnoDB COMMENT='HS Codes (Used by OnePica AvaTax extension)';
            "
    );
}

$hsCodeCountryTableName = $this->getTable('avatax_records/hs_code_country');
if ($conn->showTableStatus($hsCodeCountryTableName) === false) {
    $installer->run(
        "CREATE TABLE `" . $hsCodeCountryTableName . "` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `hs_id` int(10) unsigned NOT NULL,
            `hs_full_code` varchar(10) UNIQUE NOT NULL,
            `country_codes` varchar(255),
            PRIMARY KEY (`id`),
            KEY `FK_OP_AVATAX_HS_CODE_COUNTRY_HS_ID_AVATAX_HS_CODE_ID` (`hs_id`),
            KEY `IDX_OP_AVATAX_HS_CODE_COUNTRY_HS_FULL_CODE` (`hs_full_code`),
            KEY `IDX_OP_AVATAX_HS_CODE_COUNTRY_COUNTRY_CODES` (`country_codes`),
            CONSTRAINT `FK_OP_AVATAX_HS_CODE_COUNTRY_HS_ID_AVATAX_HS_CODE_ID` FOREIGN KEY (`hs_id`)
              REFERENCES `" . $hsCodeTableName . "` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB COMMENT='HS Codes for Countries (Used by OnePica AvaTax extension)';
            "
    );
}

$installer->endSetup();
