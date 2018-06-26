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
 * Document resource model
 *
 * @method int getId()
 * @method int getCompanyId()
 * @method string getSignedDate()
 * @method string getExpirationDate()
 * @method string getFilename()
 * @method bool getValid()
 * @method bool getVerified()
 * @method int getExemptPercentage()
 * @method bool getIsSingleCertificate()
 * @method array|\stdClass getExemptionReason()
 * @method string getCreatedDate()
 * @method string getModifiedDate()
 * @method int getPageCount()
 * @method array|\stdClass getExposureZone()
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Model_Records_RestV2_Document extends Varien_Object
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {

    }
}
