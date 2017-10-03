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
        $this->getSelect()->where(
            'order.is_virtual is null 
                or (order.is_virtual = 0 AND order_address.address_type = "shipping")
                or (order.is_virtual = 1 AND order_address.address_type = "billing")'
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
                if ($item->getType() === 'GetTax') {
                    switch ($this->getRequestData($item, 'DocType')) {
                        case 'SalesInvoice':
                            $item->setInvoiceIncrementId($this->getRequestData($item, 'DocCode'));
                            break;
                        case 'ReturnInvoice':
                            $item->setCreditMemoIncrementId($this->getRequestData($item, 'DocCode'));
                            break;
                        default:
                            break;
                    }
                }
                if ($item->getType() === 'Queue') {
                    switch ($this->getRequestData($item, 'type')) {
                        case 'Invoice':
                            $item->setInvoiceIncrementId($this->getRequestData($item, 'entity_increment_id'));
                            break;
                        case 'Credit memo':
                            $item->setCreditMemoIncrementId($this->getRequestData($item, 'entity_increment_id'));
                            break;
                        default:
                            break;
                    }
                }
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
     * @param string                           $fieldName
     * @return string
     */
    public function getRequestData($item, $fieldName)
    {
        try {
            switch ($item->getType()) {
                case 'GetTax':
                    preg_match(
                        "/\[" . $fieldName . "\:GetTaxRequest\:private\].\=\>.(.*)$/m", $item->getRequest(), $output
                    );
                    break;
                case 'Queue':
                    preg_match("/\[" . $fieldName . "\].\=\>.(.*)$/m", $item->getRequest(), $output);
                    break;
                default:
                    break;
            }
            if ($output && is_array($output) && array_key_exists(1, $output)) {
                return $output[1];
            }
        } catch (Exception $e) {
            /** expected behaviour */
        }

        return false;
    }
}

