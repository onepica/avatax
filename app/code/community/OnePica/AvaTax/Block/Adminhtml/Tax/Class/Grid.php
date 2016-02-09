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
 * Admin tax grid
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Block_Adminhtml_Tax_Class_Grid extends Mage_Adminhtml_Block_Tax_Class_Grid
{
    /**
     * Construct: Set the help url
     */
    public function __construct()
    {
        $url = Mage::helper('avatax')->getDocumentationUrl();
        Mage::helper('adminhtml')->setPageHelpUrl($url);
        return parent::__construct();
    }

    /**
     * Prepare columns
     *
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'op_avatax_code', array(
                'header' => Mage::helper('avatax')->__('AvaTax Code'),
                'align'  => 'left',
                'index'  => 'op_avatax_code',
                'width'  => '175px'
            )
        );

        return parent::_prepareColumns();
    }
}
