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
 * Avatax Observer
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Observer extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * Add error message if tax estimation has problems when user estimates post
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function controllerActionPostdispatchCheckoutCartEstimatePost(Varien_Event_Observer $observer)
    {
        $this->_handleTaxEstimation();
        return $this;
    }

    /**
     * Add error message if tax estimation has problems when user updates estimate post
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function controllerActionPostdispatchCheckoutCartEstimateUpdatePost(Varien_Event_Observer $observer)
    {
        $this->_handleTaxEstimation();
        return $this;
    }

    /**
     * Add error message if tax estimation has problems when user located at checkout/cart/index
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function controllerActionPredispatchCheckoutCartIndex(Varien_Event_Observer $observer)
    {
        $this->_addErrorMessage($this->_getQuote());

        return $this;
    }

    /**
     * Add error message if tax estimation has problems when creating order in admin
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function controllerActionPredispatchAdminhtmlSalesOrderCreateLoadBlock(Varien_Event_Observer $observer)
    {
        $adminQuote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        $this->_addErrorMessage($adminQuote);

        return $this;
    }

    /**
     * Add error message if tax estimation has problems
     *
     * @return $this
     */
    protected function _handleTaxEstimation()
    {
        $quote = $this->_getQuote();
        $quote->collectTotals();
        $this->_addErrorMessage($quote);

        return $this;
    }

    /**
     * Add error message if estimation has error
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return $this
     */
    protected function _addErrorMessage($quote)
    {
        if ($quote->getData('estimate_tax_error')) {
            $this->_getErrorsHelper()->addErrorMessage($quote->getStoreId());
        }

        return $this;
    }

    /**
     * Stop order creation if tax estimation has problems
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws OnePica_AvaTax_Exception
     */
    public function salesModelServiceQuoteSubmitBefore(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        $this->_handleTaxEstimationOnOrderPlace($quote);
        return $this;
    }

    /**
     * Stop order creation if tax estimation has problems
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function controllerActionPostdispatchCheckoutOnepageSaveShippingMethod(Varien_Event_Observer $observer) {
        if ($this->_getErrorsHelper()->fullStopOnError($this->_getQuote())) {
            Mage::app()
                ->getResponse()
                ->setBody($this->_getResponseErrorMessage());
        }
        return $this;
    }

    /**
     * Get response error message
     *
     * @return string
     */
    protected function _getResponseErrorMessage()
    {
        return Mage::helper('core')->jsonEncode(
            array(
                'error'   => - 1,
                'message' => $this->_getErrorsHelper()->getErrorMessage()
            )
        );
    }

    /**
     * Stop order creation if tax estimation has problems when multishipping
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws OnePica_AvaTax_Exception
     */
    public function checkoutTypeMultishippingCreateOrdersSingle(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Quote_Address $address */
        $address = $observer->getEvent()->getAddress();
        $quote = $address->getQuote();
        $this->_handleTaxEstimationOnOrderPlace($quote);
        return $this;
    }

    /**
     * Stop order creation if tax estimation has problems
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return $this
     * @throws OnePica_AvaTax_Exception
     */
    protected function _handleTaxEstimationOnOrderPlace($quote)
    {
        /** @var OnePica_AvaTax_Helper_Errors $helper */
        $helper = $this->_getErrorsHelper();
        $helper->removeErrorMessage();
        if ($helper->fullStopOnError($quote)) {
            throw new OnePica_AvaTax_Exception($helper->getErrorMessage());
        }
        return $this;
    }

    /**
     * Delete validation notices on successful order place on multiple checkout
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function checkoutSubmitAllAfter(Varien_Event_Observer $observer)
    {
        $this->_deleteValidateNotices();
        return $this;
    }

    /**
     * Delete validation notices on successful order place
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function salesModelServiceQuoteSubmitAfter(Varien_Event_Observer $observer)
    {
        $this->_deleteValidateNotices();
        return $this;
    }

    /**
     * Delete validation notices
     *
     * @return $this
     */
    protected function _deleteValidateNotices()
    {
        /** @var Mage_Checkout_Model_Session $session */
        $session = Mage::getSingleton('core/session');
        $messages = $session->getMessages();
        $messages->deleteMessageByIdentifier(OnePica_AvaTax_Helper_Errors::VALIDATION_NOTICE_IDENTIFIER);
        return $this;
    }
}
