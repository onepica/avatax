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

/** @var $this Mage_Core_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();

$attributeCode = 'avatax_normalization_flag';
$label = 'Avatax Normalization Flag';

$ver = Mage::getVersionInfo();

if ($ver['minor'] < 6 || $ver['minor'] == 10) {
    $adapter = $installer->getConnection();
    $installer->run(
        "ALTER TABLE `" . $this->getTable('sales_flat_quote') . "`
    ADD COLUMN `$attributeCode` tinyint(1) UNSIGNED NULL COMMENT '$label';"
    );
} else {
    $setup = Mage::getModel('sales/resource_setup', 'core_setup');

    $setup->addAttribute(
        'quote', $attributeCode, array(
            'label'            => $label,
            'type'             => 'int',
            'input'            => 'text',
            'visible'          => 0,
            'required'         => 0,
            'user_defined'     => 0,
            'visible_on_front' => 0,
            'position'         => 200,
            'sort_order'       => 200)
    );
}


$installer->endSetup();
