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
 * Config export entity model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Export_Entity_Config
{
    /**
     * Adapter
     *
     * @var OnePica_AvaTax_Model_Export_Adapter_Abstract
     */
    protected $_adapter;

    /**
     * Collection
     *
     * @var Mage_Core_Model_Mysql4_Collection_Abstract
     */
    protected $_collection = null;

    /**
     * Get adapter
     *
     * @return OnePica_AvaTax_Model_Export_Adapter_Abstract
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Set adapter
     *
     * @param OnePica_AvaTax_Model_Export_Adapter_Abstract $adapter
     * @return $this
     */
    public function setAdapter($adapter)
    {
        $this->_adapter = $adapter;

        return $this;
    }

    /**
     * Get collection
     *
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function getCollection()
    {
        if ($this->_collection === null) {
            $this->_collection = new Varien_Data_Collection();

            $this->_collection->addItem(
                new Varien_Object(
                    array(
                        'name' => 'Export info',
                        'data' => $this->_getExportHeader()
                    )
                )
            );

            $this->_collection->addItem(
                new Varien_Object(
                    array(
                        'name' => 'AvaTax Config',
                        'data' => $this->_getAvaTaxConfig()
                    )
                )
            );

            $this->_collection->addItem(
                new Varien_Object(
                    array(
                        'name' => 'Extensions',
                        'data' => $this->_getExtensions()
                    )
                )
            );

            $this->_collection->addItem(
                new Varien_Object(
                    array(
                        'name' => 'Rewrites',
                        'data' => $this->_getRewrites()
                    )
                )
            );
        }

        return $this->_collection;
    }

    /**
     * Get content
     *
     * @return string
     * @throws OnePica_AvaTax_Exception
     */
    public function getContent()
    {
        if (!$this->getAdapter()) {
            throw new OnePica_AvaTax_Exception('Adapter should be set before export process');
        }

        if (!$this->getCollection()) {
            throw new OnePica_AvaTax_Exception('Collection should be set before export process');
        }

        return $this->getAdapter()->setCollection($this->getCollection())->getContent();
    }

    /**
     * Get export header
     *
     * @return array
     */
    protected function _getExportHeader()
    {
        return array(
            'version'         => Mage::getResourceModel('core/resource')->getDbVersion('avatax_records_setup'),
            'stores'          => count(Mage::app()->getStores()),
            'created_at'      => $this->_getDateModel()->gmtDate(DATE_W3C),
            'created_by'      => Mage::getUrl('/'),
            'magento_version' => Mage::getVersion(),
            'avatax_version'  => Mage::getResourceModel('core/resource')->getDbVersion('avatax_records_setup'),
            'Stores'          => count(Mage::app()->getStores()),
        );
    }

    /**
     * @return array
     */
    protected function _getAvaTaxConfig()
    {
        $avaTaxConfig = Mage::getConfig()->getNode('default/tax/avatax')->asArray();
        ksort($avaTaxConfig);

        return $avaTaxConfig;
    }

    /**
     * @return array
     */
    protected function _getExtensions()
    {
        $extensions = array();
        foreach (Mage::getConfig()->getNode('modules')->asArray() as $name => $child) {
            try {
                if (array_key_exists('depends', $child)) {
                    $child['depends'] = implode(', ', array_keys($child['depends']));
                }
                $extensions[(string)$child['codePool']][$name] = (array)$child;
            } catch (Exception $exception) {
                Mage::log($exception->getMessage(), null, 'avatax.log');
            }
        }

        return $extensions;
    }

    /**
     * @return array
     */
    protected function _getRewrites()
    {

        $rewrites = array();
        foreach (Mage::getConfig()->getNode()->xpath('//config//rewrite') as $key => $rewrite) {
            try {
                $rewrites[$rewrite->getParent()->getParent()->getName()][$rewrite->getParent()->getName()][]
                    = $rewrite->asArray();
            } catch (Exception $exception) {
                Mage::log($exception->getMessage(), null, 'avatax.log');
            }
        }

        return $rewrites;
    }

    /**
     * Get core date model
     *
     * @return \Mage_Core_Model_Date
     */
    protected function _getDateModel()
    {
        return Mage::getSingleton('core/date');
    }
}
