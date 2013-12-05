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
 * The Admin Sales Order Create model.
 */
class OnePica_AvaTax_Model_Adminhtml_Sales_Order_Create extends Mage_Adminhtml_Model_Sales_Order_Create
{
    /**
     * If a session message has been added.
     *
     * @var bool
     */
    protected $_messageAdded = false;

    /**
     * Overrides the parent to validate the shipping address.
     *
     * @param array $address
     * @return OnePica_AvaTax_Model_Adminhtml_Sales_Order_Create
     */
    public function setShippingAddress($address)
    {
        parent::setShippingAddress($address);

        if ($this->getQuote()->getIsVirtual()) {
            return $this;
        }

        if (Mage::helper('avatax')->isAvataxEnabled()) {
            if (!Mage::app()->getFrontController()->getRequest()->getParam('isAjax')) {
                $result = $this->getShippingAddress()->validate();
                if ($result !== true) {
                    $storeId = $this->_session->getStore()->getId();
                    if(Mage::helper('avatax')->fullStopOnError($storeId)) {
                        foreach ($result as $error) {
                            $this->getSession()->addError($error);
                        }
                        Mage::throwException('');
                    }
                }
                else if ($this->getShippingAddress()->getAddressNormalized() && !$this->_messageAdded) {
                    Mage::getSingleton('avatax/session')->addNotice(Mage::helper('avatax')->__('The shipping address has been modified during the validation process.  Please confirm the address below is accurate.'));
                    $this->_messageAdded = true;  // only add the message once
                }
            }
        }
        return $this;
    }
}
