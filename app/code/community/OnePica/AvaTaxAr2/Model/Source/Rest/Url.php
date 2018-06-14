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
 * @copyright  Copyright (c) 2015 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Service Url source model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Model_Source_Rest_Url
{
    /**
     * Url for production
     */
    const PRODUCTION_URL = 'https://rest.avatax.com/';

    /**
     * Url for development
     */
    const DEVELOPMENT_URL = 'https://sandbox-rest.avatax.com/';

    /**
     * Gets the list of urls for the admin config dropdown
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::PRODUCTION_URL,
                'label' => Mage::helper('avatax')->__('Production' . ' (' . self::PRODUCTION_URL . ')')
            ),
            array(
                'value' => self::DEVELOPMENT_URL,
                'label' => Mage::helper('avatax')->__('Development' . ' (' . self::DEVELOPMENT_URL . ')')
            ),
        );
    }
}
