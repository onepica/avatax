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

$installer->run(
    "
    ALTER TABLE `" . $this->getTable('avatax_records/log') . "`
    ADD `soap_request` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'SOAP request',  
    ADD `soap_request_headers` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL 
        COMMENT 'SOAP request headers', 
    ADD `soap_result` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'SOAP result' , 
    ADD `soap_result_headers` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL 
        COMMENT 'SOAP result headers' , 
    CHANGE COLUMN `request` `request` MEDIUMTEXT NULL COMMENT 'Request';
    "
);

$installer->endSetup();
