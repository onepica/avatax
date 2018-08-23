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

/** @var \Mage_Catalog_Model_Resource_Eav_Mysql4_Setup $installer */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$attributeCode = OnePica_AvaTax_Helper_LandedCost::AVATAX_PRODUCT_LANDED_COST_ATTR_PARAMETER;

/** @var $attribute \Mage_Eav_Model_Entity_Attribute $attribute */
$attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);

if (!$attribute->getId()) {
    $installer->addAttribute(
        'catalog_product', $attributeCode, array(
            'type'             => 'varchar',
            'label'            => OnePica_AvaTax_Helper_LandedCost::AVATAX_PRODUCT_LANDED_COST_ATTR_PARAMETER_LABEL,
            'input'            => 'text',
            'backend'          => 'avatax/catalog_product_attribute_backend_parameter',
            'visible'          => 1,
            'required'         => 0,
            'user_defined'     => 1,
            'default'          => '',
            'searchable'       => 0,
            'filterable'       => 0,
            'comparable'       => 0,
            'visible_on_front' => 0,
            'unique'           => 0,
            'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'apply_to'         => 'simple,configurable,bundle',
            'is_configurable'  => 0,
            'group'            => OnePica_AvaTax_Helper_LandedCost::AVATAX_PRODUCT_GROUP_LANDED_COST,
            'position'         => 6,
            'sort_order'       => 6,
        )
    );
}

$installer->endSetup();
