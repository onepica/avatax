<?php
/**
 * OnePica_AvaTax
 *
 * NOTICE OF LICENSE
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
 * Class OnePica_AvaTax_Model_AbstractAction
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
abstract class OnePica_AvaTax_Model_AbstractAction extends Mage_Core_Model_Factory
{
    /**
     * Service
     *
     * @var OnePica_AvaTax_Model_Service_Abstract
     */
    protected $_service;

    /**
     * Class constructor
     *
     * @param array $params
     * @throws OnePica_AvaTax_Exception
     */
    public function __construct($params = array())
    {
        $activeService = $this->_getConfigHelper()->getActiveService();
        $this->_service = Mage::getSingleton('avatax/service')->factory($activeService, $params);
    }

    /**
     * Get Service
     * return OnePica_AvaTax_Model_Service_Abstract
     */
    protected function _getService()
    {
        return $this->_service;
    }

    /**
     * Get config helper
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
     * Adds a comment to order history. Method chosen based on Magento version.
     *
     * @param Mage_Sales_Model_Order $order
     * @param string                 $comment
     * @return $this
     */
    protected function _addStatusHistoryComment($order, $comment)
    {
        if (method_exists($order, 'addStatusHistoryComment')) {
            $order->addStatusHistoryComment($comment)->save();
        } elseif (method_exists($order, 'addStatusToHistory')) {
            $order->addStatusToHistory($order->getStatus(), $comment, false)->save();
        }

        return $this;
    }

    /**
     * Set Store Id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_getService()->setStoreId($storeId);

        return $this;
    }
}
