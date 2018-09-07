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
class OnePica_AvaTax_Model_Landedcost_Shipping_Method extends Varien_Object
{
    protected $_data;

    /**
     * Constructor function
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        parent::__construct();

        $shippingCode = $args['id'];
        if (!$shippingCode) {
            return false;
        }
        $this->setId($shippingCode);
        $this->setTitle(Mage::getStoreConfig('carriers/' . $shippingCode . '/title'));
        $this->_setCarrierMethods($shippingCode, $args['model']);
    }

    /**
     * Prepare and set сarrier methods to use in multiselect
     *
     * @param   string                               $shippingCode
     * @param   Mage_Shipping_Model_Carrier_Abstract $shippingModel
     * @return  $this
     */
    protected function _setCarrierMethods($shippingCode, $shippingModel)
    {
        $this->setCarrierMethods(null);

        try {
            switch ($shippingCode) {
                case 'dhl':
                    $shippingModelMethods = $shippingModel->getCode('service');
                    break;
                case 'dhlint':
                    $shippingModelMethods = Mage::getModel('usa/shipping_carrier_dhl_international')
                                                ->getDhlProducts('D');
                    break;
                default:
                    $shippingModelMethods = $shippingModel->getCode('method');
                    break;
            }

            if (is_array($shippingModelMethods)) {
                array_walk(
                    $shippingModelMethods,
                    function (&$val, $key) {
                        $val = array('value' => $key, 'label' => $val);
                    }
                );

                $this->setCarrierMethods($shippingModelMethods);
            }
        } catch (Exception $e) {
            $this->setCarrierMethods(null);
        }

        return $this;
    }

    /**
     * Set object title field value
     *
     * @param   string $value
     * @return  $this
     */
    public function setTitle($value)
    {
        if ($this->getIdFieldName()) {
            $this->setData($this->getIdFieldName(), $value);
        } else {
            $this->setData('title', $value);
        }

        return $this;
    }

    /**
     * Set object сarrierMethods field value
     *
     * @param  array|string $value
     * @return $this
     */
    public function setCarrierMethods($value)
    {
        if ($this->getIdFieldName()) {
            $this->setData($this->getIdFieldName(), $value);
        } else {
            $this->setData('сarrierMethods', $value);
        }

        return $this;
    }

    /**
     * Retrieve object title
     *
     * @return mixed
     */
    public function getTitle()
    {
        if ($this->getIdFieldName()) {
            return $this->_getData($this->getIdFieldName());
        }

        return $this->_getData('title');
    }

    /**
     * Retrieve object carrier methods
     */
    public function getCarrierMethods()
    {
        if ($this->getIdFieldName()) {
            return $this->_getData($this->getIdFieldName());
        }

        return $this->_getData('сarrierMethods');
    }
}
