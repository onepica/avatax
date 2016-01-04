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
 * Class OnePica_AvaTax16_IO_Curl
 */
class OnePica_AvaTax16_IO_Curl
{
    /**
     * Version
     */
    const VERSION = '1.0.0';

    /**
     * Default timeout
     */
    const DEFAULT_TIMEOUT = 30;

    /**
     * cUrl resource.
     *
     * @var resource
     */
    protected $_curl;

    /**
     * Id
     *
     * @var string
     */
    protected $_id = null;

    /**
     * Error
     *
     * @var bool
     */
    protected $_error = false;

    /**
     * Error code
     *
     * @var int
     */
    protected $_errorCode = 0;

    /**
     * Error message
     *
     * @var string
     */
    protected $_errorMessage = null;

    /**
     * Curl error
     *
     * @var bool
     */
    protected $_curlError = false;

    /**
     * Curl error code
     *
     * @var int
     */
    protected $_curlErrorCode = 0;

    /**
     * Curl error message
     *
     * @var string
     */
    protected $_curlErrorMessage = null;

    /**
     * Http error
     *
     * @var bool
     */
    protected $_httpError = false;

    /**
     * Http status code
     *
     * @var int
     */
    protected $_httpStatusCode = 0;

    /**
     * Http error message
     *
     * @var string
     */
    protected $_httpErrorMessage = null;

    /**
     * Base url
     *
     * @var string
     */
    protected $_baseUrl = null;

    /**
     * Url
     *
     * @var string
     */
    protected $_url = null;

    /**
     * Request headers
     *
     * @var array
     */
    protected $_requestHeaders = null;

    /**
     * Response headers
     *
     * @var array
     */
    protected $_responseHeaders = null;

    /**
     * Raw response headers
     *
     * @var string
     */
    protected $_rawResponseHeaders = '';

    /**
     * Response
     *
     * @var mixed
     */
    protected $_response = null;

    /**
     * Raw response
     *
     * @var string
     */
    protected $_rawResponse = null;

    /**
     * Before send function
     *
     * @var string
     */
    protected $_beforeSendFunction = null;

    /**
     * Download complete function
     *
     * @var string
     */
    protected $_downloadCompleteFunction = null;

    /**
     * Success function
     *
     * @var string
     */
    protected $_successFunction = null;

    /**
     * Error function
     *
     * @var string
     */
    protected $_errorFunction = null;

    /**
     * Complete function
     *
     * @var string
     */
    protected $_completeFunction = null;

    /**
     * Cookies
     *
     * @var array
     */
    protected $_cookies = array();

    /**
     * Response cookies
     *
     * @var array
     */
    protected $_responseCookies = array();

    /**
     * Headers
     *
     * @var array
     */
    protected $_headers = array();

    /**
     * Options
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Options
     *
     * @var function
     */
    protected $_jsonDecoder = null;

    /**
     * Json pattern
     *
     * @var string
     */
    protected $_jsonPattern = '/^(?:application|text)\/(?:[a-z]+(?:[\.-][0-9a-z]+){0,}[\+\.]|x-)?json(?:-[a-z]+)?/i';

    /**
     * XML pattern
     *
     * @var string
     */
    protected $_xmlPattern = '~^(?:text/|application/(?:atom\+|rss\+)?)xml~i';

    /**
     * Construct
     *
     * @access public
     * @param  $base_url
     * @throws \ErrorException
     */
    public function __construct($base_url = null)
    {
        if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is not loaded');
        }

