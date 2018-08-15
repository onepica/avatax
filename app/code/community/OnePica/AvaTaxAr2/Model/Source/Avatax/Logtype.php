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

    const REST_COMMON = 'RestV2:Common';

    const REST_CUSTOMER = 'RestV2:Customer';

    const REST_CUSTOMER_CERTIFICATE = 'RestV2:CustomerCertificate';

    const REST_CUSTOMER_CERT_EXPRESS_INVITE = 'RestV2:CustomerCertExpressInvite';

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

        $array[] = array(
            'value' => self::REST_COMMON,
            'label' => Mage::helper('avatax')->__('Rest V2: Common')
        );

        $array[] = array(
            'value' => self::REST_CUSTOMER,
            'label' => Mage::helper('avatax')->__('Rest V2: Customer')
        );

        $array[] = array(
            'value' => self::REST_CUSTOMER_CERTIFICATE,
            'label' => Mage::helper('avatax')->__('Rest V2: Customer Certificate')
        );

        $array[] = array(
            'value' => self::REST_CUSTOMER_CERT_EXPRESS_INVITE,
            'label' => Mage::helper('avatax')->__('Rest V2: Customer Cert Express Invite')
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
        $logTypes[self::REST_COMMON] = self::REST_COMMON;
        $logTypes[self::REST_CUSTOMER] = self::REST_CUSTOMER;
        $logTypes[self::REST_CUSTOMER_CERTIFICATE] = self::REST_CUSTOMER_CERTIFICATE;
        $logTypes[self::REST_CUSTOMER_CERT_EXPRESS_INVITE] = self::REST_CUSTOMER_CERT_EXPRESS_INVITE;

        return $logTypes;
    }
}
