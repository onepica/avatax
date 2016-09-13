<?php

/**
 * OnePica_AvaTax_Model_Factory
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

/**
 * The fabric AvaTax model, copied from Mage_Core_Model_Factory (enterprise 1.14.2.2)
 * for compatibility with magento 1.7 versions
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */

class OnePica_AvaTax_Model_Factory
{
    /**
     * Xml path to url rewrite model class alias
     */
    const XML_PATH_URL_REWRITE_MODEL = 'global/url_rewrite/model';

    const XML_PATH_INDEX_INDEX_MODEL = 'global/index/index_model';

    /**
     * Config instance
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Initialize factory
     *
     * @param array $arguments
     */
    public function __construct(array $arguments = array())
    {
        $this->_config = !empty($arguments['config']) ? $arguments['config'] : Mage::getConfig();
    }

    /**
     * Retrieve model object
     *
     * @param string $modelClass
     * @param array|object $arguments
     * @return bool|Mage_Core_Model_Abstract
     */
    public function getModel($modelClass = '', $arguments = array())
    {
        return Mage::getModel($modelClass, $arguments);
    }

    /**
     * Retrieve model object singleton
     *
     * @param string $modelClass
     * @param array $arguments
     * @return Mage_Core_Model_Abstract
     */
    public function getSingleton($modelClass = '', array $arguments = array())
    {
        return Mage::getSingleton($modelClass, $arguments);
    }

    /**
     * Retrieve object of resource model
     *
     * @param string $modelClass
     * @param array $arguments
     * @return Object
     */
    public function getResourceModel($modelClass, $arguments = array())
    {
        return Mage::getResourceModel($modelClass, $arguments);
    }

    /**
     * Retrieve helper instance
     *
     * @param string $helperClass
     * @return Mage_Core_Helper_Abstract
     */
    public function getHelper($helperClass)
    {
        return Mage::helper($helperClass);
    }

    /**
     * Get config instance
     *
     * @return Mage_Core_Model_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Retrieve url_rewrite instance
     *
     * @return Mage_Core_Model_Url_Rewrite
     */
    public function getUrlRewriteInstance()
    {
        return $this->getModel($this->getUrlRewriteClassAlias());
    }

    /**
     * Retrieve alias for url_rewrite model
     *
     * @return string
     */
    public function getUrlRewriteClassAlias()
    {
        return (string)$this->_config->getNode(self::XML_PATH_URL_REWRITE_MODEL);
    }

    /**
     * @return string
     */
    public function getIndexClassAlias()
    {
        return (string)$this->_config->getNode(self::XML_PATH_INDEX_INDEX_MODEL);
    }
}
