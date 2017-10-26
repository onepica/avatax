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
 * Avatax Observer SalesConvertQuoteAddressToOrderAddress
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Observer_SalesConvertQuoteAddressToOrderAddress extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * Save quote address id to Mage_Sales_Model_Order_Address
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function execute(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Quote_Address $quoteAddress */
        $quoteAddress = $observer->getEvent()->getAddress();
        /** @var Mage_Sales_Model_Order_Address $orderAddress */
        $orderAddress = $observer->getEvent()->getOrderAddress();

        $orderAddress->setAvataxQuoteAddressId($quoteAddress->getId());

        return $this;
    }
}
