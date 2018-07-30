<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
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
 * Class OnePica_AvaTaxAr2_Block_Adminhtml_Popup_Form
 */
class OnePica_AvaTaxAr2_Block_Adminhtml_Popup_Form extends Mage_Adminhtml_Block_Template
{
    /**
     * @return string
     */
    public function getTokenUrl()
    {
        return $this->getUrl('adminhtml/avaTaxAr2_popup/getToken');
    }

    /**
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->getUrl('adminhtml/avaTaxAr2_popup/certCreateAfter');
    }

    /**
     * @return string|int|null
     */
    public function getCustomerNumber()
    {
        try {
            return $this->getRequest()->getParam('customerNumber');
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return string|int|null
     */
    public function getCustomerId()
    {
        try {
            return $this->getRequest()->getParam('customerId');
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return array
     */
    public function getRegions()
    {
        try {
            /** @var \Mage_Directory_Model_Region_Api $regionApiModel */
            $regionApiModel = Mage::getModel('directory/region_api');
            $regionApiItems = $regionApiModel->items('US');

            return $regionApiItems ? $regionApiItems : array();
        } catch (Exception $exception) {
            return array();
        }
    }
}
