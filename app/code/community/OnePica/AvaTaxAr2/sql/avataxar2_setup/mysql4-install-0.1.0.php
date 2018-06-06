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
 * @copyright  Copyright (c) 2009 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
/** @var  \Mage_Catalog_Model_Resource_Eav_Mysql4_Setup $installer */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$attributeCode = OnePica_AvaTaxAr2_Helper_Data::AVATAX_CUSTOMER_EXEMPTION_NUMBER;

/** @var \Mage_Eav_Model_Entity_Attribute $attribute */
$attribute = Mage::getModel('eav/entity_attribute')->loadByCode('customer', $attributeCode);

if (!$attribute->getId()) {
    $installer->addAttribute(
        'customer', $attributeCode, array(
            'input'            => 'text',
            'type'             => 'text',
            'label'            => Mage::helper('avataxar2')->__('Exemption number'),
            'default'          => null,
            'backend'          => '',
            'visible'          => true,
            'required'         => false,
            'visible_on_front' => true,
            'position'         => 10,
        )
    );

    // update options for attribute
    $attribute = Mage::getModel('eav/config')->getAttribute('customer', $attributeCode);
    $attribute->setData('used_in_forms', array(OnePica_AvaTaxAr2_Helper_Data::AVATAX_CUSTOMER_EXEMPTION_FORM_CODE));
    $attribute->setData('sort_order', 10);
    $attribute->save();
}

$installer->endSetup();
