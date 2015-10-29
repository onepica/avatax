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
     * Saved result
     */
    const SAVED_RESULT = 'Saved';

    /**
     * Deleted result
     */
    const DELETED_RESULT = 'Deleted';

    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init('avatax_records/queue', 'queue_id');
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
            $object->setCreatedAt($this->_getDateModel()->gmtDate('Y-m-d H:i:s'));
        }
        $object->setUpdatedAt($this->_getDateModel()->gmtDate('Y-m-d H:i:s'));
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
        $this->_logAction($object, self::SAVED_RESULT);
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
        $this->_logAction($object, self::DELETED_RESULT);
        return $this;
    }

    /**
     * Log action
     *
     * @param Mage_Core_Model_Abstract $object
     * @param string $result
     * @return $this
     */
    protected function _logAction(Mage_Core_Model_Abstract $object, $result)
    {
        $storeId = $object->getStoreId();
        $logStatus = Mage::getStoreConfig('tax/avatax/log_status', $storeId);
        if ($logStatus) {
            $logTypes = Mage::helper('avatax')->getLogType($storeId);
            if (in_array(OnePica_AvaTax_Model_Source_Avatax_Logtype::QUEUE, $logTypes)) {
                Mage::getModel('avatax_records/log')
                    ->setStoreId($object->getStoreId())
                    ->setLevel(OnePica_AvaTax_Model_Records_Log::LOG_LEVEL_SUCCESS)
                    ->setType(OnePica_AvaTax_Model_Source_Avatax_Logtype::QUEUE)
                    ->setRequest(print_r($object->getData(), true))
                    ->setResult($result)
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

    /**
     * Get core date model
     *
     * @return Mage_Core_Model_Date
     */
    protected function _getDateModel()
    {
        return Mage::getSingleton('core/date');
    }
}
