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

/**
 * @class OnePica_AvaTax_Model_Service_Abstract_Tools
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */

class OnePica_AvaTax_Model_Service_Abstract_Tools extends Varien_Object
{
    /**
     * Avatax cache tag
     */
    const AVATAX_SERVICE_CACHE_GROUP = 'avatax_cache_tags';

    /**
     * Avatax cache tag
     */
    const AVATAX_CACHE_GROUP = 'avatax';

    /**
     * Model cache tag for clear cache in after save and after delete
     */
    protected $_cacheTag = self::AVATAX_SERVICE_CACHE_GROUP;

    /**
     * Length of time in minutes for cached rates
     *
     * @var int
     */
    const CACHE_TTL = 120;

    /**
     * Class pre-constructor
     */
    protected function _construct()
    {
        $this->addData(array('cache_lifetime' => 86400, 'automatic_seralization' => true));
        $this->addCacheTag(array(
            self::AVATAX_SERVICE_CACHE_GROUP,
            Mage::app()->getStore()->getId(),
            (int)Mage::app()->getStore()->isCurrentlySecure()
        ));
    }

    /**
     * Get Key for caching block content
     *
     * @return string
     */
    public function getCacheKey()
    {
        $key = $this->getCacheKeyInfo();
        $key = array_values($key); // ignore array keys
        $key = implode('|', $key);
        $key = sha1($key);
        return $key;
    }

    /**
     *
     * Get list of cache tags applied to model object.
     * Return false if cache tags are not supported by model
     *
     * @return array | false
     */
    public function getCacheTags()
    {
        $tags = false;
        if ($this->_cacheTag) {
            if ($this->_cacheTag === true) {
                $tags = array();
            } else {
                if (is_array($this->_cacheTag)) {
                    $tags = $this->_cacheTag;
                } else {
                    $tags = array($this->_cacheTag);
                }
                $idTags = $this->getData(self::AVATAX_CACHE_GROUP);
                if ($idTags) {
                    $tags = array_merge($tags, $idTags);
                }
            }
        }
        return $tags;
    }

    /**
     * Get cache key for tags
     *
     * @param string $cacheKey
     * @return string
     */
    protected function _getTagsCacheKey($cacheKey = null)
    {
        $cacheKey = !empty($cacheKey) ? $cacheKey : $this->getCacheKey();
        $cacheKey = md5($cacheKey . '_tags');
        return $cacheKey;
    }

    /**
     * Get cache key informative items
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
            'AVATAX_CACHE',
            Mage::app()->getStore()->getId(),
            (int)Mage::app()->getStore()->isCurrentlySecure(),
        );
    }

    /**
     * Get block cache life time
     *
     * @return int
     */
    public function getCacheLifetime()
    {
        return $this->getData('cache_lifetime');
    }

    /**
     * Add tag to block
     *
     * @param string|array $tag
     * @return Mage_Core_Block_Abstract
     */
    public function addCacheTag($tag)
    {
        $tag = is_array($tag) ? $tag : array($tag);
        $tags = !$this->hasData(self::AVATAX_CACHE_GROUP) ?
            $tag : array_merge($this->getData(self::AVATAX_CACHE_GROUP), $tag);
        $this->setData(self::AVATAX_CACHE_GROUP, $tags);
        return $this;
    }

    /**
     * Load block html from cache storage
     *
     * @return string | false
     */
    protected function _loadCache()
    {
        if (!$this->_getApp()->useCache(self::AVATAX_CACHE_GROUP)) {
            return false;
        }
        $cacheKey = $this->getCacheKey();
        $cacheData = $this->_getApp()->loadCache($cacheKey);
        return $cacheData;
    }

    /**
     * Save cache
     *
     * @param mixed $data
     * @return $this
     */
    protected function _saveCache($data)
    {
        $cacheKey = $this->getCacheKey();
        $tags = $this->getCacheTags();
        if (is_array($data)){
            $data = serialize($data);
        }

        $this->_getApp()->saveCache($data, $cacheKey, $tags, $this->getCacheLifetime());
        $this->_getApp()->saveCache(
            json_encode($tags),
            $this->_getTagsCacheKey($cacheKey),
            $tags,
            $this->getCacheLifetime()
        );
        return $this;
    }

    /**
     * Retrieve application instance
     *
     * @return Mage_Core_Model_App
     */
    protected function _getApp()
    {
        return Mage::app();
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('avatax');
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Address
     */
    protected function _getAddressHelper()
    {
        return Mage::helper('avatax/address');
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avatax/config');
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getErrorsHelper()
    {
        return Mage::helper('avatax/errors');
    }

}
