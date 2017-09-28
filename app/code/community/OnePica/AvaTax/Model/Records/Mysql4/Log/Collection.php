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

    public function addRelatedInfoToSelect()
    {

        $this->getSelect()->joinLeft(
            array('order_address' => $this->getTable('sales/order_address')),
            'main_table.quote_address_id = order_address.avatax_quote_address_id',
            array(
                'order_address.entity_id' => 'entity_id',
                'order_address.parent_id' => 'parent_id',
            )
        );
        $this->getSelect()->joinLeft(
            array('order' => $this->getTable('sales/order')),
            'order_address.parent_id = order.entity_id',
            array(
                'order.entity_id' => 'entity_id',
                'order.increment_id' => 'increment_id',
            )
        );
        $this->getSelect()->joinLeft(
            array('invoice' => $this->getTable('sales/invoice')),
            'order.entity_id = invoice.order_id',
            array(
                'invoice.entity_id'    => 'entity_id',
                'invoice.increment_id' => 'increment_id'
            )
        );
        $this->getSelect()->joinLeft(
            array('creditmemo' => $this->getTable('sales/creditmemo')),
            'order.entity_id = creditmemo.order_id',
            array(
                'creditmemo.entity_id'    => 'entity_id',
                'creditmemo.increment_id' => 'increment_id'
            )
        );

        $this->_relatedInformationAdded = true;

        return $this;
    }

    /**
     *
     */
    public function interpretRelatedInformation()
    {

        /**
         * Related Data Interpretation:
         * SalesOrder id and incremental id should be present for log only if GetTaxRequest:DocType is SalesOrder
         * SalesInvoice incremental id should be present for log only if GetTaxRequest:DocType is SalesInvoce
         * CreditMemo incremental id should be present for log only if GetTaxRequest:DocType is ReturnInvoice
         * interpretRelatedInformation content should be wrapped into try - catch block
         */

        //TODO: implement
        return $this;
    }

    /**
     * Redeclare after load method for specifying collection items original data
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->_relatedInformationAdded) {
            $this->interpretRelatedInformation();
        }

        return $this;
    }
}

