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
class OnePica_AvaTax_Model_Observer_AdminSystemConfigSaveBefore extends OnePica_AvaTax_Model_Observer_Abstract
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
                        $website = Mage::app()->getWebsite(true);
                        $currentStoreCode = $config->getStore();

                        /** @var OnePica_AvaTax_Helper_LandedCost $lcConfig */
                        $lcConfig = Mage::helper('avatax/landedCost');

                        $isPriceIncludeTaxSaveValue = $config->getData('groups/calculation/fields/price_includes_tax/value');
                        $isPriceIncludeTax = isset($isPriceIncludeTaxSaveValue)
                                            ? (bool)$isPriceIncludeTaxSaveValue
                                            : $website->getConfig('tax/calculation/price_includes_tax');


                        $storeLandedCostEnabled = null;
                        $isLandedCostEnabledSaveValue = $config->getData('groups/avatax_landed_cost/fields/landed_cost_enabled/value');
                        $isLandedCostEnabled = (isset($isLandedCostEnabledSaveValue)) ? (bool)$isLandedCostEnabledSaveValue : false;
                        if (!$isLandedCostEnabled) {
                            $storesToCheck = $website->getStoreCodes();
                            if (($key = array_search($currentStoreCode, $storesToCheck)) !== false) {
                                unset($storesToCheck[$key]);
                            }
                            foreach ($storesToCheck as $code) {
                                $isLandedCostEnabled = $isLandedCostEnabled | (bool)$lcConfig->isLandedCostEnabled($code);
                                if ($isLandedCostEnabled) {
                                    $storeLandedCostEnabled = $code;
                                    break;
                                }
                            }
                        }



                        if ($isLandedCostEnabled && $isPriceIncludeTax) {
                            throw new \OnePica_AvaTax_Exception(
                                Mage::helper('avatax')->__(
                                    'You can only use \'Excluding Tax\' calculation model for \'Catalog Prices\' when \'Customs Duty\' feature is enabled. %s',
                                    (($storeLandedCostEnabled) ? 'Store code : ' . $storeLandedCostEnabled . '.' : '')
                                ));
                        }
                    }
                    break;
            }
        }

        return $this;
    }
}
