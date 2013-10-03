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


class OnePica_AvaTax_Model_Records_Queue extends Mage_Core_Model_Abstract
{
	const QUEUE_TYPE_INVOICE		= 'Invoice';
	const QUEUE_TYPE_CREDITMEMEO	= 'Credit memo';
	
	const QUEUE_STATUS_PENDING		= 'Pending';
	const QUEUE_STATUS_RETRY		= 'Retry pending';
	const QUEUE_STATUS_FAILED		= 'Failed';
	const QUEUE_STATUS_COMPLETE		= 'Complete';
	const QUEUE_STATUS_UNBALANCED	= 'Unbalanced';
	
	public function _construct()
	{
		parent::_construct();
		$this->_init('avatax_records/queue');
	}
    
    public function setEntity($object) {
    	$this->setEntityId($object->getId());
    	$this->setEntityIncrementId($object->getIncrementId());
    	$this->setStoreId($object->getStoreId());
    	return $this;
    }
    
    public function getTypeOptions() {
    	return array(
    		self::QUEUE_TYPE_INVOICE		=> self::QUEUE_TYPE_INVOICE,
    		self::QUEUE_TYPE_CREDITMEMEO	=> self::QUEUE_TYPE_CREDITMEMEO
    	);
    }
    
    public function getStatusOptions() {
    	return array(
    		self::QUEUE_STATUS_PENDING		=> self::QUEUE_STATUS_PENDING,
    		self::QUEUE_STATUS_RETRY		=> self::QUEUE_STATUS_RETRY,
    		self::QUEUE_STATUS_FAILED		=> self::QUEUE_STATUS_FAILED,
    		self::QUEUE_STATUS_COMPLETE		=> self::QUEUE_STATUS_COMPLETE,
    		self::QUEUE_STATUS_UNBALANCED	=> self::QUEUE_STATUS_UNBALANCED
    	);
    }

    public function loadInvoiceByIncrementId($incrementId)
    {
        $this->_getResource()->loadInvoiceByIncrementId($this, $incrementId);
        $this->_afterLoad();
        $this->setOrigData();

        return $this;
    }
}