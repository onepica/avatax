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
 * Class OnePica_AvaTax_Model_Action_Creditmemo
 */
class OnePica_AvaTax_Model_Action_Creditmemo extends OnePica_AvaTax_Model_Action_Abstract
{
    /**
     * Save order in AvaTax system
     *
     * @see OnePica_AvaTax_Model_Observer_SalesOrderCreditmemoSaveAfter::execute()
     * @param Mage_Sales_Model_Order_Creditmemo  $creditmemo
     * @param OnePica_AvaTax_Model_Records_Queue $queue
     * @return mixed
     * @throws OnePica_AvaTax_Exception
     * @throws OnePica_AvaTax_Model_Service_Exception_Commitfailure
     * @throws OnePica_AvaTax_Model_Service_Exception_Unbalanced
     */
    public function process($creditmemo, $queue)
    {
        $order = $creditmemo->getOrder();
        $storeId = $order->getStoreId();
        $this->setStoreId($storeId);
        $shippingAddress = ($order->getShippingAddress()) ? $order->getShippingAddress() : $order->getBillingAddress();
        if (!$shippingAddress) {
            throw new OnePica_AvaTax_Exception($this->_getHelper()->__('There is no address attached to this order'));
        }

        /** @var OnePica_AvaTax_Model_Service_Result_Creditmemo $creditmemoResult */
        $creditmemoResult = $this->_getService()->creditmemo($creditmemo, $queue);

        //if successful
        if (!$creditmemoResult->getHasError()) {
            $message = $this->_getHelper()
                ->__('Credit memo #%s was saved to AvaTax', $creditmemoResult->getDocumentCode());
            $this->_getHelper()->addStatusHistoryComment($order, $message);

            $totalTax = $creditmemoResult->getTotalTax();
            if ($totalTax != ($creditmemo->getBaseTaxAmount() * -1)) {
                throw new OnePica_AvaTax_Model_Service_Exception_Unbalanced(
                    'Collected: ' . $creditmemo->getTaxAmount() . ', Actual: ' . $totalTax
                );
            }
            //if not successful
        } else {
            $messages = $creditmemoResult->getErrors();
            throw new OnePica_AvaTax_Model_Service_Exception_Commitfailure(implode(' // ', $messages));
        }

        return true;
    }
}
