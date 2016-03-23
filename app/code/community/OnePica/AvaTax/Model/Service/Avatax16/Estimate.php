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
        $rates = Mage::getSingleton('avatax/session')->getAvatax16Rates();
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
     * @return array
     */
    public function getRates($address)
    {
        /** @var OnePica_AvaTax_Model_Sales_Quote_Address $address */
        $this->_lines = array();
        $this->setCanSendRequest(true); //reset flag

        $quote = $address->getQuote();
        $storeId = $quote->getStore()->getId();
        $transactionDate = $this->_convertGmtDate(Varien_Date::now(), $storeId);

        // Set up document for request
        $this->_request = $this->_getNewDocumentRequestObject();

        // set up header
        $header = $this->_getRequestHeaderWithMainValues($storeId, $address);
        $header->setDocumentCode('quote-' . $address->getId());
        $header->setTransactionDate($transactionDate);
        $header->setDefaultLocations($this->_getHeaderDefaultLocations($address));

        $this->_request->setHeader($header);

        $this->_addItemsInCart($address);
        $this->_addShipping($address);
        //Added code for calculating tax for giftwrap items (order)
        $this->_addGwOrderAmount($address);
        $this->_addGwPrintedCardAmount($address);

        //check to see if we can/need to make the request to Avalara
        $requestKey = $this->_genRequestKey();
        $makeRequest = empty($this->_rates[$requestKey]['items']);
        //@startSkipCommitHooks
        $makeRequest &= count($this->_lineToLineId) ? true : false;

        $makeRequest &= $this->_hasDestinationAddress();
        $makeRequest &= $address->getId() ? true : false;
        $makeRequest &= !isset($this->_rates[$requestKey]['failure']);
        //@finishSkipCommitHooks

        //make request if needed and save results in cache
        if ($makeRequest) {
            /** @var OnePica_AvaTax16_Document_Response $result */
            $result = $this->_send($quote->getStoreId());
            $this->_rates[$requestKey] = array(
                'timestamp' => $this->_getDateModel()->timestamp(),
                'address_id' => $address->getId(),
                'summary' => array(),
                'items' => array(),
                'gw_items' => array()
            );

            //success
            if (!$result->getHasError()) {
                foreach ($result->getLines() as $ctl) {
                    /** @var OnePica_AvaTax16_Document_Response_Line $ctl */
                    $id = $this->_getItemIdByLine($ctl);
                    $code = $this->_getTaxArrayCodeByLine($ctl);
                    $this->_rates[$requestKey][$code][$id] = array(
                        'rate' => $this->_getLineRate($ctl),
                        'amt'  => $ctl->getCalculatedTax()->getTax(),
                        'jurisdiction_rates' => $this->_getItemJurisdictionRate($ctl)
                    );
                }
                $this->_rates[$requestKey]['summary'] = $this->_getSummaryFromResponse($result);
                //failure
            } else {
                $this->_rates[$requestKey]['failure'] = true;
            }
            Mage::getSingleton('avatax/session')->setAvatax16Rates($this->_rates);
        }

        $rates = isset($this->_rates[$requestKey]) ? $this->_rates[$requestKey] : array();
        return $rates;
    }

    /**
     * Check if destination address have any sense
     *
     * @return bool
     */
    protected function _hasDestinationAddress()
    {
        $hasDestinationAddress = false;
        if ($this->_request->getHeader() && $this->_request->getHeader()->getDefaultLocations()) {
            $locations = $this->_request->getHeader()->getDefaultLocations();

            if (isset($locations[self::TAX_LOCATION_PURPOSE_SHIP_TO])) {
                $shipToLocation = $locations[self::TAX_LOCATION_PURPOSE_SHIP_TO];
                $address = $shipToLocation->getAddress();
                $city = (string)$address->getCity();
                $zip = $address->getZipcode();
                $state = $address->getState();
                $hasDestinationAddress = (($city && $state) || $zip) ? true : false;
            }
        }

        return $hasDestinationAddress;
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
            $this->_setLinesToRequest();
        }

        return count($this->_lines);
    }

    /**
     * Makes a Line object from a product item object
     *
     * @param Varien_Object|Mage_Sales_Model_Quote_Item $item
     * @return bool|int
     * @throws \OnePica_AvaTax_Exception
     */
    protected function _newLine($item)
    {
        if (!$item->getId()) {
            $this->setCanSendRequest(false);

            return false;
        }

        $this->_addGwItemsAmount($item);
        if ($this->isProductCalculated($item)) {
            return false;
        }
        $product = $this->_getProductByProductId($item->getProductId());
        $taxClass = $this->_getTaxClassCodeByProduct($product);
        $price = $item->getBaseRowTotal();
        if ($this->_getTaxDataHelper()->applyTaxAfterDiscount($item->getStoreId())) {
            $price -= $item->getBaseDiscountAmount();
        }

        $lineNumber = $this->_getNewLineCode();

        $line = $this->_getNewDocumentRequestLineObject();
        $line->setLineCode($lineNumber);
        $line->setItemCode(
            $this->_getCalculationHelper()->getItemCode(
                $this->_getProductForItemCode($item),
                $item->getStoreId()
            )
        );
        $line->setNumberOfItems($item->getTotalQty());
        $line->setlineAmount($price);
        $line->setItemDescription($item->getName());
        $discounted = (float)$item->getDiscountAmount()
                      && $this->_getTaxDataHelper()
                          ->applyTaxAfterDiscount($item->getStoreId())
            ? 'true'
            : 'false';

        $line->setDiscounted($discounted);

        if ($this->_getTaxDataHelper()->priceIncludesTax($item->getStoreId())) {
            $line->setTaxIncluded('true');
        }

        if ($taxClass) {
            $line->setAvalaraGoodsAndServicesType($taxClass);
        }

        $metadata = null;
        $ref1Value = $this->_getRefValueByProductAndNumber($product, 1, $item->getStoreId());
        if ($ref1Value) {
            $metadata['ref1'] = $ref1Value;
        }
        $ref2Value = $this->_getRefValueByProductAndNumber($product, 2, $item->getStoreId());
        if ($ref2Value) {
            $metadata['ref2'] = $ref2Value;
        }
        if ($metadata) {
            $line->setMetadata($metadata);
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
        $lineNumber = $this->_getNewLineCode();
        $storeId = $item->getQuote()->getStoreId();
        //Add gift wrapping price(for individual items)
        $gwItemsAmount = $item->getGwBasePrice() * $item->getQty();

        $line = $this->_getNewDocumentRequestLineObject();
        $line->setLineCode($lineNumber);
        $gwItemsSku = $this->_getConfigHelper()->getGwItemsSku($storeId);
        $line->setItemCode($gwItemsSku ? $gwItemsSku : self::DEFAULT_GW_ITEMS_SKU);
        $line->setItemDescription(self::DEFAULT_GW_ITEMS_DESCRIPTION);
        $line->setAvalaraGoodsAndServicesType($this->_getGiftTaxClassCode($storeId));
        $line->setNumberOfItems($item->getQty());
        $line->setlineAmount($gwItemsAmount);
        $line->setDiscounted('false');

        if ($this->_getTaxDataHelper()->priceIncludesTax($storeId)) {
            $line->setTaxIncluded('true');
        }

        $this->_lines[$lineNumber] = $line;
        $this->_setLinesToRequest();
        $this->_lineToLineId[$lineNumber] = $this->_getConfigHelper()->getGwItemsSku($storeId);
        $this->_productGiftPair[$lineNumber] = $item->getId();

        return $lineNumber;
    }

    /**
     * Adds shipping cost to request as item
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return int
     */
    protected function _addShipping($address)
    {
        $lineNumber = $this->_getNewLineCode();
        $storeId = $address->getQuote()->getStore()->getId();
        $taxClass = Mage::helper('tax')->getShippingTaxClass($storeId);
        $shippingAmount = max(
            0.0, (float) $address->getBaseShippingAmount()
        );

        if ($this->_getTaxDataHelper()->applyTaxAfterDiscount($storeId)) {
            $shippingAmount -= (float)$address->getBaseShippingDiscountAmount();
        }

        $line = $this->_getNewDocumentRequestLineObject();
        $line->setLineCode($lineNumber);
        $shippingSku = $this->_getConfigHelper()->getShippingSku($storeId);
        $line->setItemCode($shippingSku ?: self::DEFAULT_SHIPPING_ITEMS_SKU);
        $line->setItemDescription(self::DEFAULT_SHIPPING_ITEMS_DESCRIPTION);
        $line->setAvalaraGoodsAndServicesType($taxClass);
        $line->setNumberOfItems(1);
        $line->setlineAmount($shippingAmount);
        $discounted = (float)$address->getBaseShippingDiscountAmount()
                      && $this->_getTaxDataHelper()->applyTaxAfterDiscount($storeId) ? 'true' : 'false';
        $line->setDiscounted($discounted);

        if ($this->_getTaxDataHelper()->shippingPriceIncludesTax($storeId)) {
            $line->setTaxIncluded('true');
        }

        $this->_lines[$lineNumber] = $line;
        $this->_setLinesToRequest();
        $this->_lineToLineId[$lineNumber] = $shippingSku;

        return $lineNumber;
    }

    /**
     * Adds giftwraporder cost to request as item (for order)
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return int|bool
     */
    protected function _addGwOrderAmount($address)
    {
        if (!$address->getGwPrice()) {
            return false;
        }
        $lineNumber = $this->_getNewLineCode();
        $storeId = $address->getQuote()->getStore()->getId();
        //Add gift wrapping price(for entire order)
        $gwOrderAmount = $address->getGwBasePrice();

        $line = $this->_getNewDocumentRequestLineObject();
        $line->setLineCode($lineNumber);
        $gwOrderSku = $this->_getConfigHelper()->getGwOrderSku($storeId);
        $line->setItemCode($gwOrderSku ? $gwOrderSku : self::DEFAULT_GW_ORDER_SKU);
        $line->setItemDescription(self::DEFAULT_GW_ORDER_DESCRIPTION);
        $line->setAvalaraGoodsAndServicesType($this->_getGiftTaxClassCode($storeId));
        $line->setNumberOfItems(1);
        $line->setlineAmount($gwOrderAmount);
        $line->setDiscounted('false');

        if ($this->_getTaxDataHelper()->priceIncludesTax($storeId)) {
            $line->setTaxIncluded('true');
        }

        $this->_lines[$lineNumber] = $line;
        $this->_setLinesToRequest();
        $this->_lineToLineId[$lineNumber] = $gwOrderSku;
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
        $lineNumber = $this->_getNewLineCode();
        $storeId = $address->getQuote()->getStore()->getId();
        //Add printed card price
        $gwPrintedCardAmount = $address->getGwPrintedCardBasePrice();

        $line = $this->_getNewDocumentRequestLineObject();
        $line->setLineCode($lineNumber);
        $gwPrintedCardSku = $this->_getConfigHelper()->getGwPrintedCardSku($storeId);
        $line->setItemCode($gwPrintedCardSku ? $gwPrintedCardSku : self::DEFAULT_GW_PRINTED_CARD_SKU);
        $line->setItemDescription(self::DEFAULT_GW_PRINTED_CARD_DESCRIPTION);
        $line->setAvalaraGoodsAndServicesType($this->_getGiftTaxClassCode($storeId));
        $line->setNumberOfItems(1);
        $line->setlineAmount($gwPrintedCardAmount);
        $line->setDiscounted('false');

        if ($this->_getTaxDataHelper()->priceIncludesTax($storeId)) {
            $line->setTaxIncluded('true');
        }

        $this->_lines[$lineNumber] = $line;
        $this->_setLinesToRequest();
        $this->_lineToLineId[$lineNumber] = $gwPrintedCardSku;
        return $lineNumber;
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
     * Sends a request to the Avatax16 server
     *
     * @param int $storeId
     * @return mixed
     */
    protected function _send($storeId)
    {
        /** @var OnePica_AvaTax_Model_Service_Avatax16_Config $configModel */
        $configModel = $this->getServiceConfig();
        $config = $configModel->getLibConfig();
        $connection = $configModel->getTaxConnection();
        $result = null;
        $message = null;

        try {
            $result = $connection->createCalculation($this->_request);
        } catch (Exception $exception) {
            $message = $this->_getNewServiceMessageObject();
            $message->setSummary($exception->getMessage());
        }

        if (!isset($result) || !is_object($result)) {
            $actualResult = $result;
            $result = new Varien_Object();
            $result->setHasError(true)
                ->setResultCode(self::RESPONSE_RESULT_CODE_EXCEPTION)
                ->setActualResult($actualResult)
                ->setMessages(array($message));
        }

        $this->_log(
            OnePica_AvaTax_Model_Source_Avatax16_Logtype::CALCULATION,
            $this->_request,
            $result,
            $storeId,
            $config
        );

        return $result;
    }

    /**
     * Get item id/code for given line
     *
     * @param OnePica_AvaTax16_Document_Response_Line $line
     * @return string|int
     */
    protected function _getItemIdByLine($line)
    {
        return isset($this->_productGiftPair[$line->getLineCode()])
            ? $this->_productGiftPair[$line->getLineCode()]
            : $this->_lineToLineId[$line->getLineCode()];
    }

    /**
     * Get tax array code for given line
     *
     * @param OnePica_AvaTax16_Document_Response_Line $line
     * @return string
     */
    protected function _getTaxArrayCodeByLine($line)
    {
        return isset($this->_productGiftPair[$line->getLineCode()]) ? 'gw_items' : 'items';
    }

    /**
     * Get line rate
     *
     * @param OnePica_AvaTax16_Document_Response_Line $line
     * @return float
     */
    protected function _getLineRate($line)
    {
        $rate = 0;
        if ($line->getCalculatedTax()->getTax()) {
            foreach ($line->getCalculatedTax()->getDetails() as $detail) {
                $rate += $detail->getRate();
            }
        }
        return $rate * 100;
    }

    /**
     * Get line rate
     *
     * @param OnePica_AvaTax16_Document_Response $response
     * @return array
     */
    protected function _getSummaryFromResponse($response)
    {
        $result = array();
        $rates = $this->_getJurisdictionsRate($response);
        foreach ($response->getCalculatedTaxSummary()->getTaxByType() as $type => $value) {
            foreach ($value->getJurisdictions() as $data) {
                $jurisdiction = $this->_prepareJurisdictionName(
                    $type,
                    $data->getJurisdictionName(),
                    $data->getJurisdictionType()
                );
                $result[] = array(
                    'name' => $jurisdiction,
                    'rate' => isset($rates[$jurisdiction]) ? $rates[$jurisdiction] : 0,
                    'amt'  => $data->getTax()
                );
            }
        }

        return $result;
    }

    /**
     * Get Jurisdictions rate array
     *
     * @param OnePica_AvaTax16_Document_Response $response
     * @return array
     */
    protected function _getJurisdictionsRate($response)
    {
        $rates = array();
        $fixedRatesData = array();

        /** @var OnePica_AvaTax16_Document_Response_Line $line */
        foreach ($response->getLines() as $line) {
            if (!$line->getCalculatedTax()->getTax()) {
                continue;
            }

            foreach ($line->getCalculatedTax()->getDetails() as $detail) {
                $jurisdiction = $this->_prepareJurisdictionName(
                    $detail->getTaxType(),
                    $detail->getJurisdictionName(),
                    $detail->getJurisdictionType()
                );

                if (!isset($rates[$jurisdiction]) && $detail->getRate()) {
                    $rates[$jurisdiction] = $detail->getRate() * 100;
                }

                if (!$detail->getRate() && $detail->getTax()) {
                    if (!isset($fixedRatesData[$jurisdiction]['fixedTax'])) {
                        $fixedRatesData[$jurisdiction]['fixedTax'] = 0;
                    }
                    if (!isset($fixedRatesData[$jurisdiction]['lineAmount'])) {
                        $fixedRatesData[$jurisdiction]['lineAmount'] = 0;
                    }
                    $fixedRatesData[$jurisdiction]['fixedTax'] += $detail->getTax();
                    $fixedRatesData[$jurisdiction]['lineAmount'] += $line->getLineAmount();
                }
            }
        }

        $fixedRates = array();
        foreach ($fixedRatesData as $jurisdiction => $values) {
            $fixedRates[$jurisdiction] = $this->_calculateRate($values['fixedTax'], $values['lineAmount']);
        }

        return array_merge($rates, $fixedRates);
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
     * Get item jurisdiction rate
     *
     * @param OnePica_AvaTax16_Document_Response_Line $line
     * @return array
     */
    protected function _getItemJurisdictionRate($line)
    {
        $rates = array();
        if ($line->getCalculatedTax()->getTax()) {
            foreach ($line->getCalculatedTax()->getDetails() as $detail) {
                $jurisdiction = $this->_prepareJurisdictionName(
                    $detail->getTaxType(),
                    $detail->getJurisdictionName(),
                    $detail->getJurisdictionType()
                );
                $rates[$jurisdiction] = $detail->getRate() * 100;

                if ($rates[$jurisdiction] === 0 && $detail->getTax()) {
                    $rates[$jurisdiction] = $this->_calculateRate($detail->getTax(), $line->getLineAmount());
                }
            }
        }

        return $rates;
    }

    /**
     * Prepare Jurisdiction name
     *
     * @param string $taxType
     * @param string $jurisdictionName
     * @param string $jurisdictionType
     * @return string
     */
    protected function _prepareJurisdictionName($taxType, $jurisdictionName, $jurisdictionType)
    {
        $name = preg_replace('/(?<!\ )[A-Z]/', ' $0', $taxType)
                . ': '
                . $jurisdictionName
                . ' '
                . $jurisdictionType;

        return ucfirst(trim($name));
    }

    /**
     * Calculate rate
     *
     * @param float $tax
     * @param float $amount
     * @return float
     */
    protected function _calculateRate($tax, $amount)
    {
        return $this->_getHelper()->roundUp(($tax / $amount) * 100, 2);
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
