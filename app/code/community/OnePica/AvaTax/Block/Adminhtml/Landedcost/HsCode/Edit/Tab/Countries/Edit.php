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
class OnePica_AvaTax_Block_Adminhtml_Landedcost_HsCode_Edit_Tab_Countries_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * OnePica_AvaTax_Block_Adminhtml_Landedcost_HsCodeCountries_Edit constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'avatax';
        $this->_controller = 'adminhtml_landedcost_hsCode_edit_tab_countries';

        $this->_updateButton('save', 'label', $this->__('Save HS Code'));
        $this->_updateButton('delete', 'label', $this->__('Delete Item'));
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('hs_code_countries_data') && Mage::registry('hs_code_countries_data')->getId()) {
            return $this->__(
                "Edit Item '%s'", $this->escapeHtml(Mage::registry('hs_code_countries_data')->getHsFullCode())
            );
        } else {
            return $this->__('Add Item');
        }
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            '*/*/hscodeEdit', array(
                'id'         => $this->getRequest()->getParam('hs_code_id'),
                'active_tab' => 'grid_section'
            )
        );
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getDeleteUrl()
    {
        return $this->getUrl(
            '*/*/hscodecountriesDelete', array(
                $this->_objectId              => $this->getRequest()->getParam($this->_objectId),
                'hs_code_id'                  => $this->getRequest()->getParam('hs_code_id'),
                Mage_Core_Model_Url::FORM_KEY => $this->getFormKey()
            )
        );
    }
}

