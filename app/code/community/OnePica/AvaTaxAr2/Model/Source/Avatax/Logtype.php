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
    const REST_COMMON = 'RestV2:Common';

    const REST_PING = 'RestV2:Ping';

    const REST_COMPANIES = 'RestV2:Companies';

    const REST_COMPANY = 'RestV2:Company';

    const REST_COMPANY_CERTIFICATE_CUSTOMERS = 'RestV2:CompanyCertificateCustomers';

    const REST_COMPANY_CERTIFICATE_ATTACHMENT = 'RestV2:CompanyCertificateAttachment';

    const REST_COMPANY_CERTIFICATE = 'RestV2:CompanyCertificate';

    const REST_COMPANY_CERTIFICATES = 'RestV2:CompanyCertificates';

    const REST_CUSTOMER = 'RestV2:Customer';

    const REST_CUSTOMERS = 'RestV2:Customers';

    const REST_CUSTOMER_CERTIFICATE = 'RestV2:CustomerCertificate';

    const REST_CUSTOMER_CERTIFICATE_UNLINK = 'RestV2:CustomerCertificateUnlink';

    const REST_CUSTOMER_CERT_EXPRESS_INVITE = 'RestV2:CustomerCertExpressInvite';

    const ECOM_COMMON = 'CertAPI:Common';

    const ECOM_PING = 'CertAPI:Ping';

    /**
     * Gets the list of type for the admin config dropdown
     *
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();

        $array[] = array(
            'value' => self::ECOM_PING,
            'label' => Mage::helper('avatax')->__('Cert API: Ping')
        );

        $array[] = array(
            'value' => self::ECOM_COMMON,
            'label' => Mage::helper('avatax')->__('Cert API: Common')
        );

        $array[] = array(
            'value' => self::REST_PING,
            'label' => Mage::helper('avatax')->__('Rest V2: Ping')
        );

        $array[] = array(
            'value' => self::REST_COMMON,
            'label' => Mage::helper('avatax')->__('Rest V2: Common')
        );

        $array[] = array(
            'value' => self::REST_COMPANIES,
            'label' => Mage::helper('avatax')->__('Rest V2: Companies')
        );

        $array[] = array(
            'value' => self::REST_COMPANY,
            'label' => Mage::helper('avatax')->__('Rest V2: Company')
        );

        $array[] = array(
            'value' => self::REST_COMPANY_CERTIFICATE_CUSTOMERS,
            'label' => Mage::helper('avatax')->__('Rest V2: Company Certificate Customers')
        );

        $array[] = array(
            'value' => self::REST_COMPANY_CERTIFICATE_ATTACHMENT,
            'label' => Mage::helper('avatax')->__('Rest V2: Company Certificate Attachment')
        );

        $array[] = array(
            'value' => self::REST_COMPANY_CERTIFICATE,
            'label' => Mage::helper('avatax')->__('Rest V2: Company Certificate')
        );

        $array[] = array(
            'value' => self::REST_COMPANY_CERTIFICATE,
            'label' => Mage::helper('avatax')->__('Rest V2: Company Certificates')
        );

        $array[] = array(
            'value' => self::REST_CUSTOMER,
            'label' => Mage::helper('avatax')->__('Rest V2: Customer')
        );

        $array[] = array(
            'value' => self::REST_CUSTOMERS,
            'label' => Mage::helper('avatax')->__('Rest V2: Customers')
        );

        $array[] = array(
            'value' => self::REST_CUSTOMER_CERTIFICATE,
            'label' => Mage::helper('avatax')->__('Rest V2: Customer Certificate')
        );

        $array[] = array(
            'value' => self::REST_CUSTOMER_CERTIFICATE_UNLINK,
            'label' => Mage::helper('avatax')->__('Rest V2: Customer Certificate Unlink')
        );

        $array[] = array(
            'value' => self::REST_CUSTOMER_CERT_EXPRESS_INVITE,
            'label' => Mage::helper('avatax')->__('Rest V2: Customer Cert Express Invite')
        );

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
        $logTypes[self::ECOM_PING] = self::ECOM_PING;
        $logTypes[self::ECOM_COMMON] = self::ECOM_COMMON;
        $logTypes[self::REST_PING] = self::REST_PING;
        $logTypes[self::REST_COMMON] = self::REST_COMMON;
        $logTypes[self::REST_COMPANY] = self::REST_COMPANY;
        $logTypes[self::REST_COMPANIES] = self::REST_COMPANIES;
        $logTypes[self::REST_COMPANY_CERTIFICATE_CUSTOMERS] = self::REST_COMPANY_CERTIFICATE_CUSTOMERS;
        $logTypes[self::REST_COMPANY_CERTIFICATE_ATTACHMENT] = self::REST_COMPANY_CERTIFICATE_ATTACHMENT;
        $logTypes[self::REST_COMPANY_CERTIFICATE] = self::REST_COMPANY_CERTIFICATE;
        $logTypes[self::REST_COMPANY_CERTIFICATES] = self::REST_COMPANY_CERTIFICATES;
        $logTypes[self::REST_CUSTOMER] = self::REST_CUSTOMER;
        $logTypes[self::REST_CUSTOMERS] = self::REST_CUSTOMERS;
        $logTypes[self::REST_CUSTOMER_CERTIFICATE] = self::REST_CUSTOMER_CERTIFICATE;
        $logTypes[self::REST_CUSTOMER_CERTIFICATE_UNLINK] = self::REST_CUSTOMER_CERTIFICATE_UNLINK;
        $logTypes[self::REST_CUSTOMER_CERT_EXPRESS_INVITE] = self::REST_CUSTOMER_CERT_EXPRESS_INVITE;

        return $logTypes;
    }
}
