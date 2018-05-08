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
 * OnePica_AvaTax_Model_Observer_AdminhtmlSalesOrderCreateProcessData
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Observer_AdminhtmlSalesOrderCreateProcessData
    extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * Save quote address id to Mage_Sales_Model_Order_Address
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws \Varien_Exception
     */
    public function execute(Varien_Event_Observer $observer)
    {
        /** @var \Mage_Sales_Model_Quote $quote */
        $quote = $observer->getOrderCreateModel()->getQuote();
        $orderPostData = $observer->getRequest('order');

        if (isset($orderPostData['account']['avatax_lc_seller_is_importer'])) {
            $quote->setCustomerAvataxLcSellerIsImporter($orderPostData['account']['avatax_lc_seller_is_importer']);
        }

        return $this;
    }
}
