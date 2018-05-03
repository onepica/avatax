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
$this->startSetup();

$table = $this->getTable('avatax_records/unit_of_measurement');

if (!$installer->getConnection()->isTableExists($table)) {
    $installer->run(
        "
CREATE TABLE `" . $this->getTable('avatax_records/unit_of_measurement') . "` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `avalara_code` varchar(100) DEFAULT NULL,
    `description` text,
    `country_list` text,
    PRIMARY KEY (`id`),
    KEY `IDX_OP_AVATAX_UNIT_OF_MEASUREMENT_AVALARA_CODE` (`avalara_code`)
) ENGINE=InnoDB COMMENT='Used by One Pica AvaTax extension';
    "
    );
}

$this->endSetup();
