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
 * Avatax Rest V2 Observer AdminSystemConfigChangedSectionTax
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Model_Observer_AdminSystemConfigChangedSectionTax extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * Test for required values when admin config setting related to the this extension are changed
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws \Mage_Core_Exception
     */
    public function execute(Varien_Event_Observer $observer)
    {
        if ($this->_getConfigHelper()->isEnabled()) {
            $storeId = Mage::app()->getStore($observer->getEvent()->getStore())->getStoreId();

            $this->_addErrorsToSession($storeId);
        }

        return $this;
    }

    /**
     * Add error messages to session
     *
     * @param int $storeId
     * @return $this
     */
    protected function _addErrorsToSession($storeId)
    {
        $session = $this->_getAdminhtmlSession();
        $errors = $this->_prepareErrors($storeId);

        if (!$errors) {
            return $this;
        }

        if (count($errors) == 1) {
            $session->addError(implode('', $errors));
        } else {
            $session->addError(
                Mage::helper('avataxar2')->__('Please fix the following issues:') .
                '<br/> - ' . implode('<br/> - ', $errors)
            );
        }

        return $this;
    }

    /**
     * Prepare errors array
     *
     * @param int $storeId
     * @return array
     */
    protected function _prepareErrors($storeId)
    {
        $errors = array();
        $errors = array_merge(
            $errors,
            $this->_sendPing($storeId),
            $this->_sendEcomPing($storeId)
        );

        return $errors;
    }

    /**
     * Send ping request
     *
     * @param int $storeId
     * @return array
     */
    protected function _sendPing($storeId)
    {
        $errors = array();
        $ping = Mage::getSingleton('avataxar2/service_avatax_ping')->ping($storeId);
        if ($ping !== true) {
            $errors[] = $ping;
        }

        return $errors;
    }

    /**
     * Send ping request
     *
     * @param int $storeId
     * @return array
     */
    protected function _sendEcomPing($storeId)
    {
        $errors = array();
        $ping = Mage::getSingleton('avataxar2/service_ecom_ping')->ping($storeId);
        if ($ping !== true) {
            $errors[] = $ping;
        }

        return $errors;
    }

    /**
     * @return OnePica_AvaTaxAr2_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avataxar2/config');
    }
}
