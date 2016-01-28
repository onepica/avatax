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
 *
 * @method OnePica_AvaTax_Model_Records_Mysql4_Queue _getResource()
 */

/**
 * Queue model
 *
 * @method int getAttempt()
 * @method $this setAttempt(int $attempt)
 * @method string getStatus()
 * @method $this setStatus(string $status)
 * @method string getMessage()
 * @method $this setMessage(string $message)
 * @method $this setEntityId(int $entityId)
 * @method $this setEntityIncrementId(int $entityIncrementId)
 * @method int getEntityIncrementId()
 * @method $this setStoreId(int $storeId)
 * @method int getStoreId()
 * @method OnePica_AvaTax_Model_Records_Mysql4_Queue _getResource()
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Records_Queue extends Mage_Core_Model_Abstract
{
    /**
     * Invoice type
     */
    const QUEUE_TYPE_INVOICE = 'Invoice';

    /**
     * Credit memo type
     */
    const QUEUE_TYPE_CREDITMEMEO = 'Credit memo';

    /**
     * Pending status
     */
    const QUEUE_STATUS_PENDING = 'Pending';

    /**
     * Retry status
     */
    const QUEUE_STATUS_RETRY = 'Retry pending';

    /**
     * Failed status
     */
    const QUEUE_STATUS_FAILED = 'Failed';

    /**
     * Complete status
     */
    const QUEUE_STATUS_COMPLETE = 'Complete';

    /**
     * Unbalanced status
     */
    const QUEUE_STATUS_UNBALANCED = 'Unbalanced';

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('avatax_records/queue');
    }

    /**
     * Set entity
     *
     * @param Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo $object
     * @return $this
     */
    public function setEntity($object)
    {
        $this->setEntityId($object->getId());
        $this->setEntityIncrementId($object->getIncrementId());
        $this->setStoreId($object->getStoreId());

        return $this;
    }

    /**
     * Get type options
     *
     * @return array
     */
    public function getTypeOptions()
    {
        return array(
            self::QUEUE_TYPE_INVOICE     => self::QUEUE_TYPE_INVOICE,
            self::QUEUE_TYPE_CREDITMEMEO => self::QUEUE_TYPE_CREDITMEMEO
        );
    }

    /**
     * Get status options
     *
     * @return array
     */
    public function getStatusOptions()
    {
        return array(
            self::QUEUE_STATUS_PENDING    => self::QUEUE_STATUS_PENDING,
            self::QUEUE_STATUS_RETRY      => self::QUEUE_STATUS_RETRY,
            self::QUEUE_STATUS_FAILED     => self::QUEUE_STATUS_FAILED,
            self::QUEUE_STATUS_COMPLETE   => self::QUEUE_STATUS_COMPLETE,
            self::QUEUE_STATUS_UNBALANCED => self::QUEUE_STATUS_UNBALANCED
        );
    }

    /**
     * Load invoice by increment id
     *
     * @param int $incrementId
     * @return $this
     */
    public function loadInvoiceByIncrementId($incrementId)
    {
        $this->_getResource()->loadInvoiceByIncrementId($this, $incrementId);
        $this->_afterLoad();
        $this->setOrigData();

        return $this;
    }
}
