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
 * Avatax Observer SalesOrderInvoiceSaveBefore
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Observer_SalesOrderInvoiceSaveBefore extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * * Block partial invoice if invoice includes Landed Cost tax
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws \OnePica_AvaTax_Exception
     * @throws \Varien_Exception
     */
    public function execute(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();

        /** @var Mage_Sales_Model_Order $order */
        $order = $invoice->getOrder();
        $isLandedCostAmount = $order->getAvataxLandedCostImportDutiesAmount();
        if ($isLandedCostAmount > 0) {
            $isPartialInvoice = false;
            foreach ($order->getAllItems() as $item) {
                if ($item->getQtyInvoiced() != $item->getQtyOrdered()) {
                    $isPartialInvoice = true;
                    break;
                }
            }

            if ($isPartialInvoice) {
                throw new \OnePica_AvaTax_Exception(
                    Mage::helper('avatax')
                        ->__('You are not available to make partial invoice for orders that include customs duty taxes.')
                );
            }
        }

        return $this;
    }
}
