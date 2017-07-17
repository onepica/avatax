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
 * The Onepage Shipping Method Available block
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
abstract class OnePica_AvaTax_Block_Checkout_Onepage_Method extends OnePica_AvaTax_Block_Checkout_Abstract
{
    /**
     * Normalization Disabler Block Name
     * @var null
     */
    protected $_disablerBlockName = null;

    /**
     * Quote address
     *
     * @var Mage_Sales_Model_Quote_Address
     */
    protected $_address;

    /**
     * Get quote address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }

        return $this->_address;
    }

    /**
     * Overriding parent to insert session message block if an address has been validated.
     *
     * @return string
     */
    protected function _toHtml()
    {
        $additional = parent::_toHtml();

        if ($this->_showNotification()) {
            $additional .= $this->_htmlNotification();
        }

        return $additional;
    }

    /**
     * Check if Normalization Notification Is Allowed on current checkout step
     *
     * @return bool
     */
    protected abstract function _showNotification();

    /**
     * Get Normalization Notification Html
     *
     * @return string
     */
    protected function _htmlNotification()
    {
        $result = '';

        $normalizeAddressDisabler = $this->_getConfigData()->getNormalizeAddressDisabler();
        $checkboxDisabler = '';
        if ($normalizeAddressDisabler) {
            $checkboxDisabler = Mage::getBlockSingleton($this->_disablerBlockName)->toHtml();
        }

        if ($this->getAddress()->getAddressNormalized()) {
            $notice = Mage::helper('avatax/config')->getOnepageNormalizeMessage(Mage::app()->getStore());
            $notice .= $checkboxDisabler;
            if ($notice) {
                Mage::getSingleton('core/session')->addNotice($notice);
                $result .= $this->getMessagesBlock()->getGroupedHtml();
            }
        } elseif ($this->getAddress()->getAddressNotified()) {
            $result .= $this->getMessagesBlock()->getGroupedHtml();
        }

        if ($this->_getConfigData()->getNormalizeAddress(Mage::app()->getStore())
            && !$this->getAddress()->getAddressNormalized()
        ) {
            $result .= $checkboxDisabler;
        }

        return $result;
    }
}
