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
 * Class OnePica_AvaTax_Model_Service_Avatax_Estimate
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Avatax_Estimate
    extends OnePica_AvaTax_Model_Service_Avatax_Tax
{
    /**
     * Length of time in minutes for cached rates
     *
     * @var int
     */
    const CACHE_TTL = 120;

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
     * An array of line items
     *
     * @var array
     */
    protected $_lines = array();

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
     * Last request key
     *
     * @var string
     */
    protected $_lastRequestKey;

    /**
     * Loads any saved rates in session
     */
    protected function _construct()
    {
        $rates = Mage::getSingleton('avatax/session')->getRates();
        if (is_array($rates)) {
            foreach ($rates as $key => $rate) {
                if ($rate['timestamp'] < $this->_getDateModel()->timestamp('-' . self::CACHE_TTL . ' minutes')) {
                    unset($rates[$key]);
                }
            }
            $this->_rates = $rates;
        }
        return parent::_construct();
    }

    /**
     * Get rates from Avalara
     *
     * @param Mage_Sales_Model_Quote_Address $address
     *
     * @return array
     */
    public function getRates(Mage_Sales_Model_Quote_Address $address)
    {
        if (self::$_hasError) {
            return array('failure' => true);
        }

        /** @var OnePica_AvaTax_Model_Sales_Quote_Address $address */
        $this->_lines = array();
        $this->setCanSendRequest(true); //reset flag

        //set up request
        $quote = $address->getQuote();
        $this->_request = new GetTaxRequest();
        $this->_request->setDocType(DocumentType::$SalesOrder);
        $this->_request->setDocCode('quote-' . $address->getId());
        $this->_addGeneralInfo($address);
        $this->_setOriginAddress($quote->getStoreId());
        $this->_setDestinationAddress($address);
        $this->_request->setDetailLevel(DetailLevel::$Line);
        $this->_addItemsInCart($address);
        $this->_addShipping($address);
        //Added code for calculating tax for giftwrap items
        $this->_addGwOrderAmount($address);
        $this->_addGwPrintedCardAmount($address);
        //check to see if we can/need to make the request to Avalara
        $requestKey = $this->_genRequestKey();
        $makeRequest = empty($this->_rates[$requestKey]['items']);
        //@startSkipCommitHooks
        $makeRequest &= count($this->_lineToLineId) ? true : false;
        $makeRequest &= $this->_request->getDestinationAddress() == '' ? false : true;
        $makeRequest &= $address->getId() ? true : false;
        $makeRequest &= !isset($this->_rates[$requestKey]['failure']);
        $makeRequest &= $this->isCanSendRequest();
        //@finishSkipCommitHooks

        //make request if needed and save results in cache
        if ($makeRequest) {
            $result = $this->_send($quote->getStoreId());
            $this->_rates[$requestKey] = array(
                'timestamp'  => $this->_getDateModel()->timestamp(),
                'address_id' => $address->getId(),
                'summary'    => array(),
                'items'      => array(),
                'gw_items'   => array()
            );

            //success
            /** @var GetTaxResult $result */
            if ($result->getResultCode() == SeverityLevel::$Success) {
                foreach ($result->getTaxLines() as $ctl) {
                    /** @var TaxLine $ctl */
                    $id = $this->_getItemIdByLine($ctl);
                    $code = $this->_getTaxArrayCodeByLine($ctl);
                    $this->_rates[$requestKey][$code][$id] = array(
                        'rate'         => ($ctl->getTax() ? $ctl->getRate() : 0) * 100,
                        'amt'          => $ctl->getTax(),
                        'taxable'      => $ctl->getTaxable(),
                        'tax_included' => $ctl->getTaxIncluded(),
                    );
                }
                $this->_rates[$requestKey]['summary'] = $this->_getSummaryFromResponse($result);
                //failure
            } else {
                $this->_rates[$requestKey]['failure'] = true;
            }

            Mage::getSingleton('avatax/session')->setRates($this->_rates);
        }

        $rates = isset($this->_rates[$requestKey]) ? $this->_rates[$requestKey] : array();

        return $rates;
    }

    /**
     * Get line rate
     *
     * @param OnePica_AvaTax_Document_Response $response
     * @return array
     */
    protected function _getSummaryFromResponse($response)
    {
        $unique = array();
        foreach ($response->getTaxSummary() as $row) {
            $name = $row->getTaxName();
            $unique[$name] = (isset($unique[$name])) ? $unique[$name] + 1 : 1;
        }

        $result = array();
        foreach ($response->getTaxSummary() as $row) {
            $name = $row->getTaxName();
            $name = ($unique[$name] > 1) ? $name . " " . $row->getJurisCode() : $name;
            $result[] = array(
                'name' => $name,
                'rate' => $row->getRate() * 100,
                'amt'  => $row->getTax()
            );
        }

        return $result;
    }

    /**
     * Generates a hash key for the exact request
     *
     * @return string
     */
    protected function _genRequestKey()
    {
        $hash = sprintf("%u", crc32(serialize($this->_request)));
        $this->_setLastRequestKey($hash);
        return $hash;
    }

    /**
     * Set last request key
     *
     * @param string $requestKey
     */
    protected function _setLastRequestKey($requestKey)
    {
        $this->_lastRequestKey = $requestKey;
    }

    /**
     * Get last request key
     *
     * @return string|null
     */
    public function getLastRequestKey()
    {
        return $this->_lastRequestKey;
    }

    /**
     * Adds shipping cost to request as item
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return int
     */
    protected function _addShipping($address)
    {
        $lineNumber = count($this->_lines);
        $storeId = $address->getQuote()->getStore()->getId();
        $taxClass = Mage::helper('tax')->getShippingTaxClass($storeId);
        $shippingAmount = max(
            0.0, (float)$address->getBaseShippingAmount()
        );

        if ($this->_getTaxDataHelper()->applyTaxAfterDiscount($storeId)) {
            $shippingAmount -= (float)$address->getBaseShippingDiscountAmount();
        }

        $line = new Line();
        $line->setNo($lineNumber);
        $shippingSku = $this->_getConfigHelper()->getShippingSku($storeId);
        $line->setItemCode($shippingSku ?: 'Shipping');
        $line->setDescription('Shipping costs');
        $line->setTaxCode($taxClass);
        $line->setQty(1);
        $line->setAmount($shippingAmount);
        $line->setDiscounted(
            (float)$address->getBaseShippingDiscountAmount()
            && $this->_getTaxDataHelper()->applyTaxAfterDiscount($storeId)
        );

        if ($this->_getTaxDataHelper()->shippingPriceIncludesTax($storeId)) {
            $line->setTaxIncluded(true);
        }

        $this->_lines[$lineNumber] = $line;
        $this->_request->setLines($this->_lines);
        $this->_lineToLineId[$lineNumber] = $this->_getConfigHelper()->getShippingSku($storeId);

        return $lineNumber;
    }

    /**
     * Adds giftwraporder cost to request as item
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return int|bool
     */
    protected function _addGwOrderAmount($address)
    {
        if (!$address->getGwPrice()) {
            return false;
        }
        $lineNumber = count($this->_lines);
        $storeId = $address->getQuote()->getStore()->getId();
        //Add gift wrapping price(for entire order)
        $gwOrderAmount = $address->getGwBasePrice();

        $line = new Line();
        $line->setNo($lineNumber);
        $gwOrderSku = $this->_getConfigHelper()->getGwOrderSku($storeId);
        $line->setItemCode($gwOrderSku ? $gwOrderSku : 'GwOrderAmount');
        $line->setDescription('Gift Wrap Order Amount');
        $line->setTaxCode($this->_getGiftTaxClassCode($storeId));
        $line->setQty(1);
        $line->setAmount($gwOrderAmount);
        $line->setDiscounted(false);

        if ($this->_getTaxDataHelper()->priceIncludesTax($storeId)) {
            $line->setTaxIncluded(true);
        }

        $this->_lines[$lineNumber] = $line;
        $this->_request->setLines($this->_lines);
        $this->_lineToLineId[$lineNumber] = $this->_getConfigHelper()->getGwOrderSku($storeId);
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

        $line = new Line();
        $line->setNo($lineNumber);
        $gwItemsSku = $this->_getConfigHelper()->getGwItemsSku($storeId);
        $line->setItemCode($gwItemsSku ? $gwItemsSku : 'GwItemsAmount');
        $line->setDescription('Gift Wrap Items Amount');
        $line->setTaxCode($this->_getGiftTaxClassCode($storeId));
        $line->setQty($item->getQty());
        $line->setAmount($gwItemsAmount);
        $line->setDiscounted(false);

        if ($this->_getTaxDataHelper()->priceIncludesTax($storeId)) {
            $line->setTaxIncluded(true);
        }

        $this->_lines[$lineNumber] = $line;
        $this->_request->setLines($this->_lines);
        $this->_lineToLineId[$lineNumber] = $this->_getConfigHelper()->getGwItemsSku($storeId);
        $this->_productGiftPair[$lineNumber] = $item->getId();

        return $lineNumber;
    }

    /**
     * Adds giftwrap printed card cost to request as item
     *
     * @param Mage_Sales_Model_Quote
     * @return int|bool
     */
    protected function _addGwPrintedCardAmount($address)
    {
        if (!$address->getGwPrintedCardPrice()) {
            return false;
        }
        $lineNumber = count($this->_lines);
        $storeId = $address->getQuote()->getStore()->getId();
        //Add printed card price
        $gwPrintedCardAmount = $address->getGwPrintedCardBasePrice();

        $line = new Line();
        $line->setNo($lineNumber);
        $gwPrintedCardSku = $this->_getConfigHelper()->getGwPrintedCardSku($storeId);
        $line->setItemCode($gwPrintedCardSku ? $gwPrintedCardSku : 'GwPrintedCardAmount');
        $line->setDescription('Gift Wrap Printed Card Amount');
        $line->setTaxCode($this->_getGiftTaxClassCode($storeId));
        $line->setQty(1);
        $line->setAmount($gwPrintedCardAmount);
        $line->setDiscounted(false);

        if ($this->_getTaxDataHelper()->priceIncludesTax($storeId)) {
            $line->setTaxIncluded(true);
        }

        $this->_lines[$lineNumber] = $line;
        $this->_request->setLines($this->_lines);
        $this->_lineToLineId[$lineNumber] = $this->_getConfigHelper()->getGwPrintedCardSku($storeId);
        return $lineNumber;
    }

    /**
     * Adds all items in the cart to the request
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return int
     */
    protected function _addItemsInCart(Mage_Sales_Model_Quote_Address $address)
    {
        $items = $address->getAllItems();
        if (count($items) > 0) {
            $this->_initProductCollection($items);
            $this->_initTaxClassCollection($address);
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
        if (!$item->getId()) {
            $this->setCanSendRequest(false);

            return $this;
        }

        $this->_addGwItemsAmount($item);
        if ($this->isProductCalculated($item)) {
            return false;
        }
        $product = $this->_getProductByProductId($item->getProductId());
        $taxClass = $this->_getTaxClassCodeByProduct($product);
        $price = $item->getBaseRowTotal();

        if ($this->_getTaxDataHelper()->applyTaxAfterDiscount($item->getStoreId())) {
            $price = $item->getBaseRowTotal() - $item->getBaseDiscountAmount();
        }

        $lineNumber = count($this->_lines);
        $line = new Line();
        $line->setNo($lineNumber);
        $line->setItemCode(
            $this->_getCalculationHelper()->getItemCode(
                $this->_getProductForItemCode($item),
                $item->getStoreId()
            )
        );
        $line->setDescription($item->getName());
        $line->setQty($item->getTotalQty());
        $line->setAmount($price);
        $line->setDiscounted(
            (float)$item->getDiscountAmount() && $this->_getTaxDataHelper()->applyTaxAfterDiscount($item->getStoreId())
        );

        if ($this->_getTaxDataHelper()->priceIncludesTax($item->getStoreId())) {
            $line->setTaxIncluded(true);
        }

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
        $this->_lineToLineId[$lineNumber] = $item->getId();

        return $lineNumber;
    }

    /**
     * Retrieve product for item code
     *
     * @param Mage_Sales_Model_Quote_Address_Item|Mage_Sales_Model_Quote_Item $item
     * @return null|Mage_Catalog_Model_Product
     * @throws OnePica_AvaTax_Exception
     */
    protected function _getProductForItemCode($item)
    {
        $product = $this->_getProductByProductId($item->getProductId());
        if (!$this->_getCalculationHelper()->isConfigurable($item)) {
            return $product;
        }

        $children = $item->getChildren();

        if (isset($children[0]) && $children[0]->getProductId()) {
            $product = $this->_getProductByProductId($children[0]->getProductId());
        }

        return $product;
    }

    /**
     * Get item id/code for given line
     *
     * @param TaxLine $line
     * @return string|int
     */
    protected function _getItemIdByLine($line)
    {
        return isset($this->_productGiftPair[$line->getNo()])
            ? $this->_productGiftPair[$line->getNo()]
            : $this->_lineToLineId[$line->getNo()];
    }

    /**
     * Get tax array code for given line
     *
     * @param TaxLine $line
     * @return string
     */
    protected function _getTaxArrayCodeByLine($line)
    {
        return isset($this->_productGiftPair[$line->getNo()]) ? 'gw_items' : 'items';
    }

    /**
     * Get tax detail summary
     * this method is using last request key,
     * so it returns summary of last made estimation.
     * if you are using two calculation simultaneously,
     * be sure to call getRates method for each calculation
     * before calling getSummary
     *
     * @param Mage_Sales_Model_Quote_Address $address
     *
     * @return array
     */
    public function getSummary($address)
    {
        $lastRequestKey = $this->getLastRequestKey();

        if (isset($lastRequestKey)) {
            $result = isset($this->_rates[$lastRequestKey]['summary'])
                        ? $this->_rates[$lastRequestKey]['summary'] : array();
        } else {
            $rates = $this->getRates($address);
            $result = (isset($rates)) ? $rates['summary'] : null;
        }

        return $result;
    }

    /**
     * Get tax data helper
     *
     * @return Mage_Tax_Helper_Data
     */
    protected function _getTaxDataHelper()
    {
        return Mage::helper('tax');
    }
}
