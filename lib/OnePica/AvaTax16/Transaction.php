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
 * Class OnePica_AvaTax16_Transaction
 */
class OnePica_AvaTax16_Transaction extends OnePica_AvaTax16_ResourceAbstract
{
    /**
     * Create Transaction
     *
     * @param OnePica_AvaTax16_Document_Request $documentRequest
     * @return StdClass $data
     */
    public function createTransaction($documentRequest)
    {
        $postUrl = $this->_config->getBaseUrl() . self::TRANSACTION_URL_PATH;
        $postData = $documentRequest->toArray();
        $curl = $this->_getCurlObjectWithHeaders();
        $curl->post($postUrl, $postData);
        $data = $curl->response;
        return $data;
    }

    /**
     * Create Transaction from Calculation
     *
     * @param string $transactionType
     * @param string $documentCode
     * @param bool $recalculate
     * @param string $comment
     * @return StdClass $data
     */
    public function createTransactionFromCalculation($transactionType, $documentCode, $recalculate = null,
        $comment = null)
    {
        $config = $this->getConfig();
        $postUrl = $config->getBaseUrl()
                 . self::CALCULATION_URL_PATH
                 . '/account/'
                 . $config->getAccountId()
                 . '/company/'
                 . $config->getCompanyCode()
                 . '/'
                 . $transactionType
                 . '/'
                 . $documentCode
                 . self::TRANSACTION_URL_PATH;

        $postData = array(
            'recalculate' => $recalculate,
            'documentCode' => $documentCode,
            'comment' => $comment
        );

        $curl = $this->_getCurlObjectWithHeaders();
        $curl->post($postUrl, $postData);
        $data = $curl->response;
        return $data;
    }

    /**
     * Get Transaction
     *
     * @param string $transactionType
     * @param string $documentCode
     * @return StdClass $data
     */
    public function getTransaction($transactionType, $documentCode)
    {
        $config = $this->getConfig();
        $getUrl = $config->getBaseUrl()
                . self::TRANSACTION_URL_PATH
                . '/account/'
                . $config->getAccountId()
                . '/company/'
                . $config->getCompanyCode()
                . '/'
                . $transactionType
                . '/'
                . $documentCode;

        $curl = $this->_getCurlObjectWithHeaders();
        $curl->get($getUrl);
        $data = $curl->response;
        return $data;
    }

    /**
     * Get List Of Transactions
     *
     * @param string $transactionType
     * @param int $limit
     * @param string $startDate
     * @param string $endDate
     * @param string $startCode (not implemented)
     * @return StdClass|array $result
     */
    public function getListOfTransactions($transactionType, $limit = null, $startDate = null, $endDate = null,
        $startCode = null)
    {
        $config = $this->getConfig();
        $getUrl = $config->getBaseUrl()
                . self::TRANSACTION_URL_PATH
                . '/account/'
                . $config->getAccountId()
                . '/company/'
                . $config->getCompanyCode()
                . '/'
                . $transactionType;

        $filterData = array(
            'limit' => $limit,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'startCode' => $startCode,
        );

        $curl = $this->_getCurlObjectWithHeaders();
        $curl->get($getUrl, $filterData);
        $data = $curl->response;

        $result = null;
        if (is_array($data)) {
            foreach ($data as $dataItem) {
                $transactionListItem = new OnePica_AvaTax16_Transaction_ListItemResponse();
                $result[] = $transactionListItem->fillData($dataItem);
            }
        }
        return $result;
    }

    /**
     * Get Transaction Input
     *
     * @param string $transactionType
     * @param string $documentCode
     * @return StdClass|array $result
     */
    public function getTransactionInput($transactionType, $documentCode)
    {
        $config = $this->getConfig();
        $getUrl = $config->getBaseUrl()
                . self::TRANSACTION_URL_PATH
                . '/account/'
                . $config->getAccountId()
                . '/company/'
                . $config->getCompanyCode()
                . '/'
                . $transactionType
                . '/'
                . $documentCode
                . '/source';

        $curl = $this->_getCurlObjectWithHeaders();
        $curl->get($getUrl);
        $data = $curl->response;
        return $data;
    }

    /**
     * Transition Transaction State
     *
     * @param string $transactionType
     * @param string $documentCode
     * @param string $type
     * @param string $comment
     * @return bool
     * @todo analise and refactor return value
     */
    public function transitionTransactionState($transactionType, $documentCode, $type, $comment)
    {
        $config = $this->getConfig();
        $postUrl = $config->getBaseUrl()
            . self::TRANSACTION_URL_PATH
            . '/account/'
            . $config->getAccountId()
            . '/company/'
            . $config->getCompanyCode()
            . '/'
            . $transactionType
            . '/'
            . $documentCode
            . '/stateTransitions';

        $postData = array(
            'type' => $type,
            'comment' => $comment
        );

        $curl = $this->_getCurlObjectWithHeaders();
        $curl->post($postUrl, $postData);

        return ($curl->httpStatusCode == 201) ? true : false;
    }
}
