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

        $this->_addItemsInCart($item);

        return array();
    }

    /**
     * Adds all items in the cart to the request
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return int
     */
    protected function _addItemsInCart($item)
    {
        if ($item->getAddress() instanceof Mage_Sales_Model_Quote_Address) {
            $items = $item->getAddress()->getAllItems();
        } elseif ($item->getQuote() instanceof Mage_Sales_Model_Quote) {
            $items = $item->getQuote()->getAllItems();
        } else {
            $items = array();
        }

        if (count($items) > 0) {
            $this->_initProductCollection($items);
            $this->_initTaxClassCollection($item->getAddress());
            foreach ($items as $item) {
                /** @var Mage_Sales_Model_Quote_Item $item */
                $this->_newLine($item);
            }
            $this->_request->setLines($this->_lines);
        }

        return count($this->_lines);
    }

    /**
     * Makes a Line object from a product item object
     *
     * @param Varien_Object|Mage_Sales_Model_Quote_Item $item
     * @return int|bool
     */
    protected function _newLine($item)
    {
        $this->_addGwItemsAmount($item);
        if ($this->isProductCalculated($item)) {
            return false;
        }
        $product = $this->_getProductByProductId($item->getProductId());
        $taxClass = $this->_getTaxClassCodeByProduct($product);
        $price = $item->getBaseRowTotal() - $item->getBaseDiscountAmount();
        $lineNumber = count($this->_lines);

        $line = new OnePica_AvaTax16_Document_Request_Line();
        $line->setLineCode($lineNumber);
        $line->setItemCode(substr($item->getSku(), 0, 50));
        $line->setNumberOfItems($item->getQty());
        $line->setlineAmount($price);
        $line->setItemDescription($item->getName());
        $line->setDiscounted($item->getDiscountAmount() ? true : false);

        if ($taxClass) {
            $line->setTaxCode($taxClass);
        }
        $ref1Value = $this->_getRefValueByProductAndNumber($product, 1, $item->getStoreId());
        if ($ref1Value) {
            $line->setRef1($ref1Value);
        }
        $ref2Value = $this->_getRefValueByProductAndNumber($product, 2, $item->getStoreId());
        if ($ref2Value) {
            $line->setRef2($ref2Value);
        }

        $this->_lines[$lineNumber] = $line;
        $this->_lineToLineId[$lineNumber] = $item->getSku();
        return $lineNumber;
    }

    /**
     * Adds giftwrapitems cost to request as item
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return int|bool
     */
    protected function _addGwItemsAmount($item)
    {
        if (!$item->getGwId()) {
            return false;
        }
        $lineNumber = count($this->_lines);
        $storeId = $item->getQuote()->getStoreId();
        //Add gift wrapping price(for individual items)
        $gwItemsAmount = $item->getGwBasePrice() * $item->getQty();

        $line = new OnePica_AvaTax16_Document_Request_Line();
        $line->setLineCode($lineNumber);
        $gwItemsSku = $this->_getConfigHelper()->getGwItemsSku($storeId);
        $line->setItemCode($gwItemsSku ? $gwItemsSku : 'GwItemsAmount');
        $line->setItemDescription('Gift Wrap Items Amount');
        $line->setTaxCode($this->_getGiftTaxClassCode($storeId));
        $line->setNumberOfItems($item->getQty());
        $line->setlineAmount($gwItemsAmount);
        $line->setDiscounted(false);

        $this->_lines[$lineNumber] = $line;
        $this->_request->setLines($this->_lines);
        $this->_lineToLineId[$lineNumber] = $this->_getConfigHelper()->getGwItemsSku($storeId);
        $this->_productGiftPair[$lineNumber] = $item->getSku();

        return $lineNumber;
    }
}
