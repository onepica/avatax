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
class OnePica_AvaTax_Model_Observer_SalesConvertQuoteItemToOrderItem extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws \Varien_Exception
     */
    public function execute(Varien_Event_Observer $observer)
    {
        /** @var \Mage_Sales_Model_Quote_Item $quoteItem */
        $quoteItem = $observer->getEvent()->getItem();
        /** @var \Mage_Sales_Model_Order_Item $orderItem */
        $orderItem = $observer->getEvent()->getOrderItem();

        $orderItem->setAvataxLandedCostImportDutiesAmount($quoteItem->getAvataxLandedCostImportDutiesAmount());
        $orderItem->setBaseAvataxLandedCostImportDutiesAmount($quoteItem->getBaseAvataxLandedCostImportDutiesAmount());

        return $this;
    }
}
