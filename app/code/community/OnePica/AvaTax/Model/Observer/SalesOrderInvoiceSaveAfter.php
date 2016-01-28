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
 * @copyright  Copyright (c) 2015 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Avatax Observer SalesOrderInvoiceSaveAfter
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Observer_SalesOrderInvoiceSaveAfter extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * Create a sales invoice record in Avalara
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function execute(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();

        if ($invoice->getData('avatax_can_add_to_queue')
            && (int)$invoice->getState() === Mage_Sales_Model_Order_Invoice::STATE_PAID
            && Mage::helper('avatax/address')->isObjectActionable($invoice)
        ) {
            Mage::getModel('avatax_records/queue')
                ->setEntity($invoice)
                ->setType(OnePica_AvaTax_Model_Records_Queue::QUEUE_TYPE_INVOICE)
                ->setStatus(OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_PENDING)
                ->save();
        }

        return $this;
    }
}
