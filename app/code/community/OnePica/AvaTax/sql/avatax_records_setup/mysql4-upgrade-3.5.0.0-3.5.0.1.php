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

$installer->getConnection()
    ->addColumn($installer->getTable('avatax_records/log'), 'soap_request', array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'after'    => null,
        'comment'  => 'SOAP request'
    ));

$installer->getConnection()
    ->addColumn($installer->getTable('avatax_records/log'), 'soap_request_headers', array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'after'    => null,
        'comment'  => 'SOAP request'
    ));

$installer->getConnection()
    ->addColumn($installer->getTable('avatax_records/log'), 'soap_result', array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'after'    => null,
        'comment'  => 'SOAP result'
    ));

$installer->getConnection()
    ->addColumn($installer->getTable('avatax_records/log'), 'soap_result_headers', array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'after'    => null,
        'comment'  => 'SOAP result'
    ));

$installer->endSetup();
