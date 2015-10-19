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
     * Path to error in backend massage
     */
    const CALCULATE_ERROR_BACKEND_MESSAGE = 'error_backend_message';

    /**
     * Path to error frontend message
     */
    const CALCULATE_ERROR_FRONTEND_MESSAGE = 'error_frontend_message';

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
            return Mage::getStoreConfig(self::CALCULATE_ERROR_BACKEND_MESSAGE, $store);
        } else {
            return Mage::getStoreConfig(self::CALCULATE_ERROR_FRONTEND_MESSAGE, $store);
        }
    }
}
