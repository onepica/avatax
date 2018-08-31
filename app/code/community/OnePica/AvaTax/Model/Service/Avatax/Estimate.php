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
     * @throws \Varien_Exception
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
        // Add landed Cost Params
        $this->_initLandedCostModeParam($address);
        $this->_addGeneralLandedCostInfo($address);
        $this->_addGeneralInfo($address);
        $this->_setOriginAddressFromModel($quote);
        $this->_setDestinationAddress($address);
        $this->_setDetailLevel();
        $this->_addItemsInCart($address);
        $this->_addShipping($address);
        $this->_addLandedCostShippingInsurance($address);
        //Added code for calculating tax for giftwrap items
        $this->_addGwOrderAmount($address);
        $this->_addGwPrintedCardAmount($address);

        Mage::dispatchEvent('avatax_make_request_before', array('request' =>  $this->_request));

        //check to see if we can/need to make the request to Avalara
        $requestKey = $this->_genRequestKey();
        $makeRequest = empty($this->_rates[$requestKey]['items']);
        $makeRequest &= count($this->_lineToLineId) ? true : false;
        $makeRequest &= $this->_request->getDestinationAddress() == '' ? false : true;
        $makeRequest &= $address->getId() ? true : false;
        $makeRequest &= !isset($this->_rates[$requestKey]['failure']);
        $makeRequest &= $this->isCanSendRequest();

        //make request if needed and save results in cache
        if ($makeRequest) {
            $quoteData = new Varien_Object(
                array(
                    'quote_id'         => $address->getQuoteId(),
                    'quote_address_id' => $address->getId()
                )
            );
            $result = $this->_send($quote->getStoreId(), $quoteData);

            $this->_rates[$requestKey] = array(
                'timestamp'          => $this->_getDateModel()->timestamp(),
                'address_id'         => $address->getId(),
                'summary'            => array(),
                'items'              => array(),
                'gw_items'           => array(),
                'landed_cost_amount' => null,
                'fixed_tax_amount'   => null
            );

            //success
            /** @var GetTaxResult $result */
            if ($result->getResultCode() == SeverityLevel::$Success) {
                /** @var \TaxLine $ctl */
                $this->_rates[$requestKey]['landed_cost_amount'] = null;

                foreach ($result->getTaxLines() as $ctl) {
                    /** @var TaxLine $ctl */
                    $id = $this->_getItemIdByLine($ctl);
                    $code = $this->_getTaxArrayCodeByLine($ctl);

                    $this->_rates[$requestKey][$code][$id] = array(
                        'rate'         => $this->_getTaxRateFromTaxLineItem($ctl),
                        'amt'          => $ctl->getTax(),
                        'taxable'      => $ctl->getTaxable(),
                        'tax_included' => $ctl->getTaxIncluded(),
                        'summary'      => $this->_getSummaryFromTaxLineItem($ctl)
                    );

                    if ($this->_getLandedCostHelper()->isLandedCostEnabled($quote->getStoreId())) {
                        $landedCostLine = $this->_collectLandedCostLine($ctl);
                        /* line level */
                        $this->_rates[$requestKey][$code][$id]['landed_cost_amount'] = $landedCostLine->getTax();
                        $this->_rates[$requestKey][$code][$id]['landed_cost_items'] = $landedCostLine->getTaxDetails();

                        /* request level */
                        $landedCostAmt = $this->_rates[$requestKey]['landed_cost_amount'] + $landedCostLine->getTax();
                        $this->_rates[$requestKey]['landed_cost_amount'] = $landedCostAmt;;
                    }

                    if ($this->_getFixedTaxHelper()->isFixedTaxEnabled($quote->getStoreId())) {
                        $fixedTaxLine = $this->_collectFixedTaxLine($ctl);
                        /* line level */
                        $this->_rates[$requestKey][$code][$id]['fixed_tax_amount'] = $fixedTaxLine->getTax();
                        $this->_rates[$requestKey][$code][$id]['fixed_tax_items'] = $fixedTaxLine->getTaxDetails();

                        /* request level */
                        $fixedTaxAmt = $this->_rates[$requestKey]['fixed_tax_amount'] + $fixedTaxLine->getTax();
                        $this->_rates[$requestKey]['fixed_tax_amount'] = $fixedTaxAmt;
                    }
                }

                $this->_rates[$requestKey]['summary'] = $this->_getSummaryFromResponse($result);
                $this->_rates[$requestKey]['landed_cost_message'] = $this->_getLandedCostMessage($result);
                //failure
            } else {
                $this->_rates[$requestKey]['failure'] = true;
                $this->_rates[$requestKey]['failure_details'] = $this->_getFailureDetails($result);
            }

            Mage::getSingleton('avatax/session')->setRates($this->_rates);
        }

        $rates = isset($this->_rates[$requestKey]) ? $this->_rates[$requestKey] : array();

        return $rates;
    }

    /**
     * @param \TaxLine $taxLine
     * @return \Varien_Object
     */
    protected function _collectLandedCostLine($taxLine)
    {
        $landedCostTaxLine = array(
            'no'                => $taxLine->getNo(),
            'tax_code'          => $taxLine->getTaxCode(),
            'taxability'        => $taxLine->getTaxability(),
            'boundary_level'    => $taxLine->getBoundaryLevel(),
            'reporting_date'    => $taxLine->getReportingDate(),
            'accounting_method' => $taxLine->getAccountingMethod(),
            'tax_included'      => $taxLine->getTaxIncluded(),
            'exempt_cert_id'    => $taxLine->getExemptCertId(),
            'exemption'         => 0,
            'taxable'           => 0,
            'tax'               => 0,
            'tax_details'       => array(),
        );

        /** @var \TaxDetail $taxDetail */
        foreach ($taxLine->getTaxDetails() as $taxDetail) {
            if ($this->_getLandedCostHelper()->isLandedCostTax($taxDetail)) {
                $landedCostTaxLine['tax_details'][] = $taxDetail;
                $landedCostTaxLine['exemption'] = $landedCostTaxLine['exemption'] + $taxDetail->getExemption();
                $landedCostTaxLine['taxable'] = $landedCostTaxLine['taxable'] + $taxDetail->getTaxable();
                $landedCostTaxLine['tax'] = $landedCostTaxLine['tax'] + $taxDetail->getTax();
            }
        }

        return new Varien_Object($landedCostTaxLine);
    }

    /**
     * @param \TaxLine $taxLine
     * @return \Varien_Object
     */
    protected function _collectFixedTaxLine($taxLine)
    {
        $fixedTaxLine = array(
            'no'                => $taxLine->getNo(),
            'tax_code'          => $taxLine->getTaxCode(),
            'taxability'        => $taxLine->getTaxability(),
            'boundary_level'    => $taxLine->getBoundaryLevel(),
            'reporting_date'    => $taxLine->getReportingDate(),
            'accounting_method' => $taxLine->getAccountingMethod(),
            'tax_included'      => $taxLine->getTaxIncluded(),
            'exempt_cert_id'    => $taxLine->getExemptCertId(),
            'exemption'         => 0,
            'taxable'           => 0,
            'tax'               => 0,
            'tax_details'       => array(),
        );

        /** @var \TaxDetail $taxDetail */
        foreach ($taxLine->getTaxDetails() as $taxDetail) {
            if ($this->_getFixedTaxHelper()->isFixedTax($taxDetail)) {
                $fixedTaxLine['tax_details'][] = $taxDetail;
                $fixedTaxLine['exemption'] = $fixedTaxLine['exemption'] + $taxDetail->getExemption();
                $fixedTaxLine['taxable'] = $fixedTaxLine['taxable'] + $taxDetail->getTaxable();
                $fixedTaxLine['tax'] = $fixedTaxLine['tax'] + $taxDetail->getTax();
            }
        }

        return new Varien_Object($fixedTaxLine);
    }

    /**
     * Get response failure details
     *
     * @param GetTaxResult $response
     *
     * @return null|string
     */
    protected function _getFailureDetails($response)
    {
        $details = null;

        $messages = $response->getMessages();
        if ($messages) {
            /** @var Message $message */
            foreach ($messages as $message) {
                if ($this->_ignoreResponseMessage($message)) {
                    continue;
                }

                $details = (isset($details)) ? $details . ' ' : $details;

                $messageSummary = $message->getSummary();
                if ($messageSummary) {
                    $messageSummary = $this->_getHelper()->__($messageSummary);
                    $details = $details . $messageSummary;
                }
            }

            if ($details) {
                $details = $this->_getHelper()->__('More details: ') . $details;
            }
        }

        return $details;
    }

    /**
     * Checks whether error response message should be ignored during showing failure details to customer.
     *
     * @param Message $message
     *
     * @return bool
     */
    protected function _ignoreResponseMessage($message)
    {
        $goodSeverity = in_array($message->getSeverity(), array('Warning', 'Error'));
        $goodSource = in_array(
            $message->getSource(),
            array(
                'Avalara.AvaTax.Services.Address',
                'Avalara.AvaTax.Services.Tax',
                'Avalara.AvaTax.Services.Tax.Steps'
            )
        );

        $badName = in_array(
            $message->getName(),
            array(
                'CompanyNotFoundError',
            )
        );

        return !$goodSeverity || !$goodSource || $badName;
    }

    /**
     * Get line rate
     * Prepares array of tax lines with unique names for correct displaying in Full Tax Summary
     *
     * @param GetTaxResult $response
     * @return array
     */
    protected function _getSummaryFromResponse($response)
    {
        $unique = array();
        $result = array();
        $taxSummaryItems = $this->_getTaxSummaryItemsFromResponse($response);

        /** @var array $row */
        foreach ($taxSummaryItems as $row) {
            $name = $row['name'];
            $unique[$name] = (isset($unique[$name])) ? $unique[$name] + 1 : 1;
        }

        foreach ($taxSummaryItems as $key => $row) {
            $name = $row['name'];
            $row['name'] = ($unique[$name] > 1) ? $key : $name;

            $result[] = $row;
        }

        return $result;
    }

    /**
     * Get line rate
     * Prepares array of tax lines with unique names for correct displaying in Full Tax Summary
     *
     * @param \TaxLine $line
     * @return array
     */
    protected function _getSummaryFromTaxLineItem(TaxLine $line)
    {
        return $this->_collectSummaryDetail($line->getTaxDetails());
    }

    /**
     * Prepares array of arrays with data from TaxDetail for different detail levels
     *
     * @param GetTaxResult $response
     *
     * @return array
     */
    protected function _getTaxSummaryItemsFromResponse($response)
    {
        $taxDetailItems = $this->_getTaxDetailItemsFromResponse($response);

        return $this->_collectSummaryDetail($taxDetailItems);
    }

    /**
     * Prepares array of arrays with data from TaxDetail by jurisdiction
     *
     * @param \TaxDetail[] $taxDetailItems
     *
     * @return array
     */
    protected function _collectSummaryDetail($taxDetailItems)
    {
        /**
         * Variables
         *
         * @var TaxDetail $taxDetail
         * @var string    $resultKey used to collect tax amount for separate jurisdiction
         */

        $result = array();

        foreach ($taxDetailItems as $taxDetail) {
            $taxName = $taxDetail->getTaxName();
            $rate = $taxDetail->getRate() * 100;

            if ($this->_getLandedCostHelper()->isLandedCostTax($taxDetail)) {
                $taxName = $taxDetail->getTaxSubTypeId();
                $rate = null;
            }

            if ($this->_getFixedTaxHelper()->isFixedTax($taxDetail)) {
                $taxName = $taxDetail->getTaxSubTypeId();
                $rate = null;
            }

            $resultKey = $taxName . " " . $taxDetail->getJurisCode();
            if (array_key_exists($resultKey, $result)) {
                $amt = $result[$resultKey]['amt'] + $taxDetail->getTax();
            } else {
                $amt = $taxDetail->getTax();
            }

            $result[$resultKey] = array(
                'name'               => $taxName,
                'rate'               => $rate,
                'amt'                => $amt,
                'avatax_tax_type'    => $taxDetail->getTaxType(),
                'avatax_tax_subtype' => $taxDetail->getTaxSubTypeId()
            );
        }

        return $result;
    }

    /**
     * Prepares array of Tax Detail Items with data from TaxDetail for different detail levels
     *
     * @param GetTaxResult $response
     *
     * @return array
     */
    protected function _getTaxDetailItemsFromResponse($response)
    {
        $taxDetailItems = array();
        switch ($this->_request->getDetailLevel()) {
            case DetailLevel::$Tax:
                // Response Detail Level = Tax
                /** @var TaxLine $taxLine */
                foreach ($response->getTaxLines() as $taxLine) {
                    /** @var TaxDetail $taxDetail */
                    foreach ($taxLine->getTaxDetails() as $taxDetail) {
                        $taxDetailItems[] = $taxDetail;
                    }
                }
                break;
            default:
                // Response Detail Level = Line
                $taxDetailItems = $response->getTaxSummary();
                break;
        }

        return $taxDetailItems;
    }

    /**
     * Generates a hash key for the exact request
     *
     * @return string
     */
    protected function _genRequestKey()
    {
        $hashSrc = $this->_genRequestKeySrc();
        $hash = crc32($hashSrc);
        $this->_setLastRequestKey($hash);

        return $hash;
    }

    /**
     * Build request key source string
     *
     * @return null|string
     */
    protected function _genRequestKeySrc()
    {
        //init hash src with serialized request
        $hashSources = new Varien_Object(
            array('request' => serialize($this->_request))
        );

        //add quote address item ids to hash source
        foreach ($this->_lineToLineId as $index => $itemId) {
            $hashSrcLines = ($hashSources->hasLines()) ? $hashSources->getLines() . ';' . $itemId : $itemId;
            $hashSources->setLines($hashSrcLines);
        }

        //add quote address gifts item ids to hash source
        foreach ($this->_productGiftPair as $index => $itemId) {
            $hashSrcGifts = ($hashSources->hasGifts()) ? $hashSources->getGifts() . ';' . $itemId : $itemId;
            $hashSources->setGifts($hashSrcGifts);
        }

        //build hash source string
        $hashSrc = null;
        foreach ($hashSources->getData() as $key => $value) {
            $hashSrc = (isset($hashSrc)) ? $hashSrc . '|' . "$key:$value" : "$key:$value";
        }

        return $hashSrc;
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
        $shippingAmount = max(0.0, (float)$address->getBaseShippingAmount());

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
     * Adds shipping insurance to request as item
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return int
     */
    protected function _addLandedCostShippingInsurance($address)
    {
        $lineNumber = count($this->_lines);

        if ($this->getLandedCostMode()) {
            $storeId = $this->_getStoreIdByObject($address);

            $insurance = new \Varien_Object(array('amount' => null, 'document_type' => 'order', 'object' => $address));
            Mage::dispatchEvent('avatax_landed_cost_request_tax_insurance_needs', array('insurance' => $insurance));

            if ($insurance->getAmount() !== null) {
                $insuranceAmount = $insurance->getAmount();

                $line = new Line();
                $line->setNo($lineNumber);
                $shippingSku = $this->_getLandedCostHelper()->getShippingInsuranceSku($storeId);
                $line->setItemCode($shippingSku ?: 'ShippingInsurance');
                $line->setDescription('Insurance');
                $line->setTaxCode($this->_getLandedCostHelper()->getShippingInsuranceTaxCode($storeId));
                $line->setQty(1);
                $line->setAmount($insuranceAmount);

                $this->_lines[$lineNumber] = $line;
                $this->_request->setLines($this->_lines);
                $this->_lineToLineId[$lineNumber] = $this->_getLandedCostHelper()->getShippingInsuranceSku($storeId);
            }
        }

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
                $this->_newLine($item, $address);
            }

            $this->_request->setLines($this->_lines);
        }

        return count($this->_lines);
    }

    /**
     * Makes a Line object from a product item object
     *
     * @param Varien_Object|Mage_Sales_Model_Quote_Item $item
     * @param Mage_Sales_Model_Quote_Address            $address
     * @return int|bool
     * @throws \OnePica_AvaTax_Exception
     * @throws \Varien_Exception
     */
    protected function _newLine($item, $address)
    {
        if (!$item->getId()) {
            $this->setCanSendRequest(false);

            return $this;
        }

        $this->_addGwItemsAmount($item);
        if ($this->isProductCalculated($item)) {
            return false;
        }

        $product = $this->_getProductByProductId($this->_retrieveProductIdFromQuoteItem($item));
        $this->_newLinePrepareProduct(new \Varien_Object(array('product' => $product, 'item' => $item)));
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

        $this->_addLandedCostParamsToLine($line, $product, $address);

        $this->_newLineMakeAdditionalProcessingForLine(
            new \Varien_Object(array('product' => $product, 'item' => $item, 'line' => $line))
        );
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

    /**
     * Sets detail level for request based on config data
     *
     * @return $this
     */
    protected function _setDetailLevel()
    {
        /*
         * Always set detailLevel = tax, because need more information about tax
         * especially for "bottle tax" and landedcost feature
         */
        $this->_request->setDetailLevel(DetailLevel::$Tax);

        return $this;
    }

    /**
     * Calculates rate of tax line
     *
     * For config -> detailLevel = tax get rate only if line tax is not 0
     * For config -> detailLevel = line we always summarize rate
     *
     * @param TaxLine $line
     * @return int
     */
    protected function _getTaxRateFromTaxLineItem(TaxLine $line)
    {
        $lineRate = 0;

        /** @var OnePica_AvaTax_Model_Service_Avatax_Config $config */
        $config = Mage::getSingleton('avatax/service_avatax_config');

        /** @var \TaxDetail $taxDetail */
        foreach ($line->getTaxDetails() as $taxDetail) {
            /* We don't need to summarize the landedcost rate */
            if ($this->_getLandedCostHelper()->isLandedCostTax($taxDetail)) {
                continue;
            }

            /* We don't need to summarize the fixed tax rate */
            if ($this->_getFixedTaxHelper()->isFixedTax($taxDetail)) {
                continue;
            }

            /* add taxDetail rate to line rate based on tax detail level */
            if ($config->getDetailLevel() == DetailLevel::$Tax) {
                if ($taxDetail->getTax() != 0) {
                    $lineRate = $lineRate + $taxDetail->getRate();
                }
            } else {
                $lineRate = $lineRate + $taxDetail->getRate();
            }
        }

        $lineRate = $lineRate * 100;

        return $lineRate;
    }

    /**
     * Retrieve Landed cost message
     *
     * @param GetTaxResult $result
     * @return null|string
     */
    protected function _getLandedCostMessage(GetTaxResult $result)
    {
        $messages = array();
        try {
            /** @var \Message $message */
            foreach ($result->getMessages() as $message) {
                if ($message->getRefersTo() === OnePica_AvaTax_Helper_LandedCost::AVATAX_LANDED_COST_TAX_TYPE) {
                    $messages[] = $message->getSummary();
                }
            }
        } catch (Exception $e) {
            // Avalara's lib throws exception "Trying to get property of non-object" when "messages" is empty array
        }

        return implode('<br/>', $messages);
    }
}
