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
 * Checkout normalization controller
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_NormalizationController extends Mage_Core_Controller_Front_Action
{
    /**
     * @return $this
     */
    public function updateAction()
    {
        $flag = (bool)$this->getRequest()->getPost('flag') ? 1 : 0;
        $isMultishipping = (bool)$this->getRequest()->getPost('multishipping') ? 1 : 0;

        $session = Mage::getModel('checkout/session');
        $quote = $session->getQuote();
        $quote->setAvataxNormalizationFlag($flag);
        $quote->save();

        if ($isMultishipping) {
            /** @var OnePica_AvaTax_Helper_Address $addressHelper */
            $addressHelper = Mage::helper('avatax/address');
            $addressHelper->setOriginalCustomerAddresses($quote, true);

            Mage::dispatchEvent('checkout_type_multishipping_set_shipping_items', array('quote' => $quote));
        }

        return $this;
    }
}
