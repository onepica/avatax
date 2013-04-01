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


class OnePica_AvaTax_Model_Records_Mysql4_Queue extends Mage_Core_Model_Mysql4_Abstract
{	
    protected function _construct() {
        $this->_init('avatax_records/queue', 'id');
    }
    
	/**
	 * Sets various dates before the model is saved.
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return OnePica_AvaTax_Model_Mysql4_Queue
	 */
    protected function _beforeSave(Mage_Core_Model_Abstract $object) {
        if(!$object->hasCreatedAt()) {
        	$object->setCreatedAt(gmdate('Y-m-d H:i:s'));
        }
        $object->setUpdatedAt(gmdate('Y-m-d H:i:s'));
        return $this;
    }
    
	/**
	 * Log the save
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return OnePica_AvaTax_Model_Mysql4_Queue
	 */
    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        $logStatus = Mage::getStoreConfig('tax/avatax/log_status', $object->getStoreId());
        if($logStatus) {
			if (in_array('Queue', Mage::helper('avatax')->getLogType($storeId)))
			{
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
	 * @return OnePica_AvaTax_Model_Mysql4_Queue
	 */
    protected function _afterDelete(Mage_Core_Model_Abstract $object) {
        $logStatus = Mage::getStoreConfig('tax/avatax/log_status', $object->getStoreId());
        if($logStatus) {
			if (in_array('Queue', Mage::helper('avatax')->getLogType($storeId)))
			{
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
    
}
