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
 * Admin log grid block
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Block_Adminhtml_Export_Log_Grid extends OnePica_AvaTax_Block_Adminhtml_Export_Abstract_Grid
{
    /**
     * Construct: Sets default sort to id field
     */
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('log_id');
        $this->setGridHeader(
            '<h3 class="icon-head" style="background-image:url('
            . $this->getSkinUrl('images/fam_application_view_tile.gif') . ');">'
            . $this->__('AvaTax Action Log') . '</h3>'
        );
    }

    /**
     * Adds columns to grid
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        return $this->_addColumns(
            array(
                'log_id'     => 'number',
                'store_id'   => 'number',
                'level'      => Mage::getModel('avatax_records/log')->getLevelOptions(),
                'type'       => $this->_getLogTypeModel()->getLogTypes(),
                'created_at' => 'datetime',
            )
        );
    }

    /**
     * Adds collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('avatax_records/log')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Get row url
     *
     * @param OnePica_AvaTax_Model_Records_Log $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/logView', array('id' => $row->getId()));
    }

    /**
     * Get data helper
     *
     * @return OnePica_AvaTax_Model_Source_Logtype
     */
    protected function _getLogTypeModel()
    {
        return Mage::getModel('avatax/source_logtype');
    }
}
