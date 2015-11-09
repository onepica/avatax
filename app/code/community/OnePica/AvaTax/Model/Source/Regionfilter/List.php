<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Regionfilter source model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Source_Regionfilter_List
{
    /**
     * Options
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Get option array
     *
     * @throws Mage_Core_Model_Store_Exception
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->_options) {
            return $this->_options;
        }
        $this->_initOptions();
        return $this->_options;
    }

    /**
     * Options initializations
     *
     * @throws Mage_Core_Model_Store_Exception
     * @return $this
     */
    protected function _initOptions()
    {
        foreach ($this->_getCountryList() as $country) {
            $regions = $this->_prepareRegionList($country);
            $this->_options[] = array(
                'label' => Mage::app()->getLocale()->getCountryTranslation($country),
                'value' => $regions
            );
        }

        return $this;
    }

    /**
     * Get regions by country
     *
     * @param string $country
     * @return array
     */
    protected function _getRegionsByCountry($country)
    {
        return Mage::getResourceModel('directory/region_collection')
            ->addCountryFilter($country)
            ->loadData()
            ->toOptionArray();
    }

    /**
     * Prepare region list.
     *
     * If magento, don't have region list for selected country,
     * instead region list we add a 'All' option with country code value
     *
     * @param string $country
     * @return array
     */
    protected function _prepareRegionList($country)
    {
        $regions = $this->_getRegionsByCountry($country);
        array_shift($regions);
        if (!$regions) {
            $regions[] = array(
                'title' => 'All',
                'label' => 'All',
                'value' => $country
            );
        }

        return $regions;
    }

    /**
     * Get country list
     *
     * @throws Mage_Core_Model_Store_Exception
     * @return array
     */
    protected function _getCountryList()
    {
        return Mage::helper('avatax/address')->getTaxableCountryByCurrentScope();
    }

    /**
     * Get data helper
     *
     * @return OnePica_AvaTax_Helper_Data
     */
    protected function _getDataHelper()
    {
        return Mage::helper('avatax');
    }
}
