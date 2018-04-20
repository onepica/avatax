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
class OnePica_AvaTax_Block_Adminhtml_Landedcost_HsCode_Grid
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
        return $this->_addColumns(
            array(
                'id'          => 'number',
                'hs_code'     => 'varchar',
                'description' => 'varchar',
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
        /** @var OnePica_AvaTax_Model_Records_Mysql4_Log_Collection $collection */
        $collection = Mage::getModel('avatax_records/hsCode')->getCollection();
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
        return $this->getUrl('*/*/hscodeView', array('id' => $row->getId()));
    }
}
