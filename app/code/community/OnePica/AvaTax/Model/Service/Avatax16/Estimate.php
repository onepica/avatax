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
 * Class OnePica_AvaTax_Model_Service_Avatax16_Estimate
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Avatax16_Estimate extends OnePica_AvaTax_Model_Service_Avatax16_Tax
{
    /**
     * An array of rates that acts as a cache
     * Example: $_rates[$cachekey] = array(
     *     'timestamp' => 1325015952
     *     'summary' => array(
     *         array('name'=>'NY STATE TAX', 'rate'=>4, 'amt'=>6),
     *         array('name'=>'NY CITY TAX', 'rate'=>4.50, 'amt'=>6.75),
     *         array('name'=>'NY SPECIAL TAX', 'rate'=>4.375, 'amt'=>0.56)
     *     ),
     *     'items' => array(
     *         5 => array('rate'=>8.875, 'amt'=>13.31),
     *         'Shipping' => array('rate'=>0, 'amt'=>0)
     *     )
     * )
     *
     * @var array
     */
    protected $_rates = array();

    /**
     * An array of line numbers to quote item ids
     *
     * @var array
     */
    protected $_lineToLineId = array();

    /**
     * Product gift pair
     *
     * @var array
     */
    protected $_productGiftPair = array();

    /**
     * Get rates from Avalara
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return array
     */
    public function getRates($item)
    {
        /** @var OnePica_AvaTax_Model_Sales_Quote_Address $address */
        $address = $item->getAddress();
        $this->_lines = array();

        $quote = $address->getQuote();
        $storeId = $quote->getStore()->getId();
        $configModel = Mage::getSingleton('avatax/service_avatax16_config')->init($storeId);
        $config = $configModel->getConfig();

        // Set up document for request
        $this->_request = new OnePica_AvaTax16_Document_Request();

        // set up header
        $header = new OnePica_AvaTax16_Document_Request_Header();
        $header->setAccountId($config->getAccountId());
        $header->setCompanyCode($config->getCompanyCode());
        $header->setTransactionType(self::TRANSACTION_TYPE_SALE);
        $header->setDocumentCode('quote-' . $address->getId());
        $header->setCustomerCode($this->_getConfigHelper()->getSalesPersonCode($storeId));
        $header->setVendorCode('VENDOR');
        $header->setTransactionDate($this->_getDateModel()->date('Y-m-d'));
        $header->setDefaultLocations($this->_getHeaderDefaultLocations($address));

        $this->_request->setHeader($header);

        return array();
    }
}
