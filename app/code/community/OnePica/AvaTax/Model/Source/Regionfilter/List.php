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


class OnePica_AvaTax_Model_Source_Regionfilter_List
{
    protected $_options;

    public function toOptionArray($isMultiselect=false)
    {
        if (!$this->_options) {
        	$countries = array('US', 'CA');
        	$this->_options = array();
        	
        	$this->_options[] = array(
    			'label' => '',
    			'value' => ''
    		);
        	
        	foreach($countries as $country) {
        		$regions = Mage::getResourceModel('directory/region_collection')
	            	->addCountryFilter($country)
	            	->loadData()
	            	->toOptionArray();
	           	array_shift($regions);
	           	
        		$this->_options[] = array(
        			'label' => Mage::app()->getLocale()->getCountryTranslation($country),
        			'value' => $regions
        		);
        	}
        }
        
        return $this->_options;
    }
}
