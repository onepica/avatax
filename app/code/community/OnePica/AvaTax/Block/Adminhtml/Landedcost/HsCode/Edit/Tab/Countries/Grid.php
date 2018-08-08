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
 * Avatax admin HS code grid
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Block_Adminhtml_Landedcost_HsCode_Edit_Tab_Countries_Grid
    extends OnePica_AvaTax_Block_Adminhtml_Landedcost_Abstract_Grid
{
    /**
     * Constructor: sets grid id and sort order
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('landedcost_countries_grid');
        $this->setUseAjax(true);
    }

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
                'id'            => 'number',
                'hs_full_code'  => 'varchar',
            ),
            array(
                'hs_full_code' => array('header' => Mage::helper('avatax')->__('HS Code'))
            )
        );

        $this->addColumn(
            'Country List',
            array(
                'header'   => $this->__('Country List'),
                'index'    => 'country_codes',
                'renderer' => 'OnePica_AvaTax_Block_Adminhtml_Renderer_Grid_CountryList'
            )
        );

        return $this;
    }

    /**
     * Adds collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     * @throws \Exception
     */
    protected function _prepareCollection()
    {
        /** @var \OnePica_AvaTax_Model_Records_Mysql4_HsCodeCountry_Collection $collection */
        $collection = Mage::getModel('avatax_records/hsCodeCountry')->getCollection();
        $hsCodeId = $this->getRequest()->getParam('id');

        if ($hsCodeId) {
            $collection->addFieldToFilter('hs_id', $hsCodeId);
            $this->setCollection($collection);
        }

        return parent::_prepareCollection();
    }

    /**
     * Get row url
     *
     * @param OnePica_AvaTax_Model_Records_Log $row
     * @return string
     * @throws \Exception
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/hscodecountriesEdit', array(
                'id'         => $row->getId(),
                'hs_code_id' => $this->getRequest()->getParam('id')
            )
        );
    }

    /**
     * Prepare mass actions
     *
     * @return \OnePica_AvaTax_Block_Adminhtml_Landedcost_HsCode_Edit_Tab_Countries_Grid
     * @throws \Varien_Exception
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('hscodecountries');

        $this->getMassactionBlock()->addItem(
            'delete', array(
                'label'   => $this->__('Delete'),
                'url'     => $this->getUrl(
                    '*/*/hscodecountriesMassDelete', array(
                        'hscode_id' => $this->getRequest()->getParam('id'),
                    )
                ),
                'confirm' => $this->__('Are you sure you want to delete selected records?')
            )
        );

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/hscodecountriesGrid', array(
                'id'       => $this->getRequest()->getParam('id'),
                '_current' => true
            )
        );
    }
}
