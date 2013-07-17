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


class OnePica_AvaTax_Model_Adminhtml_Config extends Mage_Adminhtml_Model_Config
{

    protected function _initSectionsAndTabs() {
    	if(Mage::helper('avatax')->isAnyStoreDisabled()) {
            $config = Mage::getConfig()->loadModulesConfiguration('system.xml')
                ->applyExtends();

            Mage::dispatchEvent('adminhtml_init_system_config', array('config' => $config));
	        
	        //these 4 lines are the only added content
	        $configFile = Mage::helper('avatax')->getEtcPath() . DS . 'system-disabled.xml';
	    	$mergeModel = new Mage_Core_Model_Config_Base();
	        $mergeModel->loadFile($configFile);        
	    	$config = $config->extend($mergeModel, true);
	
	        $this->_sections = $config->getNode('sections');        
	        $this->_tabs = $config->getNode('tabs');
    	} else {
    		return parent::_initSectionsAndTabs();
    	}
    }

}