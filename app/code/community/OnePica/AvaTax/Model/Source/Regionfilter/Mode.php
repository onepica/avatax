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
 * Regionfilter mode source model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Source_Regionfilter_Mode
{
    /**
     * Gets the list of cache methods for the admin config dropdown
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => OnePica_AvaTax_Model_Service_Abstract_Config::REGIONFILTER_OFF,
                'label' => Mage::helper('avatax')->__('None')
            ),
            array(
                'value' => OnePica_AvaTax_Model_Service_Abstract_Config::REGIONFILTER_TAX,
                'label' => Mage::helper('avatax')->__('Filter tax calculations')
            ),
            array(
                'value' => OnePica_AvaTax_Model_Service_Abstract_Config::REGIONFILTER_ALL,
                'label' => Mage::helper('avatax')->__('Filter tax calculations & address options')
            )
        );
    }
}
