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
class OnePica_AvaTax_Block_Adminhtml_Landedcost_Agreement
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'avatax';
        $this->_controller = 'adminhtml_landedcost_agreement';
        $this->_headerText = $this->__('Agreements');

        parent::__construct();
        $this->_updateButton('add', 'label', $this->__('Add New Agreement'));
    }

    /**
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/agreementNew');
    }
}
