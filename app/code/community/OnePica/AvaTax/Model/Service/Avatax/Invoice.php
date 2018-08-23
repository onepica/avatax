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
 * @copyright  Copyright (c) 2009 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * The AvaTax Invoice model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Avatax_Invoice extends OnePica_AvaTax_Model_Service_Avatax_Tax
{
    /**
     * An array of line items
     *
     * @var array
     */
    protected $_lines = array();

    /**
     * An array of line numbers to product ids
     *
     * @var array
     */
    protected $_lineToItemId = array();

    /**
     * Save order in AvaTax system
     *
     * @see OnePica_AvaTax_Model_Observer_SalesOrderInvoiceSaveAfter::execute()
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @param OnePica_AvaTax_Model_Records_Queue $queue
     * @return OnePica_AvaTax_Model_Service_Result_Invoice
     */
    public function invoice($invoice, $queue)
    {
        $this->_lines = array();
        $order = $invoice->getOrder();
        $storeId = $order->getStoreId();
        $invoiceDate = $this->_convertGmtDate($invoice->getCreatedAt(), $storeId);
        $orderDate = $this->_convertGmtDate($order->getCreatedAt(), $storeId);
        $statusDate = $this->_convertGmtDate($queue->getUpdatedAt(), $storeId);

        $shippingAddress = ($order->getShippingAddress()) ? $order->getShippingAddress() : $order->getBillingAddress();

        $this->_request = new GetTaxRequest();
        $this->_request->setDocCode($invoice->getIncrementId());
        $this->_request->setDocType(DocumentType::$SalesInvoice);

        $this->_initLandedCostModeParam($order);
        $this->_addGeneralLandedCostInfo($order);
        $this->_addGeneralInfo($order);
        $this->_addShipping($invoice);
        $this->_addLandedCostShippingInsurance($invoice);
        $items = $invoice->getItemsCollection();
        $this->_initProductCollection($items);
        $this->_initTaxClassCollection($invoice);
        //Added code for calculating tax for giftwrap items
        $this->_addGwOrderAmount($invoice);
        $this->_addGwItemsAmount($invoice);
        $this->_addGwPrintedCardAmount($invoice);

        $this->_setOriginAddressFromModel($order);
        $this->_setDestinationAddress($shippingAddress);

        $this->_request->setDocDate($invoiceDate);
        $this->_request->setPaymentDate($invoiceDate);

        $configAction = Mage::getStoreConfig('tax/avatax/action', $order->getStoreId());
        $commitAction = OnePica_AvaTax_Model_Service_Abstract_Config::ACTION_CALC_SUBMIT_COMMIT;
        $this->_request->setCommit(($configAction == $commitAction) ? true : false);

        foreach ($items as $item) {
            /** @var Mage_Sales_Model_Order_Invoice_Item $item */
            $this->_newLine($item, $shippingAddress);
        }

        $this->_request->setLines($this->_lines);

        $quoteData = new Varien_Object(array(
            'quote_id'         => $order->getQuoteId(),
            'quote_address_id' => $shippingAddress->getAvataxQuoteAddressId()
        ));
        //send to AvaTax
        $result = $this->_send($order->getStoreId(), $quoteData);

        /** @var OnePica_AvaTax_Model_Service_Result_Invoice $invoiceResult */
        $invoiceResult = Mage::getModel('avatax/service_result_invoice');
        $resultHasError = $result->getResultCode() != SeverityLevel::$Success;
        $invoiceResult->setHasError($resultHasError);

        //if successful
        if (!$resultHasError) {
            $totalTax = $result->getTotalTax();
            $invoiceResult->setTotalTax($totalTax);
            $documentCode = $result->getDocCode();
            $invoiceResult->setDocumentCode($documentCode);

            //if not successful
        } else {
            $messages = array();
            foreach ($result->getMessages() as $message) {
                $messages[] = $message->getSummary();
            }

            $invoiceResult->setErrors($messages);
        }

        return $invoiceResult;
    }

    /**
     * Save order in AvaTax system
     *
     * @see OnePica_AvaTax_Model_Observer_SalesOrderCreditmemoSaveAfter::execute()
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @param OnePica_AvaTax_Model_Records_Queue $queue
     * @return OnePica_AvaTax_Model_Service_Result_Creditmemo
     */
    public function creditmemo($creditmemo, $queue)
    {
        $this->_lines = array();
        $order = $creditmemo->getOrder();
        $storeId = $order->getStoreId();
        $orderDate = $this->_convertGmtDate($order->getCreatedAt(), $storeId);
        $statusDate = $this->_convertGmtDate($queue->getUpdatedAt(), $storeId);
        $creditmemoDate = $this->_convertGmtDate($creditmemo->getCreatedAt(), $storeId);

        $shippingAddress = ($order->getShippingAddress()) ? $order->getShippingAddress() : $order->getBillingAddress();

        $this->_request = new GetTaxRequest();
        $this->_request->setDocCode($creditmemo->getIncrementId());
        $this->_request->setDocType(DocumentType::$ReturnInvoice);

        $this->_initLandedCostModeParam($order);
        $this->_addGeneralLandedCostInfo($order);
        $this->_addGeneralInfo($order);
        $this->_addShipping($creditmemo, true);
        $this->_addLandedCostShippingInsurance($creditmemo, true);

        $items = $creditmemo->getAllItems();
        $this->_initProductCollection($items);
        $this->_initTaxClassCollection($creditmemo);
        //Added code for calculating tax for giftwrap items
        $this->_addGwOrderAmount($creditmemo, true);
        $this->_addGwItemsAmount($creditmemo, true);
        $this->_addGwPrintedCardAmount($creditmemo, true);

        $this->_addAdjustments(
            $creditmemo->getBaseAdjustmentPositive(),
            $creditmemo->getBaseAdjustmentNegative(),
            $order->getStoreId()
        );
        $this->_setOriginAddressFromModel($order);
        $this->_setDestinationAddress($shippingAddress);

        // Set the tax date for calculation.
        $override = new TaxOverride();
        $override->setTaxDate($orderDate);
        $override->setTaxOverrideType(TaxOverrideType::$TaxDate);
        $override->setReason('Credit memo - refund');
        $this->_request->setTaxOverride($override);

        $this->_request->setDocDate($creditmemoDate);
        $this->_request->setPaymentDate($creditmemoDate);

        $configAction = Mage::getStoreConfig('tax/avatax/action', $order->getStoreId());
        $commitAction = OnePica_AvaTax_Model_Service_Abstract_Config::ACTION_CALC_SUBMIT_COMMIT;
        $this->_request->setCommit(($configAction == $commitAction) ? true : false);

        foreach ($items as $item) {
            /** @var Mage_Sales_Model_Order_Creditmemo_Item $item */
            $this->_newLine($item, $shippingAddress, true);
        }

        $this->_request->setLines($this->_lines);

        $quoteData = new Varien_Object(array(
            'quote_id'         => $order->getQuoteId(),
            'quote_address_id' => $shippingAddress->getAvataxQuoteAddressId()
        ));
        //send to AvaTax
        $result = $this->_send($order->getStoreId(), $quoteData);

        /** @var OnePica_AvaTax_Model_Service_Result_Creditmemo $creditmemoResult */
        $creditmemoResult = Mage::getModel('avatax/service_result_creditmemo');
        $resultHasError = $result->getResultCode() != SeverityLevel::$Success;
        $creditmemoResult->setHasError($resultHasError);

        //if successful
        if (!$resultHasError) {
            $totalTax = $result->getTotalTax();
            $creditmemoResult->setTotalTax($totalTax);
            $documentCode = $result->getDocCode();
            $creditmemoResult->setDocumentCode($documentCode);

            //if not successful
        } else {
            $messages = array();
            foreach ($result->getMessages() as $message) {
                $messages[] = $message->getSummary();
            }

            $creditmemoResult->setErrors($messages);
        }

        return $creditmemoResult;
    }

    /**
     * Adds shipping cost to request as item
     *
     * @param Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo $object
     * @param bool $credit
     * @return int|bool
     */
    protected function _addShipping($object, $credit = false)
    {
        if ($object->getBaseShippingAmount() == 0) {
            return false;
        }

        $order = $object->getOrder();

        $lineNumber = count($this->_lines);
        $storeId = $object->getStore()->getId();
        $taxClass = Mage::helper('tax')->getShippingTaxClass($storeId);

        $line = new Line();
        $amount = (float)$object->getBaseShippingAmount();

        if ($this->_getTaxDataHelper()->priceIncludesTax($storeId)) {
            $amount = (float)$object->getBaseShippingInclTax();
            $line->setTaxIncluded(true);
        }

        if ($this->_getTaxDataHelper()->applyTaxAfterDiscount()) {
            $amount -= (float)$order->getBaseShippingDiscountAmount();
        }

        if ($credit) {
            $amount *= -1;
        }

        $line->setNo($lineNumber);
        $line->setItemCode($this->_getConfigHelper()->getShippingSku($storeId));
        $line->setDescription('Shipping costs');
        $line->setTaxCode($taxClass);
        $line->setQty(1);
        $line->setAmount($amount);
        $line->setDiscounted(
            (float)$order->getBaseShippingDiscountAmount()
            && $this->_getTaxDataHelper()->applyTaxAfterDiscount($storeId)
        );

        $this->_lineToItemId[$lineNumber] = 'shipping';
        $this->_lines[$lineNumber] = $line;
        $this->_request->setLines($this->_lines);
        return $lineNumber;
    }

    /**
     * Adds shipping insurance to request as item
     *
     * @param Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo $object
     * @param bool $credit
     * @return int
     */
    protected function _addLandedCostShippingInsurance($object, $credit = false)
    {
        $lineNumber = count($this->_lines);

        if ($this->getLandedCostMode()) {
            $storeId = $this->_getStoreIdByObject($object);

            $insurance = new \Varien_Object(array('amount' => null, 'document_type' => ($credit ? 'creditmemo' : 'invoice'), 'object' => $object));
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
     * @param Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo $object
     * @param bool $credit
     * @return int|bool
     */
    protected function _addGwOrderAmount($object, $credit = false)
    {
        if ($object->getGwPrice() == 0) {
            return false;
        }

        $lineNumber = count($this->_lines);
        $storeId = $object->getStore()->getId();

        $amount = $object->getGwBasePrice();

        $line = new Line();
        if ($this->_getTaxDataHelper()->priceIncludesTax($storeId)) {
            $amount += $object->getGwBaseTaxAmount();
            $line->setTaxIncluded(true);
        }

        if ($credit) {
            $amount *= -1;
        }

        $line->setNo($lineNumber);
        $line->setItemCode($this->_getConfigHelper()->getGwOrderSku($storeId));
        $line->setDescription('Gift Wrap Order Amount');
        $line->setTaxCode($this->_getGiftTaxClassCode($storeId));
        $line->setQty(1);
        $line->setAmount($amount);
        $line->setDiscounted(false);

        $this->_lineToItemId[$lineNumber] = $this->_getConfigHelper()->getGwOrderSku($storeId);
        $this->_lines[$lineNumber] = $line;
        $this->_request->setLines($this->_lines);
        return $lineNumber;
    }

    /**
     * Adds giftwrapitems cost to request as item
     *
     * @param Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo $object
     * @param bool $credit
     * @return int|bool
     */
    protected function _addGwItemsAmount($object, $credit = false)
    {
        if ($object->getGwItemsPrice() == 0) {
            return false;
        }

        $lineNumber = count($this->_lines);
        $storeId = $object->getStore()->getId();

        $amount = $object->getGwItemsBasePrice();

        $line = new Line();
        if ($this->_getTaxDataHelper()->priceIncludesTax($storeId)) {
            $amount += $object->getGwItemsBaseTaxAmount();
            $line->setTaxIncluded(true);
        }

        if ($credit) {
            $amount *= -1;
        }

        $line->setNo($lineNumber);
        $line->setItemCode($this->_getConfigHelper()->getGwItemsSku($storeId));
        $line->setDescription('Gift Wrap Items Amount');
        $line->setTaxCode($this->_getGiftTaxClassCode($storeId));
        $line->setQty(1);
        $line->setAmount($amount);
        $line->setDiscounted(false);

        $this->_lineToItemId[$lineNumber] = $this->_getConfigHelper()->getGwItemsSku($storeId);
        $this->_lines[$lineNumber] = $line;
        $this->_request->setLines($this->_lines);
        return $lineNumber;
    }

    /**
     * Adds giftwrap printed card cost to request as item
     *
     * @param Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo $object
     * @param bool $credit
     * @return int|bool
     */
    protected function _addGwPrintedCardAmount($object, $credit = false)
    {
        if (!$object->getGwPrintedCardBasePrice()) {
            return false;
        }

        $lineNumber = count($this->_lines);
        $storeId = $object->getStore()->getId();

        $amount = $object->getGwPrintedCardBasePrice();

        $line = new Line();
        if ($this->_getTaxDataHelper()->priceIncludesTax($storeId)) {
            $amount += $object->getGwCardBaseTaxAmount();
            $line->setTaxIncluded(true);
        }

        if ($credit) {
            $amount *= -1;
        }

        $line->setNo($lineNumber);
        $line->setItemCode($this->_getConfigHelper()->getGwPrintedCardSku($storeId));
        $line->setDescription('Gift Wrap Printed Card Amount');
        $line->setTaxCode($this->_getGiftTaxClassCode($storeId));
        $line->setQty(1);
        $line->setAmount($amount);
        $line->setDiscounted(false);

        $this->_lineToItemId[$lineNumber] = $this->_getConfigHelper()->getGwPrintedCardSku($storeId);
        $this->_lines[$lineNumber] = $line;
        $this->_request->setLines($this->_lines);
        return $lineNumber;
    }

    /**
     * Adds adjustments to request as items
     *
     * @param float $positive
     * @param float $negative
     * @param int   $storeId
     * @return array
     */
    protected function _addAdjustments($positive, $negative, $storeId)
    {
        if ($positive != 0) {
            $lineNumber = count($this->_lines);
            $identifier = $this->_getConfigHelper()->getPositiveAdjustmentSku($storeId);

            $line = new Line();
            $line->setNo($lineNumber);
            $line->setItemCode($identifier ? $identifier : 'adjustment');
            $line->setDescription('Adjustment refund');
            $line->setTaxCode($identifier);
            $line->setQty(1);
            $line->setAmount($positive * -1);
            $line->setDiscounted(false);
            $line->setTaxIncluded(true);
            $this->_lineToItemId[$lineNumber] = 'positive-adjustment';
            $this->_lines[$lineNumber] = $line;
            $this->_request->setLines($this->_lines);
        }

        if ($negative != 0) {
            $lineNumber = count($this->_lines);
            $identifier = $this->_getConfigHelper()->getNegativeAdjustmentSku($storeId);

            $line = new Line();
            $line->setNo($lineNumber);
            $line->setItemCode($identifier ? $identifier : 'adjustment');
            $line->setDescription('Adjustment fee');
            $line->setTaxCode($identifier);
            $line->setQty(1);
            $line->setAmount($negative);
            $line->setDiscounted(false);
            $line->setTaxIncluded(true);
            $this->_lineToItemId[$lineNumber] = 'negative-adjustment';
            $this->_lines[$lineNumber] = $line;
            $this->_request->setLines($this->_lines);
        }
    }

    /**
     * Makes a Line object from a product item object
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     * @param Mage_Sales_Model_Order_Address                                             $address
     * @param bool                                                                       $credit
     * @return bool
     * @throws \Varien_Exception
     * @throws \OnePica_AvaTax_Exception
     */
    protected function _newLine($item, $address, $credit = false)
    {
        if ($this->isProductCalculated($item->getOrderItem())) {
            return false;
        }

        if ($item->getQty() == 0) {
            return false;
        }

        $line = new Line();
        $storeId = $this->_retrieveStoreIdFromItem($item);
        $price = $item->getBaseRowTotal();

        if ($this->_getTaxDataHelper()->priceIncludesTax($storeId)) {
            $line->setTaxIncluded(true);
            $price = $item->getBaseRowTotalInclTax();
        }

        if ($this->_getTaxDataHelper()->applyTaxAfterDiscount($storeId)) {
            $price -= $item->getBaseDiscountAmount();
        }

        if ($credit) {
            $price *= -1;
        }

        $line->setNo(count($this->_lines));
        $line->setItemCode(
            $this->_getCalculationHelper()->getItemCode(
                $this->_getProductForItemCode($item),
                $storeId,
                $item
            )
        );
        $line->setDescription($item->getName());
        $line->setQty($item->getQty());
        $line->setAmount($price);

        $line->setDiscounted(
            (float)$item->getBaseDiscountAmount() && $this->_getTaxDataHelper()->applyTaxAfterDiscount($storeId)
        );

        $productData = $this->_getLineProductData($item, $storeId);

        $line->setTaxCode($productData->getTaxCode());
        $line->setRef1($productData->getRef1());
        $line->setRef2($productData->getRef2());

        $this->_addLandedCostParamsToLine($line, $productData->getProduct(), $address);

        $this->_newLineMakeAdditionalProcessingForLine(
            new \Varien_Object(array('productData' => $productData, 'item' => $item, 'line' => $line))
        );
        $this->_lineToItemId[count($this->_lines)] = $item->getOrderItemId();
        $this->_lines[] = $line;

        return true;
    }

    /**
     * Get line product data
     *
     * Return a Varien_Object with the following possible methods: getTaxCode, getRef1, getRef2
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     * @param int                                                                        $storeId
     * @return \Varien_Object
     * @throws \OnePica_AvaTax_Exception
     * @throws \Varien_Exception
     */
    protected function _getLineProductData($item, $storeId)
    {
        $lineProductData = new Varien_Object();
        $product = $this->_getProductByProductId($this->_retrieveProductIdFromOrderItem($item));

        if (null === $product) {
            return $lineProductData;
        }

        $this->_newLinePrepareProduct(new \Varien_Object(array('product' => $product, 'item' => $item)));

        $lineProductData->setProduct($product);
        $lineProductData->setTaxCode($this->_getTaxClassCodeByProduct($product));
        $lineProductData->setRef1($this->_getRefValueByProductAndNumber($product, 1, $storeId));
        $lineProductData->setRef2($this->_getRefValueByProductAndNumber($product, 2, $storeId));

        foreach (array(
                     OnePica_AvaTax_Helper_LandedCost::AVATAX_PRODUCT_LANDED_COST_ATTR_HSCODE,
                     OnePica_AvaTax_Helper_LandedCost::AVATAX_PRODUCT_LANDED_COST_AGREEMENT,
                     OnePica_AvaTax_Helper_LandedCost::AVATAX_PRODUCT_LANDED_COST_ATTR_PARAMETER
                 ) as $key) {
            $lineProductData->setData($key, $product->getData($key));
        }


        return $lineProductData;
    }

    /**
     * Retrieve product for item code
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     * @return null|Mage_Catalog_Model_Product
     * @throws OnePica_AvaTax_Exception
     */
    protected function _getProductForItemCode($item)
    {
        $product = $this->_getProductByProductId($item->getProductId());
        if (!$this->_getCalculationHelper()->isConfigurable($item)) {
            return $product;
        }

        $children = $item->getOrderItem()->getChildrenItems();

        if (isset($children[0]) && $children[0]->getProductId()) {
            $product = $this->_getProductByProductId($children[0]->getProductId());
        }

        return $product;
    }

    /**
     * Retrieve store id from item
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     * @return int
     */
    protected function _retrieveStoreIdFromItem($item)
    {
        $storeId = null;
        if ($item instanceof Mage_Sales_Model_Order_Invoice_Item) {
            $storeId = $item->getInvoice()->getStoreId();
        } else {
            $storeId = $item->getCreditmemo()->getStoreId();
        }

        return $storeId;
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
