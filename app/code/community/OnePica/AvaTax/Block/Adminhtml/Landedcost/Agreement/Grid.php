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
 * Avatax admin Agreement grid
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Block_Adminhtml_Landedcost_Agreement_Grid
    extends OnePica_AvaTax_Block_Adminhtml_Landedcost_Abstract_Grid
{
    /**
     * Adds columns to grid
     *
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->_addColumns(
            array(
                'id'                     => 'number',
                'avalara_agreement_code' => 'varchar',
                'description'            => 'varchar',
            )
        );

        $this->addColumn(
            'Country List',
            array(
                'header'   => $this->__('Country List'),
                'index'    => 'country_list',
                'renderer' => 'OnePica_AvaTax_Block_Adminhtml_Renderer_Grid_CountryList'
            )
        );

        return $this;
    }

    /**
     * Adds collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var \OnePica_AvaTax_Model_Records_Mysql4_Agreement_Collection $collection */
        $collection = Mage::getModel('avatax_records/agreement')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Get row url
     *
     * @param \OnePica_AvaTax_Model_Records_Agreement $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/agreementEdit', array('id' => $row->getId()));
    }

    /**
     * Prepare mass actions
     *
     * @return \OnePica_AvaTax_Block_Adminhtml_Landedcost_Agreement_Grid
     * @throws \Varien_Exception
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('agreements');

        $this->getMassactionBlock()->addItem(
            'delete', array(
                'label'   => $this->__('Delete'),
                'url'     => $this->getUrl('*/*/agreementMassDelete'),
                'confirm' => $this->__('Are you sure you want to delete selected records?')
            )
        );

        return $this;
    }
}
