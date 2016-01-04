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
 * Class OnePica_AvaTax16_Document_Response_ProcessingInfo
 */
class OnePica_AvaTax16_Document_Response_ProcessingInfo extends OnePica_AvaTax16_Document_Part
{
    /**
     * Transaction State
     *
     * @var string
     */
    protected $_transactionState;

    /**
     * Version Id
     *
     * @var string
     */
    protected $_versionId;

    /**
     * Format Id
     *
     * @var string
     */
    protected $_formatId;

    /**
     * Duration
     *
     * @var float
     */
    protected $_duration;

    /**
     * Modified Date
     *
     * @var string
     */
    protected $_modifiedDate;

    /**
     * Batch Id
     *
     * @var string
     */
    protected $_batchId;

    /**
     * Document Id
     *
     * @var string
     */
    protected $_documentId;

    /**
     * Message
     *
     * @var string
     */
    protected $_message;
}
