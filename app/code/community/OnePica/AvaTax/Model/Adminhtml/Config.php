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
 * Admin config model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Adminhtml_Config extends OnePica_AvaTax_Model_Adminhtml_Config_Abstract
{
    /**
     * Init modules configuration
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _initSectionsAndTabs()
    {
        if ($this->_getConfigHelper()->isAvaTaxDisabled()) {
            $this->_getDataHelper()->isAvatax16()
                ? $this->_addCustomConfig(array('system-disabled.xml', 'system-avatax16-disabled.xml'))
                : $this->_addCustomConfig(array('system-disabled.xml'));
        } else {
            $this->_getDataHelper()->isAvatax16()
                ? $this->_addCustomConfig(array('system-avatax16.xml'))
                : parent::_initSectionsAndTabs();
        }

        $this->_initPluginVersion();
        $this->_expandAllDependencies();

        return $this;
    }

    /**
     * Added custom config
     *
     * @param array $customConfig
     * @return $this
     */
    protected function _addCustomConfig(array $customConfig)
    {
        $config = Mage::getConfig()->loadModulesConfiguration('system.xml')->applyExtends();

        Mage::dispatchEvent('adminhtml_init_system_config', array('config' => $config));

        foreach ($customConfig as $item) {
            //these 4 lines are the only added content
            $configFile = $this->_getConfigHelper()->getEtcPath() . DS . $item;

            /** @var Mage_Core_Model_Config_Base $mergeModel */
            $mergeModel = Mage::getModel('core/config_base');
            $mergeModel->loadFile($configFile);

            $config = $config->extend($mergeModel, true);
        }

        $this->_sections = $config->getNode('sections');
        $this->_tabs = $config->getNode('tabs');

        return $this;
    }

    /**
     * Get avatax data helper
     *
     * @return OnePica_AvaTax_Helper_Data
     */
    protected function _getDataHelper()
    {
        return Mage::helper('avatax/data');
    }

    /**
     * Get avatax config helper
     *
     * @return \OnePica_AvaTax_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avatax/config');
    }

    /**
     * Init Plugin Version in AvaTax section comment
     *
     * @return \OnePica_AvaTax_Helper_Config
     */
    protected function _initPluginVersion()
    {
        $taxSection = $this->getSection('tax');
        if ($taxSection) {
            $pathComment = 'groups/avatax/comment';
            $comment = $taxSection->descend($pathComment);
            if ($comment) {
                $comment = $comment[0];

                $version = Mage::getConfig()->getNode('modules/OnePica_AvaTax/version');
                if ($this->_getDataHelper()->isAvatax16()) {
                    $version16 = Mage::getConfig()->getNode('default/tax/avatax/avatax16_extension_version');
                    $version = $version16.' ('.$version.')';
                }

                $processor = new Varien_Filter_Template();
                $processor->setVariables(array('avatax_ver' => $version));
                $precessedComment = $processor->filter($comment);
                $taxSection->setNode($pathComment, $precessedComment);
            }
        }

        return $this;
    }

    /**
     * Expand all dependencies for older versions of magento
     *
     * @return \OnePica_AvaTax_Helper_Config
     */
    protected function _expandAllDependencies()
    {
        $ver = Mage::getVersionInfo();
        if ($ver['minor'] <= 6 || $ver['minor'] == 10) {
            $taxSection = $this->getSection('tax');
            if ($taxSection) {
                $allDependencies = $taxSection->xpath('groups/avatax/fields/*/depends');
                if ($allDependencies) {
                    foreach ($allDependencies as $dependecy) {
                        $parent = $dependecy->getParent();
                        if ($parent) {
                            $parent->setNode('depends', null);
                        }
                    }
                }
            }
        }

        return $this;
    }
}
