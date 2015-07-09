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
$table = $adapter->newTable($this->getTable('avatax_records/queue'))
    ->addColumn('queue_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable'  => false,
        'primary'  => true,
        'unsigned' => true
    ))
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true
    ))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true
    ))
    ->addColumn('entity_increment_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50)
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50)
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50)
    ->addColumn('attempt', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        'default' => 0
    ))
    ->addColumn('message', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null)
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null)
    ->addForeignKey(
        $this->getFkName(
            $this->getTable('avatax_records/queue'),
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
            $this->getTable('avatax_records/queue'),
            array('entity_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('entity_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )
    ->addIndex(
        $this->getIdxName(
            $this->getTable('avatax_records/queue'),
            array('entity_increment_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('entity_increment_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )
    ->addIndex(
        $this->getIdxName(
            $this->getTable('avatax_records/queue'),
            array('type'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('type'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )
    ->addIndex(
        $this->getIdxName(
            $this->getTable('avatax_records/queue'),
            array('status'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('status'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )
    ->addIndex(
        $this->getIdxName(
            $this->getTable('avatax_records/queue'),
            array('attempt'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('attempt'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )
    ->addIndex(
        $this->getIdxName(
            $this->getTable('avatax_records/queue'),
            array('created_at'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('created_at'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )
    ->addIndex(
        $this->getIdxName(
            $this->getTable('avatax_records/queue'),
            array('updated_at'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('updated_at'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )
    ->setComment('Used by One Pica AvaTax extension');
$adapter->createTable($table);
$this->endSetup();
