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
 * Log collection model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Records_Mysql4_Log_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_relatedInformationAdded;

    /**
     * Construct
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('avatax_records/log');
    }

    /**
     * Redeclare after load method for specifying collection items original data
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _afterLoad()
    {
        if ($this->_relatedInformationAdded) {
            $this->interpretRelatedInformation();
        }

        return parent::_afterLoad();
    }

    /**
     * Add related info to select
     *
     * @return $this
     */
    public function addRelatedInfoToSelect()
    {

        $this->getSelect()->joinLeft(
            array('order_address' => $this->getTable('sales/order_address')),
            'main_table.quote_address_id = order_address.avatax_quote_address_id',
            array(
                'order_address_entity_id' => 'entity_id',
                'order_address_parent_id' => 'parent_id',
            )
        );
        $this->getSelect()->joinLeft(
            array('order' => $this->getTable('sales/order')),
            'order_address.parent_id = order.entity_id',
            array(
                'order_entity_id'    => 'entity_id',
                'order_increment_id' => 'increment_id',
            )
        );
        $this->getSelect()->joinLeft(
            array('invoice' => $this->getTable('sales/invoice')),
            'order.entity_id = invoice.order_id',
            array(
                'invoice_entity_id'    => 'entity_id',
                'invoice_increment_id' => 'increment_id'
            )
        );
        $this->getSelect()->joinLeft(
            array('creditmemo' => $this->getTable('sales/creditmemo')),
            'order.entity_id = creditmemo.order_id',
            array(
                'creditmemo_entity_id'    => 'entity_id',
                'creditmemo_increment_id' => 'increment_id'
            )
        );

        $this->_relatedInformationAdded = true;

        return $this;
    }

    /**
     * Related Data Interpretation
     * SalesOrder id and incremental id should be present for log only if GetTaxRequest:DocType is SalesOrder
     * SalesInvoice incremental id should be present for log only if GetTaxRequest:DocType is SalesInvoce
     * CreditMemo incremental id should be present for log only if GetTaxRequest:DocType is ReturnInvoice
     *
     * @return $this
     */
    public function interpretRelatedInformation()
    {
        try {
            foreach ($this->getItems() as $item) {
                $relatedData = new Varien_Object();
                switch ($this->getRequestDocType($item)) {
                    case 'SalesOrder':
                        $relatedData->setDocType('SalesOrder');
                        $relatedData->setIncrementId($item->getOrderIncrementId());
                        break;
                    case 'SalesInvoice':
                        $relatedData->setDocType('SalesInvoice');
                        $relatedData->setIncrementId($item->getInvoiceIncrementId());
                        break;
                    case 'ReturnInvoice':
                        $relatedData->setDocType('ReturnInvoice');
                        $relatedData->setIncrementId($item->getCreditmemoIncrementId());
                        break;
                    default:
                        break;
                }

                $item->setRelatedData($relatedData);
            }
        } catch (Exception $e) {
            /** expected behaviour */
        }

        return $this;
    }

    /**
     * Regex to get DocType from GetTax request
     *
     * @param OnePica_AvaTax_Model_Records_Log $item
     * @return string
     */
    public function getRequestDocType($item)
    {
        if ($item->getType() === 'GetTax') {
            try {
                preg_match("/\[DocType\:GetTaxRequest\:private\].\=\>.(.*)$/m", $item->getRequest(), $output);
                if (array_key_exists(1, $output)) {
                    return $output[1];
                }
            } catch (Exception $e) {
                /** expected behaviour */
            }
        }

        return false;
    }
}

