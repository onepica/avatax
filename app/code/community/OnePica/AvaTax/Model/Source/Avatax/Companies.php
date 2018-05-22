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
 * @class      OnePica_AvaTax_Model_Source_Avatax_Actions
 * Actions source model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Source_Avatax_Companies
{
    /**
     * Gets the list of cache methods for the admin config dropdown
     *
     * @return array
     */
    public function toArray()
    {
        $result = array(
            array(
                'value' => '',
                'label' => ''
            )
        );

        /** @var \stdClass $company */
        foreach ($this->_getCompanies() as $company) {
            $result[] = array(
                'value' => $company->CompanyCode,
                'label' => $company->CompanyName
            );
        }

        return $result;
    }

    /**
     * Get companies for account
     *
     * @return array
     */
    protected function _getCompanies()
    {
        try {
            $companies = $this->_getConfigHelper()->getAccountCompanies(Mage::app()->getStore()->getId());
        } catch (Exception $e) {
            // return empty array if user or account could not be authenticated.
            $companies = array();
        }

        return (array)$companies;
    }

    /**
     * Get config helper
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avatax/config');
    }

    /**
     * Get Service Config
     *
     * @return OnePica_AvaTax_Model_Service_Avatax_Config
     */
    protected function _getServiceConfig()
    {
        return Mage::getModel('avatax/service_avatax_config')->init();
    }
}
