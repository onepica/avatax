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
 * Class OnePica_AvaTaxAr2_Block_Popup_Form
 */
class OnePica_AvaTaxAr2_Block_Popup_Form extends Mage_Core_Block_Template
{
    use OnePica_AvaTaxAr2_Block_Secure_Url;

    /**
     * @return string
     */
    public function getTokenUrl()
    {
        return $this->getUrl('avataxcert/popup/getToken');
    }

    /**
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->getUrl('avataxcert/popup/certCreateAfter');
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
            $country = Mage::getModel('directory/country')->loadByCode('US');
            $result = array();
            foreach ($country->getRegions() as $region) {
                $item = $region->toArray(array('region_id', 'code', 'name'));
                $item['name'] = isset($item['name']) ? $item['name'] : $region->getDefaultName();
                $result[] = $item;
            }

            return $result ? $result : array();
        } catch (Exception $exception) {
            return array();
        }
    }

    /**
     * @return string|null
     * @throws \Exception
     */
    public function getSelectedRegion()
    {
        if ($this->getRequest()->getParam('skipQuote')) {
            return null;
        }

        $address = $this->_getCheckoutSession()->getQuote()->getShippingAddress();

        return $address ? $address->getRegion() : null;
    }

    /**
     * @return \Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }
}
