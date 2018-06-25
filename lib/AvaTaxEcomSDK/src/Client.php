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

namespace Avalara\AvaTaxEcomSDK;

/**
 * Class Client
 *
 * @package Avalara\AvaTaxEcomSDK
 */
class Client extends ClientBase
{
    /**
     * Generate bearer token by given credentials
     *
     * @param null|string|int $customerNumber
     * @return \stdClass|string
     * @throws \Exception
     */
    public function getToken($customerNumber = null)
    {
        $path = "/v2/auth/get-token";
        $params = [
            'query' => [],
            'body'  => null
        ];

        if ($customerNumber != null) {
            $params['headers'] = array('x-customer-number' => $customerNumber);
        }

        $response = $this->restCall($path, 'POST', $params);

        if (is_string($response)) {
            throw new \Exception($response);
        }

        if (is_object($response) && isset($response->error)) {
            throw new \Exception($response->error);
        }

        if (!isset($response->response)) {
            throw new \Exception('Response is empty');
        }

        if (!isset($response->response->token)) {
            throw new \Exception('Token is not set');
        }

        return $response->response;
    }
}
