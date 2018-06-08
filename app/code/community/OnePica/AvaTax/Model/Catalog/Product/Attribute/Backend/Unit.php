<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition End User License Agreement
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magento.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license     http://www.magento.com/license/enterprise-edition
 */

/**
 * Catalog product price attribute backend model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 */
class OnePica_AvaTax_Model_Catalog_Product_Attribute_Backend_Unit
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Set Attribute instance
     * Rewrite for redefine attribute scope
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return \OnePica_AvaTax_Model_Catalog_Product_Attribute_Backend_Unit
     */
    public function setAttribute($attribute)
    {
        parent::setAttribute($attribute);

        return $this;
    }

    /**
     * After load method
     *
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function afterLoad($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($object->hasData($attrCode)) {
            $result = $this->decodeUnitOfMeasurement($object->getData($attrCode));
            $object->setData($attrCode, $result);
        }

        return $this;
    }

    /**
     * Decode Unit Of Measurement Product Configuration
     *
     * @param string $jsonData
     * @return array
     */
    public function decodeUnitOfMeasurement($jsonData)
    {
        $config = json_decode($jsonData);
        $result = array();
        if (!empty($config)) {
            foreach ($config as $c) {
                $ac = (array)$c;
                $ac['unit']=(float)$ac['unit'];
                array_push($result, $ac);
            }
        }

        return $result;
    }

    /**
     * Before save method
     *
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($object->hasData($attrCode)) {
            /** @var OnePica_AvaTax_Helper_LandedCost $helper */
            $helper = Mage::helper('avatax/landedCost');

            $origin = $object->getData($attrCode);
            $data = array();
            foreach ($origin as $index => $item) {
                if (empty($item['delete']) || $item['delete'] == 0) {
                    $item['unit'] = round((float)$item['unit'], $helper->getUnitPrecision());
                    array_push($data, $item);
                }
            }

            $this->_validateOnCountriesIntersection($data);

            $config = json_encode($data);
            $object->setData($attrCode, $config);
        } else {
            if (!$object->hasData($attrCode) && $this->getDefaultValue()) {
                $object->setData($attrCode, $this->getDefaultValue());
            }
        }

        return $this;
    }

    /**
     * Validate units on countries intersection
     *
     * @param $data
     * @return $this
     * @throws Exception
     */
    protected function _validateOnCountriesIntersection($data)
    {
        $ids = array();
        array_walk($data, function ($value, $key) use (&$ids) {
            array_push($ids, $value['unit_of_measurement']);
        });

        $collection = Mage::getModel('avatax_records/unitOfMeasurement')
            ->getCollection()
            ->addFieldToFilter('id', array('in' => $ids));

        $intersectUnitsByCountries = array();
        /** @var OnePica_AvaTax_Model_Records_UnitOfMeasurement $item */
        foreach ($collection as $item) {
            $itemCountries = explode(',', $item->getCountryList());
            /** @var OnePica_AvaTax_Model_Records_UnitOfMeasurement $j */
            foreach ($collection as $j) {
                if ($j->getId() == $item->getId()) {
                    continue;
                }

                $jCountries = explode(',', $j->getCountryList());
                $ints = array_intersect($itemCountries, $jCountries);
                if (count($ints) > 0) {
                    if (!in_array($item->getDescription(), $intersectUnitsByCountries)) {
                        array_push($intersectUnitsByCountries, $item->getDescription());
                    }

                    if (!in_array($j->getDescription(), $intersectUnitsByCountries)) {
                        array_push($intersectUnitsByCountries, $j->getDescription());
                    }
                }
            }
        }

        if (count($intersectUnitsByCountries) > 0) {
            $message = Mage::helper('avatax')
                ->__('%s have been configured to use for the same destination countries. You have to choose only one accurate unit to use between them for current product.',
                    implode(', ', $intersectUnitsByCountries));

            throw new \Exception($message);
        }

        return $this;
    }
}
