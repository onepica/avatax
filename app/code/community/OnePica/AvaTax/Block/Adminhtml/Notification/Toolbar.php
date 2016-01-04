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
 * Admin notification toolbar block
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Block_Adminhtml_Notification_Toolbar extends Mage_Adminhtml_Block_Notification_Toolbar
{
    /**
     * Count the number of pending_retry items in queue
     *
     * @return int
     */
    public function getQueuePendingRetryCount()
    {
        return Mage::getModel('avatax_records/queue')->getCollection()
            ->addFieldToFilter('status', OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_RETRY)
            ->getSize();
    }

    /**
     * Check if avatax is allowed
     *
     * @return bool
     */
    public function isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('avatax');
    }

    /**
     * Get queue grid url
     *
     * @return string
     */
    public function getQueueGridUrl()
    {
        return $this->getUrl('adminhtml/avaTax_grid/queue');
    }

    /**
     * Check if avatax toolbar is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag('tax/avatax/error_notification_toolbar');
    }

    /**
     * Get Queue attempt max value
     *
     * @return int
     */
    public function getQueueAttemptMaxValue()
    {
        return OnePica_AvaTax_Model_Service_Abstract_Config::QUEUE_ATTEMPT_MAX;
    }
}
