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
 * Avatax Observer AdminSystemConfigSaveBefore
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Model_Observer_AdminSystemConfigSaveBefore extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     *  Validates AvaTax configuration
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function execute(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Model_Config_Data $config */
        $config = $observer->getEvent()->getObject();
        if ($config) {
            switch ($config->getSection()) {
                case 'tax':
                    {
                        $idDefaultStoreId = Mage::app()->getWebsite(true)->getDefaultGroup()->getDefaultStoreId();

                        /** @var OnePica_AvaTax_Helper_Config $avaConfig */
                        $avaConfig = Mage::helper('avatax/config');
                        /** @var OnePica_AvaTaxAr2_Helper_Config $ar2Config */
                        $ar2Config = Mage::helper('avataxar2/config');

                        $customerCodeFormat = (bool)$config->getData('groups/avatax/fields/cust_code_format/inherit')
                                                ? $avaConfig->getCustomerCodeFormat($idDefaultStoreId)
                                                : $config->getData('groups/avatax/fields/cust_code_format/value');
                        $isCertEnabled =  (bool) $config->getData('groups/avatax_document_management/fields/action/inherit')
                                                ? $ar2Config->getStatusServiceAction($idDefaultStoreId)
                                                : (bool)$config->getData('groups/avatax_document_management/fields/action/value');
                        if ($isCertEnabled && $customerCodeFormat != OnePica_AvaTax_Model_Source_Customercodeformat::CUST_ATTRIBUTE) {
                            throw new \OnePica_AvaTaxAr2_Exception(
                                Mage::helper('avataxar2')->__(
                                    "You have to set 'Data Mapping / Customer Code Format' to 'customer attribute' to be able to use 'AvaTax Document Management' feature."
                                ));
                        }
                    }
                    break;
            }
        }

        return $this;
    }
}
