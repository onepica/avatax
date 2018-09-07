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

/**
 * Document model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Model_Records_Document extends Mage_Core_Model_Abstract
{
    const STATUS_DISABLED = 0;

    const STATUS_ACTIVE = 1;

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('avataxar2_records/document');
    }

    /**
     * @return \OnePica_AvaTaxAr2_Model_Records_RestV2_Document_Collection
     * @throws \Exception
     */
    public function getCollection()
    {
        /** @var \OnePica_AvaTaxAr2_Model_Records_RestV2_Document_Collection $collection */
        $collection = Mage::getModel('avataxar2/records_restV2_document_collection');

        return $collection;
    }
}
