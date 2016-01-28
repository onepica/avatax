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
 * Class OnePica_AvaTax_Model_Observer_SalesOrderInvoicePay
 */
class OnePica_AvaTax_Model_Observer_SalesOrderInvoicePay extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * Set flag, which will be checked in SalesOrderInvoiceSaveAfter observer
     *
     * @param \Varien_Event_Observer $observer
     * @return $this
     */
    public function execute(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();
        $invoice->setData('avatax_can_add_to_queue', true);

        return $this;
    }
}
