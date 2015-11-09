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
$installer = $this;
$installer->startSetup();
try {
    $installer->run("

	ALTER TABLE `" . $this->getTable('tax/tax_class') . "`
		ADD `op_avatax_code` VARCHAR(255) DEFAULT '' NOT NULL COMMENT 'Used by One Pica AvaTax extension'
		AFTER `class_name`;

	");
} catch (Exception $e) {
    Mage::logException($e);
}
$installer->endSetup();
