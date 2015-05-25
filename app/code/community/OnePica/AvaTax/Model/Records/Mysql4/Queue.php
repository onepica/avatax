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

/**
 * Queue resource model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Records_Mysql4_Queue extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init('avatax_records/queue', 'id');
    }

    /**
     * Sets various dates before the model is saved.
     *
     * @param Mage_Core_Model_Abstract $object
     * @return $this
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->hasCreatedAt()) {
            $object->setCreatedAt(gmdate('Y-m-d H:i:s'));
        }
        $object->setUpdatedAt(gmdate('Y-m-d H:i:s'));
        return $this;
    }

    /**
     * Log the save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return $this
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $storeId = $object->getStoreId();
        $logStatus = Mage::getStoreConfig('tax/avatax/log_status', $storeId);
        if ($logStatus) {
            $logTypes = Mage::helper('avatax')->getLogType($storeId);
            if (in_array('Queue', $logTypes)) {
                Mage::getModel('avatax_records/log')
                    ->setStoreId($object->getStoreId())
                    ->setLevel('Success')
                    ->setType('Queue')
                    ->setRequest(print_r($object->getData(), true))
                    ->setResult('Saved')
                    ->save();
            }
        }
        return $this;
    }

    /**
     * Log the delete
     *
     * @param Mage_Core_Model_Abstract $object
     * @return $this
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        $storeId = $object->getStoreId();
        $logStatus = Mage::getStoreConfig('tax/avatax/log_status', $storeId);
        if ($logStatus) {
            $logTypes = Mage::helper('avatax')->getLogType($storeId);
            if (in_array('Queue', $logTypes)) {
                Mage::getModel('avatax_records/log')
                    ->setStoreId($object->getStoreId())
                    ->setLevel('Success')
                    ->setType('Queue')
                    ->setRequest(print_r($object->getData(), true))
                    ->setResult('Deleted')
                    ->save();
            }
        }
        return $this;
    }

    /**
     * Load invoice by increment id
     *
     * @param OnePica_AvaTax_Model_Records_Queue $queue
     * @param int $invoiceIncrementId
     * @return $this
     */
    public function loadInvoiceByIncrementId($queue, $invoiceIncrementId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getMainTable())
            ->where('entity_increment_id = ?', $invoiceIncrementId)
            ->where('type = ?', OnePica_AvaTax_Model_Records_Queue::QUEUE_TYPE_INVOICE);

        $data = $adapter->fetchRow($select);
        $queue->setData($data);

        return $this;
    }
}
