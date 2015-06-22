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

// check if current process is install from scratch or update from 2.4.3.3-stable
$queueColumns = array_keys($adapter->describeTable($this->getTable('avatax_records/queue')));
if (!in_array('queue_id', $queueColumns)) {
    $adapter
        ->modifyColumn(
            $this->getTable('avatax_records/log'),
            'log_id',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity' => true,
                'nullable'  => false,
                'primary'  => true,
                'unsigned' => true
            )
        )
        ->modifyColumn(
            $this->getTable('avatax_records/log'),
            'store_id',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned' => true
            )
        )
        ->modifyColumn(
            $this->getTable('avatax_records/log'),
            'level',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length' => 50
            )
        )
        ->modifyColumn(
            $this->getTable('avatax_records/log'),
            'type',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length' => 50
            )
        )
        ->modifyColumn(
            $this->getTable('avatax_records/log'),
            'request',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            )
        )
        ->modifyColumn(
            $this->getTable('avatax_records/log'),
            'result',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            )
        )
        ->modifyColumn(
            $this->getTable('avatax_records/log'),
            'additional',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            )
        )
        ->modifyColumn(
            $this->getTable('avatax_records/log'),
            'created_at',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
            )
        );
    $adapter
        ->addForeignKey(
            $this->getFkName(
                $this->getTable('avatax_records/log'),
                'store_id',
                $this->getTable('core/store'),
                'store_id'
            ),
            $this->getTable('avatax_records/log'),
            'store_id',
            $this->getTable('core/store'),
            'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        );
    $adapter->addIndex(
        $this->getTable('avatax_records/log'),
        $this->getIdxName(
            $this->getTable('avatax_records/log'),
            array('level'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('level'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    );
    $adapter->addIndex(
        $this->getTable('avatax_records/log'),
        $this->getIdxName(
            $this->getTable('avatax_records/log'),
            array('type'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('type'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    );
    $adapter->addIndex(
        $this->getTable('avatax_records/log'),
        $this->getIdxName(
            $this->getTable('avatax_records/log'),
            array('created_at'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('created_at'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    );

    $result = $adapter->changeColumn(
        $this->getTable('avatax_records/queue'),
        'id',
        'queue_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'identity' => true,
            'nullable'  => false,
            'primary'  => true,
            'unsigned' => true
        )
    );

    $adapter
        ->modifyColumn(
            $this->getTable('avatax_records/queue'),
            'store_id',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned' => true
            )
        )
        ->modifyColumn(
            $this->getTable('avatax_records/queue'),
            'entity_id',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned' => true
            )
        )
        ->modifyColumn(
            $this->getTable('avatax_records/queue'),
            'entity_increment_id',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length' => 50
            )
        )
        ->modifyColumn(
            $this->getTable('avatax_records/queue'),
            'type',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length' => 50
            )
        )
        ->modifyColumn(
            $this->getTable('avatax_records/queue'),
            'status',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length' => 50
            )
        )
        ->modifyColumn(
            $this->getTable('avatax_records/queue'),
            'attempt',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned' => true,
                'default' => 0
            )
        )
        ->modifyColumn(
            $this->getTable('avatax_records/queue'),
            'message',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length' => 255
            )
        )
        ->modifyColumn(
            $this->getTable('avatax_records/queue'),
            'created_at',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
            )
        )
        ->modifyColumn(
            $this->getTable('avatax_records/queue'),
            'updated_at',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
            )
        );
    $adapter->addForeignKey(
        $this->getFkName(
            $this->getTable('avatax_records/queue'),
            'store_id',
            $this->getTable('core/store'),
            'store_id'
        ),
        $this->getTable('avatax_records/queue'),
        'store_id',
        $this->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
    $adapter->addIndex(
        $this->getTable('avatax_records/queue'),
        $this->getIdxName(
            $this->getTable('avatax_records/queue'),
            array('entity_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('entity_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    );
    $adapter->addIndex(
        $this->getTable('avatax_records/queue'),
        $this->getIdxName(
            $this->getTable('avatax_records/queue'),
            array('entity_increment_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('entity_increment_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    );
    $adapter->addIndex(
        $this->getTable('avatax_records/queue'),
        $this->getIdxName(
            $this->getTable('avatax_records/queue'),
            array('type'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('type'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    );
    $adapter->addIndex(
        $this->getTable('avatax_records/queue'),
        $this->getIdxName(
            $this->getTable('avatax_records/queue'),
            array('status'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('status'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    );
    $adapter->addIndex(
        $this->getTable('avatax_records/queue'),
        $this->getIdxName(
            $this->getTable('avatax_records/queue'),
            array('attempt'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('attempt'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    );
    $adapter->addIndex(
        $this->getTable('avatax_records/queue'),
        $this->getIdxName(
            $this->getTable('avatax_records/queue'),
            array('created_at'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('created_at'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    );
    $adapter->addIndex(
        $this->getTable('avatax_records/queue'),
        $this->getIdxName(
            $this->getTable('avatax_records/queue'),
            array('updated_at'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('updated_at'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    );
}

$this->endSetup();
