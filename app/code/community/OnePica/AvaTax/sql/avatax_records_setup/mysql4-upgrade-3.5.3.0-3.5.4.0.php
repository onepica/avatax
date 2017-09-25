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

$tblName = $this->getTable('avatax_records/log');
$allCols = $installer->getConnection()->describeTable($tblName);

if (!array_key_exists('quote_id', $allCols) && !array_key_exists('quote_address_id', $allCols)) {
    $installer->run(
        "ALTER TABLE `" . $tblName . "` 
            ADD `quote_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Quote id',
            ADD `quote_address_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Quote address id';"
    );
}

$installer->endSetup();
