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

        // Set up document for request
        $this->_request = $this->_getNewDocumentRequestObject();

        // set up header
        $header = $this->_getRequestHeaderWithMainValues($storeId);
        $header->setDocumentCode('quote-' . $address->getId());
        $header->setTransactionDate($this->_getDateModel()->date('Y-m-d'));
        $header->setDefaultLocations($this->_getHeaderDefaultLocations($address));

        $this->_request->setHeader($header);

        $this->_addItemsInCart($item);
        $this->_addShipping($address);
        //Added code for calculating tax for giftwrap items (order)
        $this->_addGwOrderAmount($address);
        $this->_addGwPrintedCardAmount($address);

        //check to see if we can/need to make the request to Avalara
        $requestKey = $this->_genRequestKey();
        $makeRequest = empty($this->_rates[$requestKey]['items']);
        //@startSkipCommitHooks
        $makeRequest &= count($this->_lineToLineId) ? true : false;

        $hasDestinationAddress = false;
        if ($this->_request->getHeader() && $this->_request->getHeader()->getDefaultLocations()) {
            $locations = $this->_request->getHeader()->getDefaultLocations();
            $hasDestinationAddress = isset($locations[self::TAX_LOCATION_PURPOSE_SHIP_TO]) ? true : false;
        }

        $makeRequest &= $hasDestinationAddress;
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
                        'amt' => $ctl->getCalculatedTax()->getTax(),
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
            $this->_setLinesToRequest();
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
        $lineNumber = $this->_getNewLineCode();

        $line = $this->_getNewDocumentRequestLineObject();
        $line->setLineCode($lineNumber);
        $line->setItemCode(substr($item->getSku(), 0, 50));
        $line->setNumberOfItems($item->getQty());
        $line->setlineAmount($price);
        $line->setItemDescription($item->getName());
        $line->setDiscounted($item->getDiscountAmount() ? 'true' : 'false');

        if ($taxClass) {
            $line->setTaxCode($taxClass);
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
        $lineNumber = $this->_getNewLineCode();
        $storeId = $item->getQuote()->getStoreId();
        //Add gift wrapping price(for individual items)
        $gwItemsAmount = $item->getGwBasePrice() * $item->getQty();

        $line = $this->_getNewDocumentRequestLineObject();
        $line->setLineCode($lineNumber);
        $gwItemsSku = $this->_getConfigHelper()->getGwItemsSku($storeId);
        $line->setItemCode($gwItemsSku ? $gwItemsSku : self::DEFAULT_GW_ITEMS_SKU);
        $line->setItemDescription(self::DEFAULT_GW_ITEMS_DESCRIPTION);
        $line->setTaxCode($this->_getGiftTaxClassCode($storeId));
        $line->setNumberOfItems($item->getQty());
        $line->setlineAmount($gwItemsAmount);
        $line->setDiscounted('false');

        $this->_lines[$lineNumber] = $line;
        $this->_setLinesToRequest();
        $this->_lineToLineId[$lineNumber] = $this->_getConfigHelper()->getGwItemsSku($storeId);
        $this->_productGiftPair[$lineNumber] = $item->getSku();

        return $lineNumber;
    }

    /**
     * Adds shipping cost to request as item
     *
     * @param Mage_Sales_Model_Quote_Address
     * @return int
     */
    protected function _addShipping($address)
    {
        $lineNumber = $this->_getNewLineCode();
        $storeId = $address->getQuote()->getStore()->getId();
        $taxClass = Mage::helper('tax')->getShippingTaxClass($storeId);
        $shippingAmount = (float) $address->getBaseShippingAmount();

        $line = $this->_getNewDocumentRequestLineObject();
        $line->setLineCode($lineNumber);
        $shippingSku = $this->_getConfigHelper()->getShippingSku($storeId);
        $line->setItemCode($shippingSku ? $shippingSku : self::DEFAULT_SHIPPING_ITEMS_SKU);
        $line->setItemDescription(self::DEFAULT_SHIPPING_ITEMS_DESCRIPTION);
        $line->setTaxCode($taxClass);
        $line->setNumberOfItems(1);
        $line->setlineAmount($shippingAmount);
        $line->setDiscounted('false');

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
        $line->setTaxCode($this->_getGiftTaxClassCode($storeId));
        $line->setNumberOfItems(1);
        $line->setlineAmount($gwOrderAmount);
        $line->setDiscounted('false');

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
        $line->setTaxCode($this->_getGiftTaxClassCode($storeId));
        $line->setNumberOfItems(1);
        $line->setlineAmount($gwPrintedCardAmount);
        $line->setDiscounted('false');

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
        Mage::getSingleton('avatax/session')->setLastRequestKey($hash);
        return $hash;
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
        $connection = $configModel->getAddressConnection();
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
        foreach ($response->getCalculatedTaxSummary()->getTaxByType() as $taxItemByType) {
            foreach ($taxItemByType->getJurisdictions() as $data) {
                $result[] = array(
                    'name' => $data->getJurisdictionName() . '_' . $data->getJurisdictionType(),
                    'rate' => '',
                    'amt' => $data->getTax()
                );
            }
        }
        return $result;
    }

    /**
     * Get tax detail summary
     *
     * @param int|null $addressId
     * @return array
     */
    public function getSummary($addressId = null)
    {
        $summary = null;

        if ($addressId) {
            $timestamp = 0;
            foreach ($this->_rates as $row) {
                if (isset($row['address_id']) && $row['address_id'] == $addressId && $row['timestamp'] > $timestamp) {
                    $summary = $row['summary'];
                    $timestamp = $row['timestamp'];
                }
            }
        }

        if ($summary === null) {
            $requestKey = Mage::getSingleton('avatax/session')->getLastRequestKey();
            $summary = isset($this->_rates[$requestKey]['summary']) ? $this->_rates[$requestKey]['summary'] : array();
        }

        return $summary;
    }
}
