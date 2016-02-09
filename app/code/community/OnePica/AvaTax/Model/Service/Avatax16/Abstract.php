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

/**
 * Avatax 16 service abstract model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
abstract class OnePica_AvaTax_Model_Service_Avatax16_Abstract extends OnePica_AvaTax_Model_Service_Abstract_Tools
{
    /**
     * Transaction type sale
     */
    const TRANSACTION_TYPE_SALE = 'Sale';

    /**
     * Tax location purpose ship from
     */
    const TAX_LOCATION_PURPOSE_SHIP_FROM = 'ShipFrom';

    /**
     * Tax location purpose ship to
     */
    const TAX_LOCATION_PURPOSE_SHIP_TO = 'ShipTo';

    /**
     * Default GW items sku
     */
    const DEFAULT_GW_ITEMS_SKU = 'GwItemsAmount';

    /**
     * Default GW items description
     */
    const DEFAULT_GW_ITEMS_DESCRIPTION = 'Gift Wrap Items Amount';

    /**
     * Default shipping items sku
     */
    const DEFAULT_SHIPPING_ITEMS_SKU = 'Shipping';

    /**
     * Default shipping items description
     */
    const DEFAULT_SHIPPING_ITEMS_DESCRIPTION = 'Shipping costs';

    /**
     * Default GW order sku
     */
    const DEFAULT_GW_ORDER_SKU = 'GwOrderAmount';

    /**
     * Default GW order description
     */
    const DEFAULT_GW_ORDER_DESCRIPTION = 'Gift Wrap Order Amount';

    /**
     * Default GW printed card sku
     */
    const DEFAULT_GW_PRINTED_CARD_SKU = 'GwPrintedCardAmount';

    /**
     * Default GW printed card description
     */
    const DEFAULT_GW_PRINTED_CARD_DESCRIPTION = 'Gift Wrap Printed Card Amount';

    /**
     * Response result code exception
     */
    const RESPONSE_RESULT_CODE_EXCEPTION = 'Exception';

    /**
     * Default vendor code
     */
    const DEFAULT_VENDOR_CODE = 'Vendor';

    /**
     * Service date format
     */
    const SERVICE_DATE_FORMAT = 'Y-MM-dd';

    /**
     * Default positive adjustment code
     */
    const DEFAULT_POSITIVE_ADJUSTMENT_CODE = 'positive-adjustment';

    /**
     * Default positive adjustment description
     */
    const DEFAULT_POSITIVE_ADJUSTMENT_DESCRIPTION = 'Adjustment refund';

    /**
     * Default negative adjustment code
     */
    const DEFAULT_NEGATIVE_ADJUSTMENT_CODE = 'negative-adjustment';

    /**
     * Default negative adjustment description
     */
    const DEFAULT_NEGATIVE_ADJUSTMENT_DESCRIPTION = 'Adjustment fee';

    /**
     * Document code invoice prefix
     */
    const DOCUMENT_CODE_INVOICE_PREFIX = 'I';

    /**
     * Document code creditmemo prefix
     */
    const DOCUMENT_CODE_CREDITMEMO_PREFIX = 'C';

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('avatax');
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Address
     */
    protected function _getAddressHelper()
    {
        return Mage::helper('avatax/address');
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avatax/config');
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getErrorsHelper()
    {
        return Mage::helper('avatax/errors');
    }

    /**
     * Get gift wrapping data helper
     *
     * @return \Enterprise_GiftWrapping_Helper_Data
     */
    protected function _getGiftWrappingDataHelper()
    {
        return Mage::helper('enterprise_giftwrapping');
    }

    /**
     * Logs a debug message
     *
     * @param string $type
     * @param mixed $request the request string
     * @param mixed $result the result string
     * @param int $storeId id of the store the call is make for
     * @param mixed $additional any other info
     * @return $this
     */
    protected function _log($type, $request, $result, $storeId = null, $additional = null)
    {
        if ($result->getHasError() === false) {
            switch ($this->_getHelper()->getLogMode($storeId)) {
                case OnePica_AvaTax_Model_Source_Logmode::ERRORS:
                    return $this;
                    break;
                case OnePica_AvaTax_Model_Source_Logmode::NORMAL:
                    $additional = null;
                    break;
            }
        }
        $level = $result->getHasError() ? OnePica_AvaTax_Model_Records_Log::LOG_LEVEL_ERROR
                                        : OnePica_AvaTax_Model_Records_Log::LOG_LEVEL_SUCCESS;

        $requestLog = ($request instanceof OnePica_AvaTax16_Document_Part) ? $request->toArray() : $request;
        $resultLog = ($result instanceof OnePica_AvaTax16_Document_Part) ? $result->toArray() : $result;

        if (in_array($type, $this->_getHelper()->getLogType($storeId))) {
            Mage::getModel('avatax_records/log')
                ->setStoreId($storeId)
                ->setLevel($level)
                ->setType($type)
                ->setRequest(print_r($requestLog, true))
                ->setResult(print_r($resultLog, true))
                ->setAdditional(print_r($additional, true))
                ->save();
        }
        return $this;
    }

    /**
     * Get date model
     *
     * @return Mage_Core_Model_Date
     */
    protected function _getDateModel()
    {
        return Mage::getSingleton('core/date');
    }

    /**
     * Get New Document Part Location Address Object
     *
     * @return OnePica_AvaTax16_Document_Part_Location_Address
     */
    protected function _getNewDocumentPartLocationAddressObject()
    {
        return new OnePica_AvaTax16_Document_Part_Location_Address();
    }

    /**
     * Get New Document Part Location Object
     *
     * @return OnePica_AvaTax16_Document_Part_Location
     */
    protected function _getNewDocumentPartLocationObject()
    {
        return new OnePica_AvaTax16_Document_Part_Location();
    }

    /**
     * Get New Document Request Header Object
     *
     * @return OnePica_AvaTax16_Document_Request_Header
     */
    protected function _getNewDocumentRequestHeaderObject()
    {
        return new OnePica_AvaTax16_Document_Request_Header();
    }

    /**
     * Get New Document Request Object
     *
     * @return OnePica_AvaTax16_Document_Request
     */
    protected function _getNewDocumentRequestObject()
    {
        return new OnePica_AvaTax16_Document_Request();
    }

    /**
     * Get New Document Request Line Object
     *
     * @return OnePica_AvaTax16_Document_Request_Line
     */
    protected function _getNewDocumentRequestLineObject()
    {
        return new OnePica_AvaTax16_Document_Request_Line();
    }

    /**
     * Get New Service Message Object
     *
     * @return Varien_Object
     */
    protected function _getNewServiceMessageObject()
    {
        return new Varien_Object();
    }
}
