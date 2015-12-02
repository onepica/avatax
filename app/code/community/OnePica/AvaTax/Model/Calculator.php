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
 * Class OnePica_AvaTax_Model_Calculator
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Calculator extends Mage_Core_Model_Factory
{
    /**
     * Service
     *
     * @var OnePica_AvaTax_Model_Service_Abstract
     */
    protected $_service;

    /**
     * Class constructor
     *
     * @param array $params
     * @throws OnePica_AvaTax_Exception
     */
    public function __construct($params = array())
    {
        $activeService = $this->_getConfigHelper()->getActiveService();
        $this->_service = Mage::getSingleton('avatax/service')->factory($activeService, $params);
    }

    /**
     * Get Service
     *
     * return OnePica_AvaTax_Model_Service_Abstract
     */
    protected function _getService()
    {
        return $this->_service;
    }

    /**
     * Get rates from Service
     * Example: $_ratesData = array(
     *     'timestamp' => 1325015952
     *     'summary' => array(
     *         array('name'=>'NY STATE TAX', 'rate'=>4, 'amt'=>6),
     *         array('name'=>'NY CITY TAX', 'rate'=>4.50, 'amt'=>6.75),
     *         array('name'=>'NY SPECIAL TAX', 'rate'=>4.375, 'amt'=>0.56)
     *     ),
     *     'items' => array(
     *         5 => array('rate'=>8.875, 'amt'=>13.31),
     *         'Shipping' => array('rate'=>0, 'amt'=>0)
     *     ),
     *    // if error on get tax
     *     'failure' => true
     * )
     * @param Mage_Sales_Model_Quote_Item $item
     * @return array
     */
    protected function _getRates($item)
    {
        $storeId = $item->getAddress()->getQuote()->getStoreId();
        $this->setStoreId($storeId);
        $rates = $this->_getService()->getRates($item);
        if (isset($rates['failure']) && ($rates['failure'] === true)) {
            /** @var OnePica_AvaTax_Model_Sales_Quote_Address $address */
            $address = $item->getAddress();
            // set error flag for processing estimation errors on upper level
            $address->getQuote()->setData('estimate_tax_error', true);
        }
        return $rates;
    }

    /**
     * Estimates tax rate for one item.
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return int
     */
    public function getItemRate($item)
    {
        if ($this->isProductCalculated($item)) {
            return 0;
        } else {
            $id = $item->getSku();
            $ratesData = $this->_getRates($item);
            return isset($ratesData['items'][$id]['rate']) ? $ratesData['items'][$id]['rate'] : 0;
        }
    }

    /**
     * Get item tax group
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return array
     */
    public function getItemTaxGroup($item)
    {
        if ($this->isProductCalculated($item)) {
            return array();
        }

        $id = $item->getSku();
        $ratesData = $this->_getRates($item);

        $jurisdictionRates = isset($ratesData['items'][$id]['jurisdiction_rates'])
            ? $ratesData['items'][$id]['jurisdiction_rates']
            : array();

        $taxGroup = array();
        foreach ($jurisdictionRates as $jurisdiction => $rate) {
            $taxGroup[] = array(
                'rates'   => array(
                    array(
                        'code'     => $jurisdiction,
                        'title'    => $jurisdiction,
                        'percent'  => $rate,
                        'position' => 0,
                        'priority' => 0,
                        'rule_id'  => 0
                    )
                ),
                'percent' => $rate,
                'id'      => $jurisdiction
            );
        }

        return $taxGroup;
    }

    /**
     * Get item tax
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return int
     */
    public function getItemGiftTax($item)
    {
        if ($item->getParentItemId()) {
            return 0;
        }
        $ratesData = $this->_getRates($item);
        $id = $item->getSku();
        return isset($ratesData['gw_items'][$id]['amt']) ? $ratesData['gw_items'][$id]['amt'] : 0;
    }

    /**
     * Estimates tax amount for one item. Does not trigger a call if the shipping
     * address has no postal code, or if the postal code is set to "-" (OneStepCheckout)
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return int
     */
    public function getItemTax($item)
    {
        if ($item->getAddress()->getPostcode() && $item->getAddress()->getPostcode() != '-') {
            if ($this->isProductCalculated($item)) {
                $tax = 0;
                foreach ($item->getChildren() as $child) {
                    $child->setAddress($item->getAddress());
                    $tax += $this->getItemTax($child);
                }
                return $tax;
            } else {
                $ratesData = $this->_getRates($item);;
                $id = $item->getSku();
                return isset($ratesData['items'][$id]['amt']) ? $ratesData['items'][$id]['amt'] : 0;
            }
        }
        return 0;
    }

    /**
     * Get tax detail summary
     *
     * @param int|null $addressId
     * @return array
     */
    public function getSummary($addressId = null)
    {
        return $this->_getService()->getSummary($addressId);
    }

    /**
     * Test to see if the product carries its own numbers or is calculated based on parent or children
     *
     * @param Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Item|mixed $item
     * @return bool
     */
    public function isProductCalculated($item)
    {
        return $this->_getService()->isProductCalculated($item);
    }

    /**
     * Save order in AvaTax system
     *
     * @see OnePica_AvaTax_Model_Observer_SalesOrderInvoiceSaveAfter::execute()
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @param OnePica_AvaTax_Model_Records_Queue $queue
     * @return bool
     * @throws OnePica_AvaTax_Exception
     * @throws OnePica_AvaTax_Model_Service_Exception_Commitfailure
     * @throws OnePica_AvaTax_Model_Service_Exception_Unbalanced
     */
    public function invoice($invoice, $queue)
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
            $this->_addStatusHistoryComment($order, $message);

            $totalTax = $invoiceResult->getTotalTax();
            if ($totalTax != $invoice->getBaseTaxAmount()) {
                throw new OnePica_AvaTax_Model_Service_Exception_Unbalanced(
                    'Collected: '. $invoice->getBaseTaxAmount() . ', Actual: ' . $totalTax
                );
            }

            //if not successful
        } else {
            $messages = $invoiceResult->getErrors();
            throw new OnePica_AvaTax_Model_Service_Exception_Commitfailure(implode(' // ', $messages));
        }

        return true;
    }

    /**
     * Save order in AvaTax system
     *
     * @see OnePica_AvaTax_Model_Observer_SalesOrderCreditmemoSaveAfter::execute()
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @param OnePica_AvaTax_Model_Records_Queue $queue
     * @return mixed
     * @throws OnePica_AvaTax_Exception
     * @throws OnePica_AvaTax_Model_Service_Exception_Commitfailure
     * @throws OnePica_AvaTax_Model_Service_Exception_Unbalanced
     */
    public function creditmemo($creditmemo, $queue)
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
            $this->_addStatusHistoryComment($order, $message);

            $totalTax = $creditmemoResult->getTotalTax();
            if ($totalTax != ($creditmemo->getBaseTaxAmount() * -1)) {
                throw new OnePica_AvaTax_Model_Service_Exception_Unbalanced(
                    'Collected: '. $creditmemo->getTaxAmount() . ', Actual: ' . $totalTax
                );
            }

            //if not successful
        } else {
            $messages = $creditmemoResult->getErrors();
            throw new OnePica_AvaTax_Model_Service_Exception_Commitfailure(implode(' // ', $messages));
        }

        return true;
    }

    /**
     * Tries to ping AvaTax service with provided credentials
     *
     * @param int|null $storeId
     * @return bool|array
     */
    public function ping($storeId)
    {
        $storeId = Mage::app()->getStore($storeId)->getStoreId();
        $this->setStoreId($storeId);
        return $this->_getService()->ping($storeId);
    }

    /**
     * Get config helper
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avatax/config');
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getErrorsHelper()
    {
        return Mage::helper('avatax/errors');
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('avatax');
    }

    /**
     * Adds a comment to order history. Method choosen based on Magento version.
     *
     * @param Mage_Sales_Model_Order $order
     * @param string $comment
     * @return $this
     */
    protected function _addStatusHistoryComment($order, $comment)
    {
        if (method_exists($order, 'addStatusHistoryComment')) {
            $order->addStatusHistoryComment($comment)->save();
        } elseif (method_exists($order, 'addStatusToHistory')) {
            $order->addStatusToHistory($order->getStatus(), $comment, false)->save();
        }
        return $this;
    }

    /**
     * Set Store Id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_getService()->setStoreId($storeId);
        return $this;
    }
}