        $this->_curl = curl_init();
        $this->_id = 1;
        $this->setDefaultUserAgent();
        $this->setDefaultJsonDecoder();
        $this->setDefaultTimeout();
        $this->setOpt(CURLINFO_HEADER_OUT, true);
        $this->setOpt(CURLOPT_HEADERFUNCTION, array($this, 'headerCallback'));
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
        $this->_headers = new OnePica_AvaTax16_IO_CaseInsensitiveArray();
        $this->setURL($base_url);
    }

    /**
     * Build Post Data
     *
     * @access public
     * @param  $data
     *
     * @return array|string
     */
    public function buildPostData($data)
    {
        if (is_array($data)) {
            if (self::is_array_multidim($data)) {
                if (isset($this->_headers['Content-Type']) &&
                    preg_match($this->_jsonPattern, $this->_headers['Content-Type'])) {
                    $json_str = json_encode($data);
                    if (!($json_str === false)) {
                        $data = $json_str;
                    }
                } else {
                    $data = self::http_build_multi_query($data);
                }
            } else {
                $binary_data = false;
                foreach ($data as $key => $value) {
                    // Fix "Notice: Array to string conversion" when $value in
                    // curl_setopt($ch, CURLOPT_POSTFIELDS, $value) is an array
                    // that contains an empty array.
                    if (is_array($value) && empty($value)) {
                        $data[$key] = '';
                        // Fix "curl_setopt(): The usage of the @filename API for
                        // file uploading is deprecated. Please use the CURLFile
                        // class instead".
                    } elseif (is_string($value) && strpos($value, '@') === 0) {
                        $binary_data = true;
                        if (class_exists('CURLFile')) {
                            $data[$key] = new \CURLFile(substr($value, 1));
                        }
                    } elseif ($value instanceof \CURLFile) {
                        $binary_data = true;
                    }
                }

                if (!$binary_data) {
                    if (isset($this->_headers['Content-Type']) &&
                        preg_match($this->_jsonPattern, $this->_headers['Content-Type'])) {
                        $json_str = json_encode($data);
                        if (!($json_str === false)) {
                            $data = $json_str;
                        }
                    } else {
                        $data = http_build_query($data, '', '&');
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Call
     *
     * @access public
     */
    public function call()
    {
        $args = func_get_args();
        $function = array_shift($args);
        if (is_callable($function)) {
            array_unshift($args, $this);
            call_user_func_array($function, $args);
        }
    }

    /**
     * Close
     *
     * @access public
     */
    public function close()
    {
        if (is_resource($this->_curl)) {
            curl_close($this->_curl);
        }
        $this->_options = null;
        $this->_jsonDecoder = null;
    }

    /**
     * Complete
     *
     * @access public
     * @param  $callback
     */
    public function complete($callback)
    {
        $this->_completeFunction = $callback;
    }

    /**
     * Progress
     *
     * @access public
     * @param  $callback
     */
    public function progress($callback)
    {
        $this->setOpt(CURLOPT_PROGRESSFUNCTION, $callback);
        $this->setOpt(CURLOPT_NOPROGRESS, false);
    }

    /**
     * Delete
     *
     * @access public
     * @param  $url
     * @param  $query_parameters
     * @param  $data
     *
     * @return string
     */
    public function delete($url, $query_parameters = array(), $data = array())
    {
        if (is_array($url)) {
            $data = $query_parameters;
            $query_parameters = $url;
            $url = $this->_baseUrl;
        }

        $this->setURL($url, $query_parameters);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));
        return $this->exec();
    }

    /**
     * Download Complete
     *
     * @access public
     * @param  $fh
     */
    public function downloadComplete($fh)
    {
        if (!$this->_error && $this->_downloadCompleteFunction) {
            rewind($fh);
            $this->call($this->_downloadCompleteFunction, $fh);
            $this->_downloadCompleteFunction = null;
        }

        if (is_resource($fh)) {
            fclose($fh);
        }

        // Fix "PHP Notice: Use of undefined constant STDOUT" when reading the
        // PHP script from stdin. Using null causes "Warning: curl_setopt():
        // supplied argument is not a valid File-Handle resource".
        if (!defined('STDOUT')) {
            define('STDOUT', fopen('php://stdout', 'w'));
        }

        // Reset CURLOPT_FILE with STDOUT to avoid: "curl_exec(): CURLOPT_FILE
        // resource has gone away, resetting to default".
        $this->setOpt(CURLOPT_FILE, STDOUT);

        // Reset CURLOPT_RETURNTRANSFER to tell cURL to return subsequent
        // responses as the return value of curl_exec(). Without this,
        // curl_exec() will revert to returning boolean values.
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * Download
     *
     * @access public
     * @param  $url
     * @param  $mixed_filename
     *
     * @return boolean
     */
    public function download($url, $mixed_filename)
    {
        if (is_callable($mixed_filename)) {
            $this->_downloadCompleteFunction = $mixed_filename;
            $fh = tmpfile();
        } else {
            $filename = $mixed_filename;
            $fh = fopen($filename, 'wb');
        }

        $this->setOpt(CURLOPT_FILE, $fh);
        $this->get($url);
        $this->downloadComplete($fh);

        return ! $this->_error;
    }

    /**
     * Error
     *
     * @access public
     * @param  $callback
     */
    public function error($callback)
    {
        $this->_errorFunction = $callback;
    }

    /**
     * Exec
     *
     * @access public
     * @param  $ch
     *
     * @return string
     */
    public function exec($ch = null)
    {
        $this->_responseCookies = array();
        if (!($ch === null)) {
            $this->_rawResponse = curl_multi_getcontent($ch);
        } else {
            $this->call($this->_beforeSendFunction);
            $this->_rawResponse = curl_exec($this->_curl);
            $this->_curlErrorCode = curl_errno($this->_curl);
        }
        $this->_curlErrorMessage = curl_error($this->_curl);
        $this->_curlError = !($this->_curlErrorCode === 0);
        $this->_httpStatusCode = curl_getinfo($this->_curl, CURLINFO_HTTP_CODE);
        $this->_httpError = in_array(floor($this->_httpStatusCode / 100), array(4, 5));
        $this->_error = $this->_curlError || $this->_httpError;
        $this->_errorCode = $this->_error ? ($this->_curlError ? $this->_curlErrorCode : $this->_httpStatusCode) : 0;

        // NOTE: CURLINFO_HEADER_OUT set to true is required for requestHeaders
        // to not be empty (e.g. $curl->setOpt(CURLINFO_HEADER_OUT, true);).
        if ($this->getOpt(CURLINFO_HEADER_OUT) === true) {
            $this->_requestHeaders = $this->_parseRequestHeaders(curl_getinfo($this->_curl, CURLINFO_HEADER_OUT));
        }
        $this->_responseHeaders = $this->_parseResponseHeaders($this->_rawResponseHeaders);
        list($this->_response, $this->_rawResponse) = $this->_parseResponse($this->_responseHeaders, $this->_rawResponse);

        $this->_httpErrorMessage = '';
        if ($this->_error) {
            if (isset($this->_responseHeaders['Status-Line'])) {
                $this->_httpErrorMessage = $this->_responseHeaders['Status-Line'];
            }
        }
        $this->_errorMessage = $this->_curlError ? $this->_curlErrorMessage : $this->_httpErrorMessage;

        if (!$this->_error) {
            $this->call($this->_successFunction);
        } else {
            $this->call($this->_errorFunction);
        }

        $this->call($this->_completeFunction);

        return $this->_response;
    }

    /**
     * Get
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function get($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->_baseUrl;
        }
        $this->setURL($url, $data);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $this->setOpt(CURLOPT_HTTPGET, true);
        return $this->exec();
    }

    /**
     * Get Opt
     *
     * @access public
     * @param  $option
     *
     * @return mixed
     */
    public function getOpt($option)
    {
        return $this->_options[$option];
    }

    /**
     * Head
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function head($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->_baseUrl;
        }
        $this->setURL($url, $data);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'HEAD');
        $this->setOpt(CURLOPT_NOBODY, true);
        return $this->exec();
    }

    /**
     * Header Callback
     *
     * @access public
     * @param  $ch
     * @param  $header
     *
     * @return integer
     */
    public function headerCallback($ch, $header)
    {
        if (preg_match('/^Set-Cookie:\s*([^=]+)=([^;]+)/mi', $header, $cookie) == 1) {
            $this->_responseCookies[$cookie[1]] = $cookie[2];
        }
        $this->_rawResponseHeaders .= $header;
        return strlen($header);
    }

    /**
     * Options
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function options($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->_baseUrl;
        }
        $this->setURL($url, $data);
        $this->unsetHeader('Content-Length');
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'OPTIONS');
        return $this->exec();
    }

    /**
     * Patch
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function patch($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->_baseUrl;
        }

        if (is_array($data) && empty($data)) {
            $this->unsetHeader('Content-Length');
        }

        $this->setURL($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));
        return $this->exec();
    }

    /**
     * Post
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function post($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->_baseUrl;
        }

        $this->setURL($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));
        return $this->exec();
    }

    /**
     * Put
     *
     * @access public
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function put($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->_baseUrl;
        }
        $this->setURL($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $put_data = $this->buildPostData($data);
        if (empty($this->_options[CURLOPT_INFILE]) && empty($this->_options[CURLOPT_INFILESIZE])) {
            $this->setHeader('Content-Length', strlen($put_data));
        }
        $this->setOpt(CURLOPT_POSTFIELDS, $put_data);
        return $this->exec();
    }

    /**
     * Set Basic Authentication
     *
     * @access public
     * @param  $username
     * @param  $password
     */
    public function setBasicAuthentication($username, $password = '')
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->setOpt(CURLOPT_USERPWD, $username . ':' . $password);
    }

    /**
     * Set Digest Authentication
     *
     * @access public
     * @param  $username
     * @param  $password
     */
    public function setDigestAuthentication($username, $password = '')
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        $this->setOpt(CURLOPT_USERPWD, $username . ':' . $password);
    }

    /**
     * Set Cookie
     *
     * @access public
     * @param  $key
     * @param  $value
     */
    public function setCookie($key, $value)
    {
        $this->_cookies[$key] = $value;
        $this->setOpt(CURLOPT_COOKIE, str_replace(' ', '%20', urldecode(http_build_query($this->_cookies, '', '; '))));
    }

    /**
     * Get cookie.
     *
     * @access public
     * @param  $key
     */
    public function getCookie($key)
    {
        return $this->getResponseCookie($key);
    }

    /**
     * Get response cookie.
     *
     * @access public
     * @param  $key
     */
    public function getResponseCookie($key)
    {
        return isset($this->_responseCookies[$key]) ? $this->_responseCookies[$key] : null;
    }

    /**
     * Set Port
     *
     * @access public
     * @param  $port
     */
    public function setPort($port)
    {
        $this->setOpt(CURLOPT_PORT, intval($port));
    }

    /**
     * Set Connect Timeout
     *
     * @access public
     * @param  $seconds
     */
    public function setConnectTimeout($seconds)
    {
        $this->setOpt(CURLOPT_CONNECTTIMEOUT, $seconds);
    }

    /**
     * Set Cookie File
     *
     * @access public
     * @param  $cookie_file
     */
    public function setCookieFile($cookie_file)
    {
        $this->setOpt(CURLOPT_COOKIEFILE, $cookie_file);
    }

    /**
     * Set Cookie Jar
     *
     * @access public
     * @param  $cookie_jar
     */
    public function setCookieJar($cookie_jar)
    {
        $this->setOpt(CURLOPT_COOKIEJAR, $cookie_jar);
    }

    /**
     * Set Default JSON Decoder
     *
     * @access public
     */
    public function setDefaultJsonDecoder()
    {
        $this->_jsonDecoder = function($response) {
            $json_obj = json_decode($response, false);
            if (!($json_obj === null)) {
                $response = $json_obj;
            }
            return $response;
        };
    }

    /**
     * Set Default Timeout
     *
     * @access public
     */
    public function setDefaultTimeout()
    {
        $this->setTimeout(self::DEFAULT_TIMEOUT);
    }

    /**
     * Set Default User Agent
     *
     * @access public
     */
    public function setDefaultUserAgent()
    {
        $user_agent = 'PHP-Curl-Class/' . self::VERSION;
        $user_agent .= ' PHP/' . PHP_VERSION;
        $curl_version = curl_version();
        $user_agent .= ' curl/' . $curl_version['version'];
        $this->setUserAgent($user_agent);
    }

    /**
     * Set Header
     *
     * @access public
     * @param  $key
     * @param  $value
     *
     * @return string
     */
    public function setHeader($key, $value)
    {
        $this->_headers[$key] = $value;
        $headers = array();
        foreach ($this->_headers as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * Set JSON Decoder
     *
     * @access public
     * @param  $function
     */
    public function setJsonDecoder($function)
    {
        if (is_callable($function)) {
            $this->_jsonDecoder = $function;
        }
    }

    /**
     * Set Opt
     *
     * @access public
     * @param  $option
     * @param  $value
     *
     * @return boolean
     */
    public function setOpt($option, $value)
    {
        $required_options = array(
            CURLOPT_RETURNTRANSFER => 'CURLOPT_RETURNTRANSFER',
        );

        if (in_array($option, array_keys($required_options), true) && !($value === true)) {
            trigger_error($required_options[$option] . ' is a required option', E_USER_WARNING);
        }

        $this->_options[$option] = $value;
        return curl_setopt($this->_curl, $option, $value);
    }

    /**
     * Set Referer
     *
     * @access public
     * @param  $referer
     */
    public function setReferer($referer)
    {
        $this->setReferrer($referer);
    }

    /**
     * Set Referrer
     *
     * @access public
     * @param  $referrer
     */
    public function setReferrer($referrer)
    {
        $this->setOpt(CURLOPT_REFERER, $referrer);
    }

    /**
     * Set Timeout
     *
     * @access public
     * @param  $seconds
     */
    public function setTimeout($seconds)
    {
        $this->setOpt(CURLOPT_TIMEOUT, $seconds);
    }

    /**
     * Set Url
     *
     * @access public
     * @param  $url
     * @param  $data
     */
    public function setURL($url, $data = array())
    {
        $this->_baseUrl = $url;
        $this->_url = $this->_buildURL($url, $data);
        $this->setOpt(CURLOPT_URL, $this->_url);
    }

    /**
     * Set User Agent
     *
     * @access public
     * @param  $user_agent
     */
    public function setUserAgent($user_agent)
    {
        $this->setOpt(CURLOPT_USERAGENT, $user_agent);
    }

    /**
     * Success
     *
     * @access public
     * @param  $callback
     */
    public function success($callback)
    {
        $this->_successFunction = $callback;
    }

    /**
     * Unset Header
     *
     * @access public
     * @param  $key
     */
    public function unsetHeader($key)
    {
        $this->setHeader($key, '');
        unset($this->_headers[$key]);
    }

    /**
     * Verbose
     *
     * @access public
     * @param  $on
     */
    public function verbose($on = true)
    {
        $this->setOpt(CURLOPT_VERBOSE, $on);
    }

    /**
     * Destruct
     *
     * @access public
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Build Url
     *
     * @access protected
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    protected function _buildURL($url, $data = array())
    {
        return $url . (empty($data) ? '' : '?' . http_build_query($data));
    }

    /**
     * Parse Headers
     *
     * @access protected
     * @param  $raw_headers
     *
     * @return array
     */
    protected function _parseHeaders($raw_headers)
    {
        $raw_headers = preg_split('/\r\n/', $raw_headers, null, PREG_SPLIT_NO_EMPTY);
        $http_headers = new OnePica_AvaTax16_IO_CaseInsensitiveArray();

        $raw_headers_count = count($raw_headers);
        for ($i = 1; $i < $raw_headers_count; $i++) {
            list($key, $value) = explode(':', $raw_headers[$i], 2);
            $key = trim($key);
            $value = trim($value);
            // Use isset() as array_key_exists() and ArrayAccess are not compatible.
            if (isset($http_headers[$key])) {
                $http_headers[$key] .= ',' . $value;
            } else {
                $http_headers[$key] = $value;
            }
        }

        return array(isset($raw_headers['0']) ? $raw_headers['0'] : '', $http_headers);
    }

    /**
     * Parse Request Headers
     *
     * @access protected
     * @param  $raw_headers
     *
     * @return array
     */
    protected function _parseRequestHeaders($raw_headers)
    {
        $request_headers = new OnePica_AvaTax16_IO_CaseInsensitiveArray();
        list($first_line, $headers) = $this->_parseHeaders($raw_headers);
        $request_headers['Request-Line'] = $first_line;
        foreach ($headers as $key => $value) {
            $request_headers[$key] = $value;
        }
        return $request_headers;
    }

    /**
     * Parse Response
     *
     * @access protected
     * @param  $response_headers
     * @param  $raw_response
     *
     * @return array
     */
    protected function _parseResponse($response_headers, $raw_response)
    {
        $response = $raw_response;
        if (isset($response_headers['Content-Type'])) {
            if (preg_match($this->_jsonPattern, $response_headers['Content-Type'])) {
                $json_decoder = $this->_jsonDecoder;
                if (is_callable($json_decoder)) {
                    $response = $json_decoder($response);
                }
            } elseif (preg_match($this->_xmlPattern, $response_headers['Content-Type'])) {
                $xml_obj = @simplexml_load_string($response);
                if (!($xml_obj === false)) {
                    $response = $xml_obj;
                }
            }
        }

        return array($response, $raw_response);
    }

    /**
     * Parse Response Headers
     *
     * @access protected
     * @param  $raw_response_headers
     *
     * @return array
     */
    protected function _parseResponseHeaders($raw_response_headers)
    {
        $response_header_array = explode("\r\n\r\n", $raw_response_headers);
        $response_header  = '';
        for ($i = count($response_header_array) - 1; $i >= 0; $i--) {
            if (stripos($response_header_array[$i], 'HTTP/') === 0) {
                $response_header = $response_header_array[$i];
                break;
            }
        }

        $response_headers = new OnePica_AvaTax16_IO_CaseInsensitiveArray();
        list($first_line, $headers) = $this->_parseHeaders($response_header);
        $response_headers['Status-Line'] = $first_line;
        foreach ($headers as $key => $value) {
            $response_headers[$key] = $value;
        }
        return $response_headers;
    }

    /**
     * Http Build Multi Query
     *
     * @access public
     * @param  $data
     * @param  $key
     *
     * @return string
     */
    public static function http_build_multi_query($data, $key = null)
    {
        $query = array();

        if (empty($data)) {
            return $key . '=';
        }

        $is_array_assoc = self::is_array_assoc($data);

        foreach ($data as $k => $value) {
            if (is_string($value) || is_numeric($value)) {
                $brackets = $is_array_assoc ? '[' . $k . ']' : '[]';
                $query[] = urlencode($key === null ? $k : $key . $brackets) . '=' . rawurlencode($value);
            } elseif (is_array($value)) {
                $nested = $key === null ? $k : $key . '[' . $k . ']';
                $query[] = self::http_build_multi_query($value, $nested);
            }
        }

        return implode('&', $query);
    }

    /**
     * Is Array Assoc
     *
     * @access public
     * @param  $array
     *
     * @return boolean
     */
    public static function is_array_assoc($array)
    {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }

    /**
     * Is Array Multidim
     *
     * @access public
     * @param  $array
     *
     * @return boolean
     */
    public static function is_array_multidim($array)
    {
        if (!is_array($array)) {
            return false;
        }

        return (bool)count(array_filter($array, 'is_array'));
    }

    /**
     * Get Error
     *
     * @return bool
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Get Error Code
     *
     * @return int
     */
    public function getErrorCode()
    {
        return $this->_errorCode;
    }

    /**
     * Get Error Message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->_errorMessage;
    }

    /**
     * Get Curl Error
     *
     * @return bool
     */
    public function getCurlError()
    {
        return $this->_curlError;
    }

    /**
     * Get Curl Error Code
     *
     * @return int
     */
    public function getCurlErrorCode()
    {
        return $this->_curlErrorCode;
    }

    /**
     * Get Curl Error Message
     *
     * @return string
     */
    public function getCurlErrorMessage()
    {
        return $this->_curlErrorMessage;
    }

    /**
     * Get Http Error
     *
     * @return bool
     */
    public function getHttpError()
    {
        return $this->_httpError;
    }

    /**
     * Get http Status Code
     *
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->_httpStatusCode;
    }

    /**
     * Get Http Error Message
     *
     * @return string
     */
    public function getHttpErrorMessage()
    {
        return $this->_httpErrorMessage;
    }

    /**
     * Get Base Url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * Get Url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Get Request Headers
     *
     * @return array
     */
    public function getRequestHeaders()
    {
        return $this->_requestHeaders;
    }

    /**
     * Get Response Headers
     *
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->_responseHeaders;
    }

    /**
     * Get Raw Response Headers
     *
     * @return string
     */
    public function getRawResponseHeaders()
    {
        return $this->_rawResponseHeaders;
    }

    /**
     * Get Response
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Get Raw Response
     *
     * @return string
     */
    public function getRawResponse()
    {
        return $this->_rawResponse;
    }

    /**
     * Get Before Send Function
     *
     * @return string
     */
    public function getBeforeSendFunction()
    {
        return $this->_beforeSendFunction;
    }

    /**
     * Set Before Send Function
     *
     * @param string $function
     * @return $this
     */
    public function setBeforeSendFunction($function)
    {
        $this->_beforeSendFunction = $function;
        return $this;
    }

    /**
     * Get Download Complete Function
     *
     * @return string
     */
    public function getDownloadCompleteFunction()
    {
        return $this->_downloadCompleteFunction;
    }

    /**
     * Get Download Complete Function
     *
     * @param string $function
     * @return $this
     */
    public function setDownloadCompleteFunction($function)
    {
        $this->_downloadCompleteFunction = $function;
        return $this;
    }
}
