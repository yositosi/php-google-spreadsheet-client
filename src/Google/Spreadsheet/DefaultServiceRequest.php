<?php
/**
 * Copyright 2013 Asim Liaquat
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Google\Spreadsheet;

/**
 * Service Request. The parent class of all services.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class DefaultServiceRequest implements ServiceRequestInterface
{
    /**
     * Request object
     * 
     * @var \Google\Spreadsheet\Request
     */
    private $accessToken;

    /**
     * Request headers
     * 
     * @var array
     */
    private $headers = array();

    /**
     * Service url
     * 
     * @var string
     */
    private $serviceUrl = 'https://spreadsheets.google.com/';

    /**
     * User agent
     * 
     * @var string
     */
    private $userAgent = 'PHP Google Spreadsheet Api';

    /**
     * Initializes the service request object.
     * 
     * @param \Google\Spreadsheet\Request $request
     */
    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
    
    /**
     * Set optional request headers. 
     * 
     * @param array $headers associative array of key value pairs
     *
     * @return Google\Spreadsheet\Request
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Get the user agent
     * 
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }
    
    /**
     * Set the user agent. It is a good ides to leave this as is.
     * 
     * @param string $userAgent
     *
     * @return Google\Spreadsheet\Request
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    protected function initRequest($url, $requestHeaders = array())
    {
        $curlParams = array (
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => 0,
            CURLOPT_FAILONERROR => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_VERBOSE => false,
        );

        if(substr($url, 0, 4) !== 'http') {
            $url = $this->serviceUrl . $url;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $curlParams);
        curl_setopt($ch, CURLOPT_URL, $url);

        $headers = array();
        if (count($this->getHeaders()) > 0) {
            foreach ($this->getHeaders() as $k => $v) {
                $headers[] = "$k: $v";
            }
        }
        $headers[] = "Authorization: OAuth " . $this->accessToken;
        $headers = array_merge($headers, $requestHeaders);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getUserAgent());
        return $ch;       
    }

    public function get($url)
    {
        $ch = $this->initRequest($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        return $this->execute($ch);
    }

    public function post($url, $postData)
    {
        $ch = $this->initRequest($url, array('Content-Type: application/atom+xml'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        return $this->execute($ch);
    }

    public function delete($url)
    {
        $ch = $this->initRequest($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->execute($ch);
    }

    /**
     * Executes the api request.
     * 
     * @return string the xml response
     *
     * @throws \Google\Spreadsheet\Exception If the was a problem with the request.
     *                                       Will throw an exception if the response
     *                                       code is 300 or greater
     */
    protected function execute($ch)
    {

        $ret = curl_exec($ch);

        $info = curl_getinfo($ch);
        if((int)$info['http_code'] > 299) {
            $exception = new Exception('Error in Google Request: '. $ret, $info['http_code']);
            //$exception->setRequest($this->request);
            //$this->resetRequestParams();
            throw $exception;
        }

        //$this->resetRequestParams();
        return $ret;
    }

    /**
     * Resets the properties of the request object to avoid unexpected behaviour
     * when making more than one request using the same request object.
     * 
     * @return void
     */
    private function resetRequestParams()
    {
        $this->request->setMethod(Request::GET);
        $this->request->setPost('');
        $this->request->setFullUrl(null);
        $this->request->setEndpoint('');
        $this->request->setHeaders(array());
    }
}