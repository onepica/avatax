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
 * Admin export controller
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Adminhtml_AvaTax_ExportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check if is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('avatax');
    }

    /**
     * Log action
     *
     * @return $this
     */
    public function logAction()
    {
        $fileName = 'avatax-log-' . $this->_getDateModel()->gmtDate('U') . '.sql';
        $content = Mage::getModel('avatax/export')
            ->setAdapter(Mage::getModel('avatax/export_adapter_sql'))
            ->setEntity(Mage::getModel('avatax/export_entity_log'))
            ->getContent();
        $this->_sendResponse($fileName, $content);
        return $this;
    }

    /**
     * Queue action
     *
     * @return $this
     */
    public function queueAction()
    {
        $fileName = 'avatax-queue-' . $this->_getDateModel()->gmtDate('U') . '.sql';
        $content = Mage::getModel('avatax/export')
            ->setAdapter(Mage::getModel('avatax/export_adapter_sql'))
            ->setEntity(Mage::getModel('avatax/export_entity_queue'))
            ->getContent();
        $this->_sendResponse($fileName, $content);
        return $this;
    }

    /**
     * Order Info action
     *
     * @return $this
     */
    public function orderinfoAction()
    {
        $fileNameArray = array(
            'avatax_sales',
            'order_' . $this->getRequest()->getParam('order_id'),
            $this->_getDateModel()->gmtDate('U')
        );

        $fileName = implode('-', $fileNameArray) . '.sql';
        $storeId = $this->getRequest()->getParam('store_id');
        $quoteId = $this->getRequest()->getParam('quote_id');

        /** @var \OnePica_AvaTax_Model_Export_Entity_Order_Log $logEntity */
        $logEntity = Mage::getModel('avatax/export_entity_order_log');
        $logContent = $this->_getOrderSqlContent($logEntity->setQuoteId($quoteId));

        /** @var \OnePica_AvaTax_Model_Export_Entity_Order_Queue $queueEntity */
        $queueEntity = Mage::getModel('avatax/export_entity_order_queue');
        $queueContent = $this->_getOrderSqlContent($queueEntity->setQuoteId($quoteId));

        /** @var \OnePica_AvaTax_Model_Export_Entity_Order_SalesQuote $quoteEntity */
        $quoteEntity = Mage::getModel('avatax/export_entity_order_salesquote');
        $quoteContent = $this->_getOrderSqlContent($quoteEntity->setQuoteId($quoteId));

        /** @var \OnePica_AvaTax_Model_Export_Entity_Order_SalesQuoteAddress $quoteAddressEntity */
        $quoteAddressEntity = Mage::getModel('avatax/export_entity_order_salesquoteaddress');
        $quoteAddressContent = $this->_getOrderSqlContent($quoteAddressEntity->setQuoteId($quoteId));

        /** @var \OnePica_AvaTax_Model_Export_Entity_Order_SalesQuoteAddress $quoteItemEntity */
        $quoteItemEntity = Mage::getModel('avatax/export_entity_order_salesquoteitem');
        $quoteItemContent = $this->_getOrderSqlContent($quoteItemEntity->setQuoteId($quoteId)->setStoreId($storeId));

        /** @var \OnePica_AvaTax_Model_Export_Entity_Order_SalesOrder $orderEntity */
        $orderEntity = Mage::getModel('avatax/export_entity_order_salesorder');
        $orderContent = $this->_getOrderSqlContent($orderEntity->setQuoteId($quoteId));

        /** @var \OnePica_AvaTax_Model_Export_Entity_Order_SalesOrderAddress $orderAddressEntity */
        $orderAddressEntity = Mage::getModel('avatax/export_entity_order_salesorderaddress');
        $orderAddressContent = $this->_getOrderSqlContent($orderAddressEntity->setQuoteId($quoteId));

        /** @var \OnePica_AvaTax_Model_Export_Entity_Order_SalesOrderItem $orderItemEntity */
        $orderItemEntity = Mage::getModel('avatax/export_entity_order_salesorderitem');
        $orderItemContent = $this->_getOrderSqlContent($orderItemEntity->setQuoteId($quoteId));

        /** @var \OnePica_AvaTax_Model_Export_Entity_Order_SalesInvoice $invoiceEntity */
        $invoiceEntity = Mage::getModel('avatax/export_entity_order_salesinvoice');
        $invoiceContent = $this->_getOrderSqlContent($invoiceEntity->setQuoteId($quoteId));

        /** @var \OnePica_AvaTax_Model_Export_Entity_Order_SalesInvoiceItem $invoiceItemEntity */
        $invoiceItemEntity = Mage::getModel('avatax/export_entity_order_salesinvoiceitem');
        $invoiceItemContent = $this->_getOrderSqlContent($invoiceItemEntity->setQuoteId($quoteId));

        /** @var \OnePica_AvaTax_Model_Export_Entity_Order_Creditmemo $creditmemoEntity */
        $creditmemoEntity = Mage::getModel('avatax/export_entity_order_creditmemo');
        $creditmemoContent = $this->_getOrderSqlContent($creditmemoEntity->setQuoteId($quoteId));

        /** @var \OnePica_AvaTax_Model_Export_Entity_Order_CreditmemoItem $creditmemoItemEntity */
        $creditmemoItemEntity = Mage::getModel('avatax/export_entity_order_creditmemoitem');
        $creditmemoItemContent = $this->_getOrderSqlContent($creditmemoItemEntity->setQuoteId($quoteId));

        $content = $logContent .
            $queueContent .
            $quoteContent .
            $quoteAddressContent .
            $quoteItemContent .
            $orderContent .
            $orderAddressContent .
            $orderItemContent .
            $invoiceContent .
            $invoiceItemContent.
            $creditmemoContent .
            $creditmemoItemContent;

        $this->_sendResponse($fileName, $content);

        return $this;
    }

    /**
     * Send response
     *
     * @param string $fileName
     * @param string $content
     * @param string $contentType
     * @return $this
     */
    protected function _sendResponse($fileName, $content, $contentType = 'application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', $this->_getDateModel()->date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        return $this;
    }

    /**
     * Get core date model
     *
     * @return Mage_Core_Model_Date
     */
    protected function _getDateModel()
    {
        return Mage::getSingleton('core/date');
    }

    /**
     * @param OnePica_AvaTax_Model_Export_Entity_Order_SalesAbstract $entity
     * @return string
     */
    protected function _getOrderSqlContent($entity)
    {
        $cols = $entity->getExportColumns();
        $adapter = Mage::getModel('avatax/export_adapter_order_sql')->setColumnsToExport($cols);

        return Mage::getModel('avatax/export')
                   ->setAdapter($adapter)
                   ->setEntity($entity)
                   ->getContent();
    }
}
