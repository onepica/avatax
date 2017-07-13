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
class OnePica_AvaTax_Block_Checkout_Onepage_Shipping_Method_Available extends OnePica_AvaTax_Block_Checkout_Abstract
{
    /**
     * Get config helper
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getConfigData()
    {
        return Mage::helper('avatax/config');
    }

    /**
     * Overriding parent to insert session message block if an address has been validated.
     *
     * @return string
     */
    protected function _toHtml()
    {
        $additional = parent::_toHtml();

        $normalizeAddressDisabler = $this->_getConfigData()->getNormalizeAddressDisabler();
        $checkboxDisabler = '';
        if ($normalizeAddressDisabler) {
            $checkboxDisabler = Mage::getBlockSingleton('avatax/checkout_onepage_address_normalization_disabler')->toHtml();
        }
        if ($this->getAddress()->getAddressNormalized()) {
            $notice = Mage::helper('avatax/config')->getOnepageNormalizeMessage(Mage::app()->getStore());
            $notice .= $checkboxDisabler;
            if ($notice) {
                Mage::getSingleton('core/session')->addNotice($notice);
                $additional .= $this->getMessagesBlock()->getGroupedHtml();
            }
        } elseif ($this->getAddress()->getAddressNotified()) {
            $additional .= $this->getMessagesBlock()->getGroupedHtml();
        }

        if ($this->_getConfigData()->getNormalizeAddress(Mage::app()->getStore())
            && !$this->getAddress()->getAddressNormalized()
        ) {
            $additional .= $checkboxDisabler;
        }

        return $additional;
    }
}
