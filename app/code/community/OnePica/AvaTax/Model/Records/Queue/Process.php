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

class OnePica_AvaTax_Model_Records_Queue_Process extends OnePica_AvaTax_Model_Abstract
{
    /**
     * Remove the Failed process
     *
     * @return self
     */
	public function clear() {
		$queue = Mage::getModel('avatax_records/queue')->getCollection()
			->addFieldToFilter('status', OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_FAILED);
					
		foreach($queue as $item) {
			$item->delete();
		}

    	return $this;
	}

    /**
     * Run the complete process
     *
     * @return self
     */
	public function run() {
		$this->_cleanCompleted()
			->_cleanFailed()
    		->_parseInvoices()
    		->_parseCreditMemos();
    	return $this;
	}
	
    /**
     * Delete any queue items that have been completed. Items stay in queue for some
     * transparency into the process.
     *
     * @return self
     */
	protected function _cleanCompleted() {
		$days = intval(Mage::getStoreConfig('tax/avatax/queue_success_lifetime'));
		$queue = Mage::getModel('avatax_records/queue')->getCollection()
					->addFieldToFilter('status', OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_COMPLETE)
					->addFieldToFilter('updated_at', array('lt' => gmdate('Y-m-d H:i:s', strtotime('-' . $days . ' days'))));
					
		foreach($queue as $item) {
			$item->delete();
		}
		
		return $this;
	}
	
    /**
     * Delete any queue items that have failed. Items stay in queue for some
     * transparency into the process.
     *
     * @return self
     */
	protected function _cleanFailed() {
		$days = intval(Mage::getStoreConfig('tax/avatax/queue_failed_lifetime'));
		$queue = Mage::getModel('avatax_records/queue')->getCollection()
					->addFieldToFilter('status', OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_FAILED)
					->addFieldToFilter('updated_at', array('lt' => gmdate('Y-m-d H:i:s', strtotime('-' . $days . ' days'))));
					
		foreach($queue as $item) {
			$item->delete();
		}
		
		return $this;
	}
	
    /**
     * Attempt to send any pending invoices to Avalara
     *
     * @return self
     */
	protected function _parseInvoices() {
		$queue = Mage::getModel('avatax_records/queue')->getCollection()
					->addFieldToFilter('type', OnePica_AvaTax_Model_Records_Queue::QUEUE_TYPE_INVOICE)
					->addFieldToFilter('status', array('neq' => OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_FAILED))
					->addFieldToFilter('status', array('neq' => OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_COMPLETE))
					->addFieldToFilter('status', array('neq' => OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_UNBALANCED));
		foreach($queue as $item) {
	        $item->setAttempt($item->getAttempt() + 1);
	        try { 
				$invoice = Mage::getModel('sales/order_invoice')->load($item->getEntityId());
	        	if($invoice->getId()) Mage::getModel('avatax/avatax_invoice')->invoice($invoice, $item);
	        	$item->setStatus(OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_COMPLETE)->setMessage(null)->save();
	        } catch (OnePica_AvaTax_Model_Avatax_Exception_Unbalanced $e) {
				$item->setStatus(OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_UNBALANCED)->setMessage($e->getMessage())->save();
	        } catch (Exception $e) { 
	        	$status = ($item->getAttempt() >= OnePica_AvaTax_Model_Config::QUEUE_ATTEMPT_MAX) ? OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_FAILED : OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_RETRY;
	        	$item->setStatus($status)->setMessage($e->getMessage())->save();
	        }
		}
		
		return $this;
	}
	
    /**
     * Attempt to send any pending credit memos to Avalara
     *
     * @return self
     */
    protected function _parseCreditMemos() {
		$queue = Mage::getModel('avatax_records/queue')->getCollection()
					->addFieldToFilter('type', OnePica_AvaTax_Model_Records_Queue::QUEUE_TYPE_CREDITMEMEO)
					->addFieldToFilter('status', array('neq' => OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_FAILED))
					->addFieldToFilter('status', array('neq' => OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_COMPLETE))
					->addFieldToFilter('status', array('neq' => OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_UNBALANCED));
		foreach($queue as $item) {
	        $item->setAttempt($item->getAttempt() + 1);
	        try {
				$creditmemo = Mage::getModel('sales/order_creditmemo')->load($item->getEntityId());
	        	if($creditmemo->getId()) Mage::getModel('avatax/avatax_invoice')->creditmemo($creditmemo, $item);
	        	$item->setStatus(OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_COMPLETE)->setMessage(null)->save();
	        } catch (OnePica_AvaTax_Model_Avatax_Exception_Unbalanced $e) {
				$item->setStatus(OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_UNBALANCED)->setMessage($e->getMessage())->save();
	        } catch (Exception $e) { 
	        	$status = ($item->getAttempt() >= OnePica_AvaTax_Model_Config::QUEUE_ATTEMPT_MAX) ? OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_FAILED : OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_RETRY;
	        	$item->setStatus($status)->setMessage($e->getMessage())->save();
	        }
		}
		
		return $this;
    }
    
}