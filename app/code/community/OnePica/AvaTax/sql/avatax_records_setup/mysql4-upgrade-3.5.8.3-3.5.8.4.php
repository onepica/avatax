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

/** @var  \Mage_Catalog_Model_Resource_Eav_Mysql4_Setup $installer */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$attributeCode = OnePica_AvaTax_Helper_LandedCost::AVATAX_CUSTOMER_LANDED_COST_ATTR_SELLER_IS_AN_IMPORTER;

/** @var \Mage_Eav_Model_Entity_Attribute $attribute */
$attribute = Mage::getModel('eav/entity_attribute')->loadByCode('customer', $attributeCode);

if (!$attribute->getId()) {
    $installer->addAttribute(
        'customer', $attributeCode, array(
            'input'            => 'select',
            'type'             => 'int',
            'label'            => 'Seller is an importer',
            'default'          => null,
            'backend'          => '',
            'source'           => 'avatax/entity_attribute_source_boolean',
            'visible'          => true,
            'required'         => false,
            'visible_on_front' => false,
            'position'         => 210,
            'sort_order'       => 210,
        )
    );

    // update options for attribute
    $attribute = Mage::getModel('eav/config')->getAttribute('customer', $attributeCode);
    $attribute->setData('is_system', 0);
    $attribute->setData('is_visible', 0);
    $attribute->setData('sort_order', 210);
    $attribute->setData('used_in_forms', array('adminhtml_customer'));
    $attribute->save();
}

$installer->endSetup();
