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


class OnePica_AvaTax_Model_Avatax_Invoice extends OnePica_AvaTax_Model_Avatax_Abstract
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
	 * @see OnePica_AvaTax_Model_Observer::salesOrderPlaceAfter()
	 * @param Mage_Sales_Model_Order_Invoice $invoice
	 * @return array
	 */
	public function invoice($invoice, $queue) {
		$order = $invoice->getOrder();
		$invoiceDate = $order->getInvoiceCollection()->getFirstItem()->getCreatedAt();
		$orderDate = $order->getCreatedAt();
		$statusDate = $queue->getUpdatedAt();

		$shippingAddress = ($order->getShippingAddress()) ? $order->getShippingAddress() : $order->getBillingAddress();
		if(!$shippingAddress) {
			throw new Exception($this->__('There is no address attached to this order'));
		}
		
		$this->_request = new GetTaxRequest();
		$this->_request->setDocCode($invoice->getIncrementId());
        $this->_request->setDocType(DocumentType::$SalesInvoice);

        $this->_addGeneralInfo($order);
        $this->_addShipping($invoice);
		//Added code for calculating tax for giftwrap items
        $this->_addGwOrderAmount($invoice);
        $this->_addGwItemsAmount($invoice);
        $this->_addGwPrintedCardAmount($invoice);
		
		$this->_setOriginAddress($order->getStoreId());
		$this->_setDestinationAddress($shippingAddress);
        //$this->_request->setPaymentDate(date('Y-m-d'));
		$this->_request->setDocDate(substr($invoiceDate, 0, 10));
		$this->_request->setPaymentDate(substr($invoiceDate, 0, 10));
		$this->_request->setTaxDate(substr($orderDate, 0, 10));
		$this->_request->setStatusDate(substr($statusDate, 0, 10));
    	
		$configAction = Mage::getStoreConfig('tax/avatax/action', $order->getStoreId());
		$commitAction = OnePica_AvaTax_Model_Config::ACTION_CALC_SUBMIT_COMMIT;
		$this->_request->setCommit(($configAction==$commitAction) ? true : false);
        
		foreach($invoice->getItemsCollection() as $item) {
			$this->_newLine($item);
		}
    	$this->_request->setLines($this->_lines);
		
    	//send to AvaTax
        $result = $this->_send($order->getStoreId());
			
        //if successful
		if($result->getResultCode() == SeverityLevel::$Success) {
			$message = Mage::helper('avatax')->__('Invoice #%s was saved to AvaTax', $result->getDocCode());
			$this->_addStatusHistoryComment($order, $message);
			
			if($result->getTotalTax() != $invoice->getBaseTaxAmount()) {
				throw new OnePica_AvaTax_Model_Avatax_Exception_Unbalanced('Collected: ' . $invoice->getBaseTaxAmount() . ', Actual: ' . $result->getTotalTax());
			}
		
		//if not successful
		} else {
			$messages = array();
			foreach($result->getMessages() as $message) $messages[] = $message->getSummary();
			throw new OnePica_AvaTax_Model_Avatax_Exception_Commitfailure(implode(' // ', $messages));
		}
		
		return true;
	}

	/**
	 * Save order in AvaTax system
	 *
	 * @see OnePica_AvaTax_Model_Observer::salesOrderPlaceAfter()
	 * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
	 */
	public function creditmemo($creditmemo, $queue) {
		$order = $creditmemo->getOrder();
		$orderDate = $order->getCreatedAt(); 
		$statusDate = $queue->getUpdatedAt();
		$creditmemoDate = $order->getCreditmemosCollection()->getFirstItem()->getCreatedAt();
		
		$shippingAddress = ($order->getShippingAddress()) ? $order->getShippingAddress() : $order->getBillingAddress();
		if(!$shippingAddress) {
			throw new Exception($this->__('There is no address attached to this order'));
		}
		
		$this->_request = new GetTaxRequest();
		$this->_request->setDocCode($creditmemo->getIncrementId());
        $this->_request->setDocType(DocumentType::$ReturnInvoice);
        
        $this->_addGeneralInfo($order);
        $this->_addShipping($creditmemo, true);
		//Added code for calculating tax for giftwrap items
        $this->_addGwOrderAmount($creditmemo);
        $this->_addGwItemsAmount($creditmemo);
        $this->_addGwPrintedCardAmount($creditmemo);
		
        $this->_addAdjustments($creditmemo->getAdjustmentPositive(), $creditmemo->getAdjustmentNegative());
		$this->_setOriginAddress($order->getStoreId());
		$this->_setDestinationAddress($shippingAddress);
		
		// Set the tax date for calculation.
		//$invoiceDate = $order->getInvoiceCollection()->getFirstItem()->getCreatedAt();
		$override = new TaxOverride();
		//$override->setTaxDate(substr($invoiceDate, 0, 10));
		$override->setTaxDate(substr($orderDate, 0, 10));
		$override->setTaxOverrideType(TaxOverrideType::$TaxDate);
		$override->setReason('Credit memo - refund');
		$this->_request->setTaxOverride($override);

		$this->_request->setDocDate(substr($creditmemoDate, 0, 10));
		$this->_request->setPaymentDate(substr($creditmemoDate, 0, 10));
		$this->_request->setTaxDate(substr($orderDate, 0, 10));
		$this->_request->setStatusDate(substr($statusDate, 0, 10));
		
		$configAction = Mage::getStoreConfig('tax/avatax/action', $order->getStoreId());
		$commitAction = OnePica_AvaTax_Model_Config::ACTION_CALC_SUBMIT_COMMIT;
		$this->_request->setCommit(($configAction==$commitAction) ? true : false);
		
		foreach($creditmemo->getAllItems() as $item) {
			$this->_newLine($item, true);
		}
    	$this->_request->setLines($this->_lines);
        
    	//send to AvaTax
        $result = $this->_send($order->getStoreId());
		
        //if successful
		if($result->getResultCode() == SeverityLevel::$Success) {
			$message = Mage::helper('avatax')->__('Credit memo #%s was saved to AvaTax', $result->getDocCode());
			$this->_addStatusHistoryComment($order, $message);
			
			if($result->getTotalTax() != ($creditmemo->getTaxAmount()*-1)) {
				throw new OnePica_AvaTax_Model_Avatax_Exception_Unbalanced('Collected: ' . $creditmemo->getTaxAmount() . ', Actual: ' . $result->getTotalTax());
			}
			
		//if not successful
		} else {
			$messages = array();
			foreach($result->getMessages() as $message) $messages[] = $message->getSummary();
			throw new OnePica_AvaTax_Model_Avatax_Exception_Commitfailure(implode(' // ', $messages));
		}
		
		return $result;
	}
	
	/**
	 * Adds shipping cost to request as item
	 *
	 * @param int $shippingAmount
	 * @return int
	 */
	protected function _addShipping($object, $credit=false) {
		if($object->getBaseShippingAmount() == 0) {
			return false;
		}
		
		$lineNumber = count($this->_lines);
    	$storeId = Mage::app()->getStore()->getId();
    	$taxClass = Mage::helper('tax')->getShippingTaxClass($storeId);
    	
    	$amount = $object->getBaseShippingAmount();
    	if($credit) $amount *= -1;
		
		$line = new Line();
		$line->setNo($lineNumber);
        $line->setItemCode(Mage::helper('avatax')->getShippingSku($storeId));
        $line->setDescription('Shipping costs');
        $line->setTaxCode($taxClass);
        $line->setQty(1);
        $line->setAmount($amount);
        $line->setDiscounted(false);
        
        $this->_lineToItemId[$lineNumber] = 'shipping';
        $this->_lines[$lineNumber] = $line;
    	$this->_request->setLines($this->_lines);
    	return $lineNumber;
	}
	
	/**
	 * Adds giftwraporder cost to request as item
	 *
	 * @param int $shippingAmount
	 * @return int
	 */
	protected function _addGwOrderAmount($object, $credit=false) {
		if($object->getGwPrice() == 0) {
			return false;
		}
		
		$lineNumber = count($this->_lines);
    	$storeId = Mage::app()->getStore()->getId();
    	$taxClass = Mage::helper('tax')->getShippingTaxClass($storeId);
    	
    	$amount = $object->getGwBasePrice();
    	if($credit) $amount *= -1;
		
		$line = new Line();
		$line->setNo($lineNumber);
        $line->setItemCode(Mage::helper('avatax')->getGwOrderSku($storeId));
        $line->setDescription('Gift Wrap Order Amount');
        $line->setTaxCode('');
        $line->setQty(1);
        $line->setAmount($amount);
        $line->setDiscounted(false);
        
        $this->_lineToItemId[$lineNumber] = Mage::helper('avatax')->getGwOrderSku($storeId);
        $this->_lines[$lineNumber] = $line;
    	$this->_request->setLines($this->_lines);
    	return $lineNumber;
	}
	
	/**
	 * Adds giftwrapitems cost to request as item
	 *
	 * @param int $shippingAmount
	 * @return int
	 */
	protected function _addGwItemsAmount($object, $credit=false) {
		if($object->getGwItemsPrice() == 0) {
			return false;
		}
		
		$lineNumber = count($this->_lines);
    	$storeId = Mage::app()->getStore()->getId();
    	
    	$amount = $object->getGwItemsBasePrice();
    	if($credit) $amount *= -1;
		
		$line = new Line();
		$line->setNo($lineNumber);
        $line->setItemCode(Mage::helper('avatax')->getGwItemsSku($storeId));
        $line->setDescription('Gift Wrap Items Amount');
        $line->setTaxCode('');
        $line->setQty(1);
        $line->setAmount($amount);
        $line->setDiscounted(false);
        
        $this->_lineToItemId[$lineNumber] = Mage::helper('avatax')->getGwItemsSku($storeId);
        $this->_lines[$lineNumber] = $line;
    	$this->_request->setLines($this->_lines);
    	return $lineNumber;
	}
	
	/**
	 * Adds giftwrap printed card cost to request as item
	 *
	 * @param int $shippingAmount
	 * @return int
	 */
	protected function _addGwPrintedCardAmount($object, $credit=false) {
		if($object->getGwPrintedCardSku() == 0) {
			return false;
		}
		
		$lineNumber = count($this->_lines);
    	$storeId = Mage::app()->getStore()->getId();
   	
    	$amount = $object->getGwPrintedCardBasePrice();
    	if($credit) $amount *= -1;
		
		$line = new Line();
		$line->setNo($lineNumber);
        $line->setItemCode(Mage::helper('avatax')->getGwPrintedCardSku($storeId));
        $line->setDescription('Gift Wrap Printed Card Amount');
        $line->setTaxCode('');
        $line->setQty(1);
        $line->setAmount($amount);
        $line->setDiscounted(false);
        
        $this->_lineToItemId[$lineNumber] = Mage::helper('avatax')->getGwPrintedCardSku($storeId);
        $this->_lines[$lineNumber] = $line;
    	$this->_request->setLines($this->_lines);
    	return $lineNumber;
	}
        
        /**
	 * Adds shipping cost to request as item
	 * 
	 * Not used...?
	 *
	 * @param int $shippingAmount
	 * @return int
	 */
	/*
	protected function _addGiftAmount($object, $credit=false) {
            if($object->getGiftCardsAmount() == 0) {
                    return false;
            }

            $lineNumber = count($this->_lines);
            $storeId = Mage::app()->getStore()->getId();

            $giftAmount = $object->getShippingAmount();
            if($credit) $amount *= -1;
            $line = new Line();
            $line->setNo($lineNumber);
            $giftSku = Mage::helper('avatax')->getGiftSku($storeId);
            $line->setItemCode($giftSku ? $giftSku : 'GiftAmount');
            $line->setDescription('Gift Amount');
            $line->setTaxCode('');
            $line->setQty(1);
            $line->setAmount($giftAmount);
            $line->setDiscounted(false);

            $this->_lineToItemId[$lineNumber] = Mage::helper('avatax')->getGiftSku($storeId);
            $this->_lines[$lineNumber] = $line;
            $this->_request->setLines($this->_lines);
            return $lineNumber;
	}
	*/
	
	/**
	 * Adds adjustments to request as items
	 *
	 * @param float $positive
	 * @param float $negative
	 * @return array
	 */
	protected function _addAdjustments($positive, $negative) {
	    $storeId = Mage::app()->getStore()->getId();
	    
		if($positive != 0) {
        	$lineNumber = count($this->_lines);
	    	$identifier = Mage::helper('avatax')->getPositiveAdjustmentSku($storeId);
			
			$line = new Line();
			$line->setNo($lineNumber);
	        $line->setItemCode($identifier ? $identifier : 'adjustment');
	        $line->setDescription('Adjustment refund');
	        $line->setTaxCode($identifier);
	        $line->setQty(1);
	        $line->setAmount($positive*-1);
	        $line->setDiscounted(false);
	        $line->setTaxIncluded(true);
	        $this->_lineToItemId[$lineNumber] = 'positive-adjustment';
	        $this->_lines[$lineNumber] = $line;
	    	$this->_request->setLines($this->_lines);
		}
	    
		if($negative != 0) {
        	$lineNumber = count($this->_lines);
	    	$identifier = Mage::helper('avatax')->getNegativeAdjustmentSku($storeId);
			
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
	 * @param Varien_Object $item
	 * @return null
	 */
	protected function _newLine($item, $credit=false) {
		if($this->isProductCalculated($item->getOrderItem())) {
			return false;
		}
		if($item->getQty() == 0) {
			return false;
		}
		
		$price = $item->getBaseRowTotal() - $item->getBaseDiscountAmount();
    	if($credit) $price *= -1;
    	
		$line = new Line();
		$line->setNo(count($this->_lines));
		$line->setItemCode(substr($item->getSku(), 0, 50));
		$line->setDescription($item->getName());
		$line->setQty($item->getQty());
		$line->setAmount($price);
		$line->setDiscounted($item->getBaseDiscountAmount() ? true : false);
		
		$product = Mage::getModel('catalog/product')->load($item->getProductId());
		$taxClass = Mage::getModel('tax/class')->load($product->getTaxClassId())->getOpAvataxCode();
		$line->setTaxCode($taxClass);

		$ref1Code = Mage::helper('avatax')->getRef1AttributeCode($product->getStoreId());
		if($ref1Code && $product->getResource()->getAttribute($ref1Code)) {
			$ref1 = $product->getResource()->getAttribute($ref1Code)->getFrontend()->getValue($product);
			try { $line->setRef1((string)$ref1); } catch(Exception $e) { }
		}
		$ref2Code = Mage::helper('avatax')->getRef2AttributeCode($product->getStoreId());
		if($ref2Code && $product->getResource()->getAttribute($ref2Code)) {
			$ref2 = $product->getResource()->getAttribute($ref2Code)->getFrontend()->getValue($product);
			try { $line->setRef2((string)$ref2); } catch(Exception $e) { }
		}
	
		$this->_lineToItemId[count($this->_lines)] = $item->getOrderItemId();
		$this->_lines[] = $line;
	}
	
}
