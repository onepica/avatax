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
$table = $adapter->newTable($this->getTable('avatax_records/log'))
    ->addColumn('log_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable'  => false,
        'primary'  => true,
        'unsigned' => true
    ))
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true
    ))
    ->addColumn('level', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50)
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50)
    ->addColumn('request', Varien_Db_Ddl_Table::TYPE_TEXT, null)
    ->addColumn('result', Varien_Db_Ddl_Table::TYPE_TEXT, null)
    ->addColumn('additional', Varien_Db_Ddl_Table::TYPE_TEXT, null)
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null)
    ->addForeignKey(
        $this->getFkName(
            $this->getTable('avatax_records/log'),
            'store_id',
            $this->getTable('core/store'),
            'store_id'
        ),
        'store_id',
        $this->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addIndex(
        $this->getIdxName(
            $this->getTable('avatax_records/log'),
            array('level'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('level'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )
    ->addIndex(
        $this->getIdxName(
            $this->getTable('avatax_records/log'),
            array('type'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('type'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )
    ->addIndex(
        $this->getIdxName(
            $this->getTable('avatax_records/log'),
            array('created_at'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('created_at'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )
    ->setComment('Used by One Pica AvaTax extension');

$adapter->createTable($table);
$this->endSetup();
