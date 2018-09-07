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
 * Avatax Observer SalesOrderCreditmemoSaveBefore
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Observer_SalesOrderCreditmemoSaveBefore extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * Block partial creditmemo if creditmemo includes Landed Cost tax
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function execute(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order_Creditmemo $creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();

        /** @var Mage_Sales_Model_Order $order */
        $order = $creditmemo->getOrder();
        $isLandedCostAmount = $order->getAvataxLandedCostImportDutiesAmount();
        if ($isLandedCostAmount > 0) {
            $isPartialInvoice = $order->getTaxAmount() != $creditmemo->getTaxAmount();
            if ($isPartialInvoice) {
                throw new \OnePica_AvaTax_Exception(
                    Mage::helper('avatax')
                        ->__('You are not available to make partial creditmemo for orders that include customs duty taxes.')
                );
            }
        }

        return $this;
    }
}
