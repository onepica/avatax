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
 * Admin ajax controller
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Adminhtml_AvaTax_AjaxController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check if is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('avatax');
    }

    /**
     * Log action
     *
     * @return void
     */
    public function getCompaniesAction()
    {
        if (!$this->getRequest()->isAjax() || !$this->_validateFormKey()) {
            return;
        }

        $dataResponse = array();

        try {
            if (!$this->getRequest()->getParam('account')) {
                Mage::throwException('Account number field is empty');
            }

            if (!$this->getRequest()->getParam('license')) {
                Mage::throwException('License field is empty');
            }

            $params = array(
                'url'     => $this->getRequest()->getParam('url'),
                'account' => $this->getRequest()->getParam('account'),
                'license' => $this->getRequest()->getParam('license')
            );

            $dataResponse['companies'] = $this->_prepareCompaniesArray(
                $this->_getConfigHelper()->getAccountCompanies(Mage::app()->getStore()->getStoreId(), $params)
            );

            $dataResponse['success'] = true;
            $dataResponse['message'] = 'Companies successfully fetched';
        } catch (Exception $e) {
            $dataResponse['companies'] = array();
            $dataResponse['success'] = false;
            $dataResponse['message'] = $e->getMessage();
        }

        $this->getResponse()->setBody(json_encode($dataResponse));
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
     * Get config helper
     *
     * @param array $companies
     * @return array
     */
    protected function _prepareCompaniesArray($companies)
    {
        $data = array();
        /** @var \stdClass $company */
        foreach ($companies as $company) {
            $data[] = array(
                'company_code' => $company->CompanyCode,
                'company_name' => $company->CompanyName
            );
        }

        return $data;
    }
}
