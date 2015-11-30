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
 * The Admin Sales Order Create model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
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
     * @return $this
     */
    public function setShippingAddress($address)
    {
        parent::setShippingAddress($address);

        if ($this->getQuote()->getIsVirtual()) {
            return $this;
        }
        if (!$this->_isAjaxRequest()) {
            $this->_validateShippingAddress();
        }
        return $this;
    }

    /**
     * Validate shipping address
     *
     * @return $this
     * @throws OnePica_AvaTax_Exception
     */
    protected function _validateShippingAddress()
    {
        if (!$this->_getDataHelper()->isServiceEnabled()) {
            return $this;
        }
        $result = $this->getShippingAddress()->validate();
        if ($result !== true) {
            throw new OnePica_AvaTax_Exception(implode('<br />', $result));
        } elseif ($this->getShippingAddress()->getAddressNormalized() && !$this->_messageAdded) {
            Mage::getSingleton('adminhtml/session')->addNotice(
                $this->_getDataHelper()->__('The shipping address has been modified during the validation process. Please confirm the address below is accurate.')
            );
            $this->_messageAdded = true;
        }

        return $this;
    }

    /**
     * Is ajax
     *
     * @return bool
     */
    protected function _isAjaxRequest()
    {
        return (bool)Mage::app()->getFrontController()->getRequest()->getParam('isAjax');
    }

    /**
     * Get data helper
     *
     * @return OnePica_AvaTax_Helper_Data
     */
    protected function _getDataHelper()
    {
        return Mage::helper('avatax');
    }
}
