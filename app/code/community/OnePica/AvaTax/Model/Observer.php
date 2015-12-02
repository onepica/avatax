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
