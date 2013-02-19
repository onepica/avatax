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

class OnePica_AvaTax_Block_Adminhtml_Notification_Toolbar extends Mage_Adminhtml_Block_Notification_Toolbar
{
	
    /**
     * Count the number of pending_retry items in queue
     *
     * @return int
     */
	protected function _getQueuePendingRetryCount() {
		return Mage::getModel('avatax_records/queue')->getCollection()
			->addFieldToFilter('status', OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_RETRY)
			->load()
			->count();
	}

    /**
     * Piggyback the admin notification messages block to show AvaTax warnings as needed
     *
     * @return string
     */
    protected function _toHtml() {
    	$html = '';
    	
    	if(Mage::getStoreConfig('tax/avatax/error_notification_toolbar')) {
    		$count = $this->_getQueuePendingRetryCount();
	    	if ($count) {
	    		if($count==1) $text = 'There is <strong>' . $count . '</strong> entry in the AvaTax Order Sync Queue that has errored. Syncing is attemped ' . OnePica_AvaTax_Model_Config::QUEUE_ATTEMPT_MAX . ' times before permanently failing.';
	    		else $text = 'There are <strong>' . $count . '</strong> entries in the AvaTax Order Sync Queue that have errored. Syncing is attemped ' . OnePica_AvaTax_Model_Config::QUEUE_ATTEMPT_MAX . ' times before permanently failing.';
	    		
	    		$html = '<div class="notification-global">';
	    		if (Mage::getSingleton('admin/session')->isAllowed('avatax')) { 
	    			$html .= '<span class="f-right">Go to the <a href="' . $this->getUrl('avatax/adminhtml_grid/queue') . '">AvaTax Order Sync Queue</a></span>';
	    		}
	    		$html .= '<strong class="label">AvaTax:</strong> ' . $text . '</div>';
	    	}
    	}
    	
        return parent::_toHtml() . $html;
    }
}
