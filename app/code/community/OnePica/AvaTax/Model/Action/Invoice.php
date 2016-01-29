<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
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
 * Class OnePica_AvaTax_Model_Action_Invoice
 */
class OnePica_AvaTax_Model_Action_Invoice extends OnePica_AvaTax_Model_Action_Abstract
{
    /**
     * Save order in AvaTax system
     *
     * @see OnePica_AvaTax_Model_Observer_SalesOrderInvoiceSaveAfter::execute()
     * @param Mage_Sales_Model_Order_Invoice     $invoice
     * @param OnePica_AvaTax_Model_Records_Queue $queue
     * @return bool
     * @throws OnePica_AvaTax_Exception
     * @throws OnePica_AvaTax_Model_Service_Exception_Commitfailure
     * @throws OnePica_AvaTax_Model_Service_Exception_Unbalanced
     */
    public function process($invoice, $queue)
    {
        $order = $invoice->getOrder();
        $storeId = $order->getStoreId();
        $this->setStoreId($storeId);
        $shippingAddress = ($order->getShippingAddress()) ? $order->getShippingAddress() : $order->getBillingAddress();
        if (!$shippingAddress) {
            throw new OnePica_AvaTax_Exception($this->_getHelper()->__('There is no address attached to this order'));
        }

        /** @var OnePica_AvaTax_Model_Service_Result_Invoice $invoiceResult */
        $invoiceResult = $this->_getService()->invoice($invoice, $queue);

        //if successful
        if (!$invoiceResult->getHasError()) {
            $message = $this->_getHelper()->__('Invoice #%s was saved to AvaTax', $invoiceResult->getDocumentCode());
            $this->_getHelper()->addStatusHistoryComment($order, $message);

            $totalTax = $invoiceResult->getTotalTax();
            if ($totalTax != $invoice->getBaseTaxAmount()) {
                throw new OnePica_AvaTax_Model_Service_Exception_Unbalanced(
                    'Collected: ' . $invoice->getBaseTaxAmount() . ', Actual: ' . $totalTax
                );
            }
            //if not successful
        } else {
            $messages = $invoiceResult->getErrors();
            throw new OnePica_AvaTax_Model_Service_Exception_Commitfailure(implode(' // ', $messages));
        }

        return true;
    }
}
