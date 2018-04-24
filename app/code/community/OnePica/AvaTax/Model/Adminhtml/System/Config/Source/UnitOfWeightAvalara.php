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
 * Unit Of Weight source config model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Adminhtml_System_Config_Source_UnitOfWeightAvalara
{
    /** kilograms */
    const KG = 'kg';

    /** grams */
    const G = 'g';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::KG, 'label' => Mage::helper('avatax')->__(self::KG)),
            array('value' => self::G, 'label' => Mage::helper('avatax')->__(self::G))
        );
    }
}
