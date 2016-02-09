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
 * Admin html export detail view block
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Block_Adminhtml_Export_Log_View extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Store curent event
     *
     * @var OnePical_Avatax_Model_Event
     */
    protected $_currentEevent = null;

    /**
     * Construct: Add back button
     */
    public function __construct()
    {
        parent::__construct();

        $this->_addButton(
            'back', array(
                'label'   => Mage::helper('avatax')->__('Back'),
                'onclick' => "setLocation('" . Mage::getSingleton('adminhtml/url')->getUrl('*/*/log') . "')",
                'class'   => 'back'
            )
        );
    }

    /**
     * Header text getter
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getCurrentEvent()) {
            return '<h3 class="icon-head" style="background-image:url('
                . $this->getSkinUrl('images/fam_application_view_tile.gif') . ');">'
                . $this->__('AvaTax Action Log Entry #%d', $this->getCurrentEvent()->getId())
                . '</h3>';
        }
        return '<h3 class="icon-head" style="background-image:url('
            . $this->getSkinUrl('images/fam_application_view_tile.gif') . ');">'
            . $this->__('AvaTax Action Log Entry Details')
            . '</h3>';
    }

    /**
     * Get current event
     *
     * @return OnePica_AvaTax_Model_Event|null
     */
    public function getCurrentEvent()
    {
        if (null === $this->_currentEevent) {
            $this->_currentEevent = Mage::registry('current_event');
        }
        return $this->_currentEevent;
    }
}
