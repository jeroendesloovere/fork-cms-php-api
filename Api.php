<?php

namespace ForkCms\Api;

/**
 * @author			Tijs Verkoyen <php-fork-api@verkoyen.eu>
 * @version			1.0.0
 * @copyright		Copyright (c) 2008, Tijs Verkoyen. All rights reserved.
 * @license			BSD License
 */
class Api
{
    // current version
    const VERSION = '1.0.0';

    /**
     * The API key to use for authentication
     *
     * @var  string
     */
    private $apiKey;

    /**
     * The e-mail address to use for authentication
     *
     * @var string
     */
    private $email;

    /**
     * The timeout
     *
     * @var	int
     */
    private $timeOut = 10;

    /**
     * The user agent
     *
     * @var	string
     */
    private $userAgent;

    /**
     * The url to communicate on
     *
     * @var string
     */
    private $url;

    /**
     * Default constructor
     */
    public function __construct($url, $email = null, $apiKey = null)
    {
        $this->setUrl($url);
        if ($email !== null) {
            $this->setEmail($email);
        }
        if ($apiKey !== null) {
            $this->setApiKey($apiKey);
        }
    }

    /**
     * Make the call
     *
     * @param  string $method       The method to call.
     * @param  array  $parameters   The parameters to pass.
     * @param  string $httpMethod   The HTTP method to use.
     * @param  bool   $authenticate Should we use authentication?
     * @return mixed
     */
    public function doCall(
        $method,
        array $parameters = null,
        $httpMethod = 'GET',
        $authenticate = true
    ) {
        // build the url
        $url = $this->getUrl();

        $parameters['method'] = (string) $method;
        $parameters['format'] = 'json';

        // HTTP method
        if ($httpMethod == 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = http_build_query($parameters);
        } else {
            $options[CURLOPT_POST] = false;
            if (!empty($parameters)) {
                $url .= '?' . http_build_query($parameters);
            }
        }

        // set options
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_USERAGENT] = $this->getUserAgent();
        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
            $options[CURLOPT_FOLLOWLOCATION] = true;
        }
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_TIMEOUT] = (int) $this->getTimeOut();
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_SSL_VERIFYHOST] = false;

        // init
        $curl = curl_init();

        // set options
        curl_setopt_array($curl, $options);

        // execute
        $response = curl_exec($curl);
        $headers = curl_getinfo($curl);

        // fetch errors
        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);

        // close
        curl_close($curl);

        $json = json_decode($response, true);

        if (
            !isset($json['meta']['status_code']) ||
            !isset($json['data'])
        ) {
            throw new Exception('Invalid response');
        }

        if ($json['meta']['status_code'] != 200) {
            throw new Exception(
                $json['meta']['status'],
                $json['meta']['status_code']
            );
        }

        // we expect JSON, so decode it
        return $json['data'];
    }

    /**
     * get the API key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Get the e-mail address
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the timeout that will be used
     *
     * @return int
     */
    public function getTimeOut()
    {
        return $this->timeOut;
    }

    /**
     * Get the useragent that will be used. Our version will be prepended to
     * yours.
     * It will look like: "PHP ForkAPI/<version> <your-user-agent>"
     *
     * @return string
     */
    public function getUserAgent()
    {
        return 'PHP ForkAPI/'. self::VERSION .' '. $this->userAgent;
    }

    /**
     * Get the URL of the website we are working on
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the API key
     *
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Set the e-mail address
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Set the timeout
     * After this time the request will stop. You should handle any errors triggered by this.
     *
     * @return void
     * @param  int  $seconds The timeout in seconds.
     */
    public function setTimeOut($seconds)
    {
        $this->timeOut = (int) $seconds;
    }

    /**
     * Set the user-agent for you application
     * It will be appended to ours, the result will look like:
     * "PHP ForkAPI/<version> <your-user-agent>"
     *
     * @return void
     * @param  string $userAgent Your user-agent, it should look like <app-name>/<app-version>.
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = (string) $userAgent;
    }

    /**
     * Set the URL to work on
     *
     * @param string $url
     */
    protected function setUrl($url)
    {
        // make sure there is a / on the end
        $url = trim((string) $url, '/') . '/';

        $this->url = (string) $url;
    }
}