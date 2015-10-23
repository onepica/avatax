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
 * Log type source model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Source_Avatax16_Logtype
{
    /**
     * Calculation type
     */
    const CALCULATION = 'calculation';

    /**
     * Get tax type
     */
    const TRANSACTION = 'transaction';

    /**
     * Gets the list of type for the admin config dropdown
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            array(
                'value' => self::CALCULATION,
                'label' => Mage::helper('avatax')->__('Calculation')
            ),
            array(
                'value' => self::TRANSACTION,
                'label' => Mage::helper('avatax')->__('Transaction')
            ),
        );
    }
}
