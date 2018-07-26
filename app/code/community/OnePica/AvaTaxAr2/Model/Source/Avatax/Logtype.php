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
class OnePica_AvaTaxAr2_Model_Source_Avatax_Logtype extends OnePica_AvaTax_Model_Source_Avatax_Logtype
{
    /**
     * Ping type
     */
    const PING_ECOM = 'PingCertAPI';

    const PING_REST = 'PingRestV2';

    /**
     * Gets the list of type for the admin config dropdown
     *
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();

        $array[] = array(
            'value' => self::PING_ECOM,
            'label' => Mage::helper('avatax')->__('Ping Cert API')
        );

        $array[] = array(
            'value' => self::PING_REST,
            'label' => Mage::helper('avatax')->__('Ping Rest V2')
        );
        sort($array);

        return $array;
    }

    /**
     * Get log types array
     *
     * @return array
     */
    public function getLogTypes()
    {
        $logTypes = parent::getLogTypes();
        $logTypes[self::PING_ECOM] = self::PING_ECOM;
        $logTypes[self::PING_REST] = self::PING_REST;

        return $logTypes;
    }
}
