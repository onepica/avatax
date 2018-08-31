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
        $result = array();
        $companies = $this->_getCompanies();

        /** @var \stdClass $company */
        foreach ($companies as $company) {
            $result[] = array(
                'value' => $company->CompanyCode,
                'label' => $company->CompanyName . ' (' . $company->CompanyCode . ')'
            );
        }

        /* get data from config if there is no companies */
        if (!$result) {
            $companyCodeConfig = (string)$this->_getConfigHelper()->getCompanyCode(Mage::app()->getStore()->getId());

            $result = array(
                array(
                    'value' => $companyCodeConfig,
                    'label' => $companyCodeConfig
                )
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
}
