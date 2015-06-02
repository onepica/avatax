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
 * Log mode source model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Source_Logmode
{
    /**
     * Only errors mode
     */
    const ERRORS = 0;

    /**
     * Normal mode
     */
    const NORMAL = 1;

    /**
     * Debug mode
     */
    const DEBUG = 2;

    /**
     * Gets the list of cache methods for the admin config dropdown
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::ERRORS,
                'label' => Mage::helper('avatax')->__('Log only errors')
            ),
            array(
                'value' => self::NORMAL,
                'label' => Mage::helper('avatax')->__('Log all actions (recommended)')
            ),
            array(
                'value' => self::DEBUG,
                'label' => Mage::helper('avatax')->__('Log all actions with trace (debug mode)')
            )
        );
    }
}
