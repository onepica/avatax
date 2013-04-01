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


class OnePica_AvaTax_Model_Source_Logtype
{
	const PING		= 'Ping';
	const GET_TAX	= 'GetTax';
	const FILTER	= 'Filter';
	const VALIDATE	= 'Validate';
	const QUEUE		= 'Queue';
	
    /**
	 * Gets the list of type for the admin config dropdown
	 *
	 * @return array
	 */
    public function toOptionArray()
    {
        return array(
            array(
            	'value' => self::PING,
            	'label' => Mage::helper('avatax')->__('Ping')),
			array(
				'value' => self::GET_TAX,
				'label' => Mage::helper('avatax')->__('Get Tax')),
			array(
				'value' => self::FILTER,
				'label' => Mage::helper('avatax')->__('Filter')),
			array(
				'value' => self::VALIDATE,
				'label' => Mage::helper('avatax')->__('Validate')),
			array(
				'value' => self::QUEUE,
				'label' => Mage::helper('avatax')->__('Queue'))


        );
    }
}
