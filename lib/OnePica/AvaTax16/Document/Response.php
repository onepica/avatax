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
 * Class OnePica_AvaTax16_Document_Response
 */
class OnePica_AvaTax16_Document_Response extends OnePica_AvaTax16_Document_Part
{
    /**
     * Has error
     *
     * @var bool
     */
    protected $_hasError = false;

    /**
     * Errors
     *
     * @var array
     */
    protected $_errors;

    /**
     * Types of complex properties
     *
     * @var array
     */
    protected $_propertyComplexTypes = array(
        '_header' => array(
            'type' => 'OnePica_AvaTax16_Document_Response_Header'
        ),
        '_lines' => array(
            'type' => 'OnePica_AvaTax16_Document_Response_Line',
            'isArrayOf' => 'true'
        ),
        '_calculatedTaxSummary' => array(
            'type' => 'OnePica_AvaTax16_Document_Response_CalculatedTaxSummary'
        ),
        '_feedback' => array(
            'type' => 'OnePica_AvaTax16_Document_Part_Feedback'
        ),
        '_processingInfo' => array(
            'type' => 'OnePica_AvaTax16_Document_Response_ProcessingInfo'
        ),
    );

    /**
     * Header
     *
     * @var OnePica_AvaTax16_Document_Response_Header
     */
    protected $_header;

    /**
     * Lines
     *
     * @var OnePica_AvaTax16_Document_Response_Line[]
     */
    protected $_lines;

    /**
     * Calculated Tax Summary
     *
     * @var OnePica_AvaTax16_Document_Response_CalculatedTaxSummary
     */
    protected $_calculatedTaxSummary;

    /**
     * Feedback
     *
     * @var OnePica_AvaTax16_Document_Part_Feedback
     */
    protected $_feedback;

    /**
     * Processing Info
     *
     * @var OnePica_AvaTax16_Document_Response_ProcessingInfo
     */
    protected $_processingInfo;
}
