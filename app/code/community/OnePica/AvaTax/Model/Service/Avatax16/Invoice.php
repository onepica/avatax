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
 * The AvaTax16 Invoice model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Avatax16_Invoice extends OnePica_AvaTax_Model_Service_Avatax16_Tax
{
    /**
     * An array of line numbers to product ids
     *
     * @var array
     */
    protected $_lineToItemId = array();

    /**
     * Save order's invoice in AvaTax system
     *
     * @see OnePica_AvaTax_Model_Observer::salesOrderPlaceAfter()
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

        $shippingAddress = ($order->getShippingAddress()) ? $order->getShippingAddress() : $order->getBillingAddress();

        // Set up document for request
        $this->_request = $this->_getNewDocumentRequestObject();

        // set up header
        $header = $this->_getRequestHeaderWithMainValues($storeId);
        $header->setDocumentCode($this->_getInvoiceDocumentCode($invoice));
        $header->setTransactionDate($invoiceDate);
        $header->setTaxCalculationDate($orderDate);
        $header->setDefaultLocations($this->_getHeaderDefaultLocations($shippingAddress));

        $this->_request->setHeader($header);

        $this->_addShipping($invoice);
        $items = $invoice->getItemsCollection();
        $this->_initProductCollection($items);
        $this->_initTaxClassCollection($invoice);
        //Added code for calculating tax for giftwrap items
        $this->_addGwOrderAmount($invoice);
        $this->_addGwItemsAmount($invoice);
        $this->_addGwPrintedCardAmount($invoice);

        foreach ($items as $item) {
            /** @var Mage_Sales_Model_Order_Invoice_Item $item */
            $this->_newLine($item);
        }
        $this->_setLinesToRequest();

        //send to AvaTax
        $result = $this->_send($order->getStoreId());

        /** @var OnePica_AvaTax_Model_Service_Result_Invoice $invoiceResult */
        $invoiceResult = Mage::getModel('avatax/service_result_invoice');
        $invoiceResult->setHasError($result->getHasError());

        //if successful
        if (!$result->getHasError()) {
            $totalTax = $result->getCalculatedTaxSummary()->getTotalTax();
            $invoiceResult->setTotalTax($totalTax);
            $documentCode = $result->getHeader()->getDocumentCode();
            $invoiceResult->setDocumentCode($documentCode);

        //if not successful
        } else {
            $invoiceResult->setErrors($result->getErrors());
        }

        return $invoiceResult;
    }

    /**
     * Save order's creditmemo in AvaTax system
     *
     * @see OnePica_AvaTax_Model_Observer::salesOrderPlaceAfter()
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @param OnePica_AvaTax_Model_Records_Queue $queue
     * @return mixed
     * @throws OnePica_AvaTax_Exception
     * @throws OnePica_AvaTax_Model_Service_Exception_Commitfailure
     * @throws OnePica_AvaTax_Model_Service_Exception_Unbalanced
     */
    public function creditmemo($creditmemo, $queue)
    {
        $this->_lines = array();
        $order = $creditmemo->getOrder();
        $storeId = $order->getStoreId();
        $creditmemoDate = $this->_convertGmtDate($creditmemo->getCreatedAt(), $storeId);
        $orderDate = $this->_convertGmtDate($order->getCreatedAt(), $storeId);

        $shippingAddress = ($order->getShippingAddress()) ? $order->getShippingAddress() : $order->getBillingAddress();

        // Set up document for request
        $this->_request = $this->_getNewDocumentRequestObject();

        // set up header
        $header = $this->_getRequestHeaderWithMainValues($storeId);
        $header->setDocumentCode($this->_getCreditmemoDocumentCode($creditmemo));
        $header->setTransactionDate($creditmemoDate);
        $header->setTaxCalculationDate($orderDate);
        $header->setDefaultLocations($this->_getHeaderDefaultLocations($shippingAddress));

        $this->_request->setHeader($header);

        $this->_addShipping($creditmemo, true);
        $items = $creditmemo->getAllItems();
        $this->_initProductCollection($items);
        $this->_initTaxClassCollection($creditmemo);
        //Added code for calculating tax for giftwrap items
        $this->_addGwOrderAmount($creditmemo, true);
        $this->_addGwItemsAmount($creditmemo, true);
        $this->_addGwPrintedCardAmount($creditmemo, true);

        $this->_addAdjustments(
            $creditmemo->getAdjustmentPositive(),
            $creditmemo->getAdjustmentNegative(),
            $order->getStoreId()
        );

        foreach ($items as $item) {
            /** @var Mage_Sales_Model_Order_Creditmemo_Item $item */
            $this->_newLine($item, true);
        }
        $this->_setLinesToRequest();

        //send to AvaTax
        $result = $this->_send($order->getStoreId());

        /** @var OnePica_AvaTax_Model_Service_Result_Creditmemo $creditmemoResult */
        $creditmemoResult = Mage::getModel('avatax/service_result_creditmemo');
        $creditmemoResult->setHasError($result->getHasError());

        //if successful
        if (!$result->getHasError()) {
            $totalTax = $result->getCalculatedTaxSummary()->getTotalTax();
            $creditmemoResult->setTotalTax($totalTax);
            $documentCode = $result->getHeader()->getDocumentCode();
            $creditmemoResult->setDocumentCode($documentCode);

            //if not successful
        } else {
            $creditmemoResult->setErrors($result->getErrors());
        }

        return $creditmemoResult;
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
            $lineNumber = $this->_getNewLineCode();
            $identifier = $this->_getConfigHelper()->getPositiveAdjustmentSku($storeId);
            $identifier = $identifier ? $identifier : self::DEFAULT_POSITIVE_ADJUSTMENT_CODE;

            $line = $this->_getNewDocumentRequestLineObject();
            $line->setLineCode($lineNumber);
            $line->setItemCode($identifier);
            $line->setItemDescription(self::DEFAULT_POSITIVE_ADJUSTMENT_DESCRIPTION);
            $line->setTaxCode($identifier);
            $line->setNumberOfItems(1);
            $line->setlineAmount($positive * -1);
            $line->setDiscounted('false');
            $line->setTaxIncluded('true');

            $this->_lineToItemId[$lineNumber] = $identifier;
            $this->_lines[$lineNumber] = $line;
            $this->_setLinesToRequest();
        }

        if ($negative != 0) {
            $lineNumber = $this->_getNewLineCode();
            $identifier = $this->_getConfigHelper()->getNegativeAdjustmentSku($storeId);
            $identifier = $identifier ? $identifier : self::DEFAULT_NEGATIVE_ADJUSTMENT_CODE;

            $line = $this->_getNewDocumentRequestLineObject();
            $line->setLineCode($lineNumber);
            $line->setItemCode($identifier);
            $line->setItemDescription(self::DEFAULT_NEGATIVE_ADJUSTMENT_DESCRIPTION);
            $line->setTaxCode($identifier);
            $line->setNumberOfItems(1);
            $line->setlineAmount($negative);
            $line->setDiscounted('false');
            $line->setTaxIncluded('true');

            $this->_lineToItemId[$lineNumber] = $identifier;
            $this->_lines[$lineNumber] = $line;
            $this->_setLinesToRequest();
        }
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

        $lineNumber = $this->_getNewLineCode();
        $storeId = $object->getStore()->getId();
        $taxClass = Mage::helper('tax')->getShippingTaxClass($storeId);

        $amount = $object->getBaseShippingAmount();
        //@startSkipCommitHooks
        $amount = $credit ? (-1 * $amount) : $amount;
        //@finishSkipCommitHooks

        $line = $this->_getNewDocumentRequestLineObject();
        $line->setLineCode($lineNumber);
        $shippingSku = $this->_getConfigHelper()->getShippingSku($storeId);
        $line->setItemCode($shippingSku ? $shippingSku : self::DEFAULT_SHIPPING_ITEMS_SKU);
        $line->setItemDescription(self::DEFAULT_SHIPPING_ITEMS_DESCRIPTION);
        $line->setTaxCode($taxClass);
        $line->setNumberOfItems(1);
        $line->setlineAmount($amount);
        $line->setDiscounted('false');

        $this->_lineToItemId[$lineNumber] = $shippingSku;
        $this->_lines[$lineNumber] = $line;
        $this->_setLinesToRequest();
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

        $lineNumber = $this->_getNewLineCode();
        $storeId = $object->getStore()->getId();
        $amount = $object->getGwBasePrice();
        //@startSkipCommitHooks
        $amount = $credit ? (-1 * $amount) : $amount;
        //@finishSkipCommitHooks

        $line = $this->_getNewDocumentRequestLineObject();
        $line->setLineCode($lineNumber);
        $gwOrderSku = $this->_getConfigHelper()->getGwOrderSku($storeId);
        $line->setItemCode($gwOrderSku ? $gwOrderSku : self::DEFAULT_GW_ORDER_SKU);
        $line->setItemDescription(self::DEFAULT_GW_ORDER_DESCRIPTION);
        $line->setTaxCode($this->_getGiftTaxClassCode($storeId));
        $line->setNumberOfItems(1);
        $line->setlineAmount($amount);
        $line->setDiscounted('false');

        $this->_lineToItemId[$lineNumber] = $gwOrderSku;
        $this->_lines[$lineNumber] = $line;
        $this->_setLinesToRequest();
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

        $lineNumber = $this->_getNewLineCode();
        $storeId = $object->getStore()->getId();

        $amount = $object->getGwItemsBasePrice();
        //@startSkipCommitHooks
        $amount = $credit ? (-1 * $amount) : $amount;
        //@finishSkipCommitHooks

        $line = $this->_getNewDocumentRequestLineObject();
        $line->setLineCode($lineNumber);
        $gwItemsSku = $this->_getConfigHelper()->getGwItemsSku($storeId);
        $line->setItemCode($gwItemsSku ? $gwItemsSku : self::DEFAULT_GW_ITEMS_SKU);
        $line->setItemDescription(self::DEFAULT_GW_ITEMS_DESCRIPTION);
        $line->setTaxCode($this->_getGiftTaxClassCode($storeId));
        $line->setNumberOfItems(1);
        $line->setlineAmount($amount);
        $line->setDiscounted('false');

        $this->_lineToItemId[$lineNumber] = $gwItemsSku;
        $this->_lines[$lineNumber] = $line;
        $this->_setLinesToRequest();
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

        $lineNumber = $this->_getNewLineCode();
        $storeId = $object->getStore()->getId();

        $amount = $object->getGwPrintedCardBasePrice();
        //@startSkipCommitHooks
        $amount = $credit ? (-1 * $amount) : $amount;
        //@finishSkipCommitHooks

        $line = $this->_getNewDocumentRequestLineObject();
        $line->setLineCode($lineNumber);
        $gwPrintedCardSku = $this->_getConfigHelper()->getGwPrintedCardSku($storeId);
        $line->setItemCode($gwPrintedCardSku ? $gwPrintedCardSku : self::DEFAULT_GW_PRINTED_CARD_SKU);
        $line->setItemDescription(self::DEFAULT_GW_PRINTED_CARD_DESCRIPTION);
        $line->setTaxCode($this->_getGiftTaxClassCode($storeId));
        $line->setNumberOfItems(1);
        $line->setlineAmount($amount);
        $line->setDiscounted('false');

        $this->_lineToItemId[$lineNumber] = $gwPrintedCardSku;
        $this->_lines[$lineNumber] = $line;
        $this->_setLinesToRequest();
        return $lineNumber;
    }

    /**
     * Makes a Line object from a product item object
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     * @param bool $credit
     * @return null|bool
     */
    protected function _newLine($item, $credit = false)
    {
        if ($this->isProductCalculated($item->getOrderItem())) {
            return false;
        }
        if ($item->getQty() == 0) {
            return false;
        }

        $lineNumber = $this->_getNewLineCode();
        $storeId = $this->_retrieveStoreIdFromItem($item);
        $product = $this->_getProductByProductId($item->getProductId());
        $taxClass = $this->_getTaxClassCodeByProduct($product);
        $price = $item->getBaseRowTotal() - $item->getBaseDiscountAmount();
        //@startSkipCommitHooks
        $price = $credit ? (-1 * $price) : $price;
        //@finishSkipCommitHooks

        $line = $this->_getNewDocumentRequestLineObject();
        $line->setLineCode($lineNumber);
        $line->setItemCode($this->_getCalculationHelper()
             ->getItemCode($this->_getProductForItemCode($item), $storeId, $item));
        $line->setItemDescription($item->getName());
        $line->setNumberOfItems($item->getQty());
        $line->setlineAmount($price);
        $line->setDiscounted($item->getBaseDiscountAmount() ? 'true' : 'false');

        if ($taxClass) {
            $line->setTaxCode($taxClass);
        }

        $metadata = null;
        $ref1Value = $this->_getRefValueByProductAndNumber($product, 1, $storeId);
        if ($ref1Value) {
            $metadata['ref1'] = $ref1Value;
        }
        $ref2Value = $this->_getRefValueByProductAndNumber($product, 2, $storeId);
        if ($ref2Value) {
            $metadata['ref2'] = $ref2Value;
        }
        if ($metadata) {
            $line->setMetadata($metadata);
        }

        $this->_lineToItemId[$lineNumber] = $item->getOrderItemId();
        $this->_lines[$lineNumber] = $line;
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
     * Retrieve converted date taking into account the current time zone and store.
     *
     * @param string $gmt
     * @param int    $storeId
     * @return string
     */
    protected function _convertGmtDate($gmt, $storeId)
    {
        $date = date('m-d-Y H:i:s', strtotime($gmt));
        return Mage::app()->getLocale()->storeDate($storeId, $date)->toString(self::SERVICE_DATE_FORMAT);
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
            $result = $connection->createTransaction($this->_request);
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
            OnePica_AvaTax_Model_Source_Avatax16_Logtype::TRANSACTION,
            $this->_request,
            $result,
            $storeId,
            $config
        );

        return $result;
    }

    /**
     * Get document code for invoice
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return string
     */
    protected function _getInvoiceDocumentCode($invoice)
    {
        return self::DOCUMENT_CODE_INVOICE_PREFIX . $invoice->getIncrementId();
    }

    /**
     * Get document code for creditmemo
     *
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return string
     */
    protected function _getCreditmemoDocumentCode($creditmemo)
    {
        return self::DOCUMENT_CODE_CREDITMEMO_PREFIX . $creditmemo->getIncrementId();
    }
}
