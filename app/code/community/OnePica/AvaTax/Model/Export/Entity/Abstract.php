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
 * Abstract export entity model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
abstract class OnePica_AvaTax_Model_Export_Entity_Abstract
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
     * Get export columns list
     *
     * @return array
     */
    abstract protected function _getExportColumns();

    /**
     * Get collection
     *
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    abstract protected function _getCollection();

    /**
     * Get collection
     *
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function getCollection()
    {
        if ($this->_collection === null) {
            $this->_collection = $this->_getCollection();
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

        $collection = $this->getCollection()
            ->addFieldToSelect($this->_getExportColumns())
            ->setOrder($this->getCollection()->getResource()->getIdFieldName(), 'DESC');

        return $this->getAdapter()
            ->setCollection($collection)
            ->getContent();
    }
}
