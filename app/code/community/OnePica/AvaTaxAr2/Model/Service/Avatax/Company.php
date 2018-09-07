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

use Avalara\AvaTaxRestV2\SeverityLevel;

/**
 * The AvaTax Certificate model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Model_Service_Avatax_Company extends OnePica_AvaTaxAr2_Model_Service_Avatax_Abstract
{
    /** @var null|Varien_Object $_company */
    protected $_company = null;

    /**
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return int
     * @throws \Mage_Core_Exception
     */
    public function getCurrentCompanyId($store = null)
    {
        return $this->getCurrentCompany($store)->getId();
    }

    /**
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return \Varien_Object
     * @throws \Mage_Core_Exception
     * @throws \Exception
     */
    public function getCurrentCompany($store = null)
    {
        if ($this->_company === null) {
            $client = $this->_getConfig()->getClient($store);

            $filterParams = array(
                sprintf("companyCode eq '%s'", $this->_getAvaTaxConfigHelper()->getCompanyCode($store))
            );

            $this->setFilter(implode(' AND ', $filterParams));

            $responseQuery = $client->queryCompanies(
                $this->getInclude(), $this->getFilter(), $this->getTop(), $this->getSkip(), $this->getOrderBy()
            );

            $this->_company = $this->processResponse($responseQuery)->getFirstItem();
        }

        return $this->_company;
    }

    /**
     * @return \OnePica_AvaTaxAr2_Model_Service_AvaTax_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('avataxar2/service_avatax_config');
    }

    /**
     * @return \OnePica_AvaTax_Helper_Config
     */
    protected function _getAvaTaxConfigHelper()
    {
        return Mage::helper('avatax/config');
    }
}
