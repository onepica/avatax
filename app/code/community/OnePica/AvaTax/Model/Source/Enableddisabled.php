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
 * Enabled/Disabled source model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Source_Enableddisabled
{
    /**
     * Disabled
     */
    const DISABLED = 0;

    /**
     * Enabled
     */
    const ENABLED = 1;

    /**
     * Gets the list of status
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::DISABLED,
                'label' => Mage::helper('avatax')->__('Disabled')
            ),
            array(
                'value' => self::ENABLED,
                'label' => Mage::helper('avatax')->__('Enabled')
            )
        );
    }
}
