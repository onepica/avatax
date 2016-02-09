<?php
/**
 * OnePica
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to codemaster@onepica.com so we can send you a copy immediately.
 *
 * @category  OnePica
 * @package   OnePica_AvaTax
 * @copyright Copyright (c) 2015 One Pica, Inc. (http://www.onepica.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class OnePica_AvaTax16_Calculation_ListItemResponse_Header
 */
class OnePica_AvaTax16_Calculation_ListItemResponse_Header extends OnePica_AvaTax16_Document_Part
{
    /**
     * Account Id
     *
     * @var string
     */
    protected $_accountId;

    /**
     * Company Code
     *
     * @var string
     */
    protected $_companyCode;

    /**
     * Transaction Type
     *
     * @var string
     */
    protected $_transactionType;

    /**
     * Document Code
     *
     * @var string
     */
    protected $_documentCode;

    /**
     * Customer Code
     *
     * @var string
     */
    protected $_customerCode;

    /**
     * Transaction Date
     *
     * @var string
     */
    protected $_transactionDate;

    /**
     * Currency
     *
     * @var string
     */
    protected $_currency;

    /**
     * Total Tax Override Amount
     *
     * @var string
     */
    protected $_totalTaxOverrideAmount;
}
