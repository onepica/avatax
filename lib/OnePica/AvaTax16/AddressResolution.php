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
 * Class OnePica_AvaTax16_AddressResolution
 */
class OnePica_AvaTax16_AddressResolution extends OnePica_AvaTax16_ResourceAbstract
{
    /**
     * Url path for address resolution
     */
    const ADDRESS_RESOLUTION_URL_PATH = '/address';

    /**
     * Resolve a Single Address
     *
     * @param OnePica_AvaTax16_Document_Part_Location_Address $address
     * @return OnePica_AvaTax16_AddressResolution_ResolveSingleAddressResponse $result
     */
    public function resolveSingleAddress($address)
    {
        $config = $this->getConfig();
        $postUrl = $config->getBaseUrl()
            . self::ADDRESS_RESOLUTION_URL_PATH
            . '/account/'
            . $config->getAccountId()
            . '/company/'
            . $config->getCompanyCode()
            . '/resolve';

        $postData = $address->toArray();
        $requestOptions = array(
            'requestType' => 'POST',
            'data'        => $postData,
            'returnClass' => 'OnePica_AvaTax16_AddressResolution_ResolveSingleAddressResponse'
        );
        $result = $this->_sendRequest($postUrl, $requestOptions);
        return $result;
    }
}
