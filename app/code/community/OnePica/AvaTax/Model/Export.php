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
 * Export model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Export
{
    /**
     * Entity
     *
     * @var OnePica_AvaTax_Model_Export_Entity_Abstract
     */
    protected $_entity = null;

    /**
     * Adapter
     *
     * @var OnePica_AvaTax_Model_Export_Adapter_Abstract
     */
    protected $_adapter = null;

    /**
     * Get entity
     *
     * @return OnePica_AvaTax_Model_Export_Entity_Abstract
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Set entity
     *
     * @param OnePica_AvaTax_Model_Export_Entity_Abstract $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->_entity = $entity;
        return $this;
    }

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
     * Get export content
     *
     * @return string
     * @throws OnePica_AvaTax_Exception
     */
    public function getContent()
    {
        if (!$this->getEntity()) {
            throw new OnePica_AvaTax_Exception('Entity should be set before export process');
        }

        if (!$this->getAdapter()) {
            throw new OnePica_AvaTax_Exception('Adapter should be set before export process');
        }

        return $this->getEntity()
            ->setAdapter($this->getAdapter())
            ->getContent();
    }
}
