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
    /**
     * @var bool
     */
    protected $_relatedInformationAdded;

    /**
     * @var \Mage_Sales_Model_Resource_Order_Address_Collection
     */
    protected $_relatedOrderAddresses;

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
        if (Mage::helper('avatax/config')->getConfigAdvancedLog()) {
            $this->_relatedInformationAdded = true;
        }

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
    protected function interpretRelatedInformation()
    {
        try {
            /** @var  \OnePica_AvaTax_Model_Records_Log $item */
            foreach ($this->getItems() as $item) {

                /* when export data need empty order, invoice or creditmemo column */
                $item->setOrderIncrementId('');
                $item->setInvoiceIncrementId('');
                $item->setCreditMemoIncrementId('');

                $this->interpretOrderIncrementId($item);

                if ($item->getType() === 'GetTax') {
                    switch ($this->_getRequestData($item, 'DocType')) {
                        case 'SalesInvoice':
                            $item->setInvoiceIncrementId($this->_getRequestData($item, 'DocCode'));
                            break;
                        case 'ReturnInvoice':
                            $item->setCreditMemoIncrementId($this->_getRequestData($item, 'DocCode'));
                            break;
                        default:
                            break;
                    }
                }

                if ($item->getType() === 'Queue') {
                    switch ($this->_getRequestData($item, 'type')) {
                        case 'Invoice':
                            $item->setInvoiceIncrementId($this->_getRequestData($item, 'entity_increment_id'));
                            break;
                        case 'Credit memo':
                            $item->setCreditMemoIncrementId($this->_getRequestData($item, 'entity_increment_id'));
                            break;
                        default:
                            break;
                    }
                }
            }
        } catch (Exception $e) {
            /* expected behaviour */
        }

        return $this;
    }

    /**
     * Interpret Order Increment Id for avatax log record
     *
     * @param OnePica_AvaTax_Model_Records_Log $item
     *
     * @return $this
     */
    protected function interpretOrderIncrementId(\OnePica_AvaTax_Model_Records_Log $item)
    {
        $orderAddresses = $this->getRelatedOrderAddresses();
        if (!$orderAddresses->isLoaded()) {
            $orderAddresses->load();
        }

        $quoteAddressId = $item->getQuoteAddressId();
        if ($quoteAddressId) {
            $address = $orderAddresses->getItemByColumnValue('avatax_quote_address_id', $quoteAddressId);
            if ($address) {
                $item->setOrderIncrementId($address->getOrderIncrementId());
            }
        }

        return $this;
    }

    /**
     * Gets related order addresses information, plus order increment id
     *
     * @return \Mage_Sales_Model_Resource_Order_Address_Collection|null
     */
    protected function getRelatedOrderAddresses()
    {
        if (!$this->_relatedOrderAddresses) {

            $ids = array();
            foreach ($this->getItems() as $item) {
                $id = $item->getQuoteAddressId();
                if ($id) {
                    $ids[$id] = true;
                }
            }
            $ids = array_keys($ids);

            $this->_relatedOrderAddresses = Mage::getModel('sales/order_address')
                ->getCollection()
                ->join(
                    array('order' => 'sales/order'),
                    'main_table.parent_id = order.entity_id',
                    array(
                        'order_increment_id' => 'increment_id'
                    )
                )
                ->addFieldToFilter('avatax_quote_address_id', array('in' => $ids));

            $this->_relatedOrderAddresses->getSelect()->where(
                '   (order.is_virtual = 0 AND main_table.address_type = "shipping")
                 OR (order.is_virtual = 1 AND main_table.address_type = "billing")'
            );
        }

        return $this->_relatedOrderAddresses;
    }

    /**
     * Regex to get DocType from GetTax request
     *
     * @param OnePica_AvaTax_Model_Records_Log $item
     * @param string                           $fieldName
     * @return string
     */
    protected function _getRequestData($item, $fieldName)
    {
        try {
            $output = null;
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
            /* expected behaviour */
        }

        return false;
    }

    /**
     * Prepares data only for specific quote_id
     *
     * @param int $quoteId
     * @return $this
     */
    public function selectOnlyForQuote($quoteId)
    {
        $this->getSelect()->where('main_table.quote_id = ?', $quoteId);

        return $this;
    }
}
