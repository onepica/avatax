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
 * The base AvaTax Helper class
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Helper_Errors extends Mage_Core_Helper_Abstract
{
    /**
     * Identifier for error message
     */
    const CALCULATE_ERROR_MESSAGE_IDENTIFIER = 'avatax_calculate_error';

    /**
     * Identifier for validation notice
     */
    const VALIDATION_NOTICE_IDENTIFIER = 'avatax_validation_notice';

    /**
     * Adds error message if there is an error
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function addErrorMessage($store = null)
    {
        $message = $this->getErrorMessage($store);
        if (Mage::app()->getStore()->isAdmin()) {
            /** @var Mage_Adminhtml_Model_Session_Quote $session */
            $session = Mage::getSingleton('adminhtml/session_quote');
        } else {
            /** @var Mage_Checkout_Model_Session $session */
            $session = Mage::getSingleton('checkout/session');
        }

        $messages = $session->getMessages();
        if (!$messages->getMessageByIdentifier(self::CALCULATE_ERROR_MESSAGE_IDENTIFIER)) {
            /** @var Mage_Core_Model_Message_Error $error */
            $error = Mage::getSingleton('core/message')->error($message);
            $error->setIdentifier(self::CALCULATE_ERROR_MESSAGE_IDENTIFIER);
            $session->addMessage($error);
        }
        return $message;
    }

    /**
     * Remove error message
     *
     * @return $this
     */
    public function removeErrorMessage()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            /** @var Mage_Adminhtml_Model_Session_Quote $session */
            $session = Mage::getSingleton('adminhtml/session_quote');
        } else {
            /** @var Mage_Checkout_Model_Session $session */
            $session = Mage::getSingleton('checkout/session');
        }
        /** @var Mage_Core_Model_Message_Collection $messages */
        $messages = $session->getMessages();
        $messages->deleteMessageByIdentifier(self::CALCULATE_ERROR_MESSAGE_IDENTIFIER);
        return $this;
    }

    /**
     * Gets error message
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getErrorMessage($store = null)
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::helper('avatax/config')->getErrorBackendMessage($store);
        } else {
            return Mage::helper('avatax/config')->getErrorFrontendMessage($store);
        }
    }

    /**
     * Get config helper
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    private function _getConfigHelper()
    {
        return Mage::helper('avatax/config');
    }

    /**
     * Can show estimation errors
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function canShowEstimationError($quote)
    {
        if ($quote->getData('estimate_tax_error') && $this->_getConfigHelper()->fullStopOnError($quote->getStoreId())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns full stop on error
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function fullStopOnError($quote)
    {
        if ($quote->getData('estimate_tax_error') && $this->_getFullStopOnErrorMode($quote->getStoreId())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns full stop on error mode
     *
     * @param null|int $storeId
     * @return bool
     */
    protected function _getFullStopOnErrorMode($storeId = null)
    {
        $validateAddress = $this->_getConfigHelper()->getValidateAddress($storeId);
        $errorFullStop = $this->_getConfigHelper()->fullStopOnError($storeId);
        $enablePreventOrderConst = OnePica_AvaTax_Model_Source_Addressvalidation::ENABLED_PREVENT_ORDER;
        if (!$validateAddress && $errorFullStop || $validateAddress == $enablePreventOrderConst) {
            return true;
        } else {
            return false;
        }
    }
}
