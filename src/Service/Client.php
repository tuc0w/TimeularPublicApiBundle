<?php

namespace Tuc0w\TimeularPublicApiBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

use GuzzleHttp\Client as GuzzleClient;

class Client {

    /**
     * Configuration variables
     */
    protected $apiBaseUrl;
    protected $apiHeader;
    protected $apiKey;
    protected $apiSecret;
    protected $apiTimeout;
    protected $apiVersion;

    private $client;
    private $token;


    public function __construct() {
        // nothing yet
    }


    /**
     * Converts the given $_response string from JSON to array
     * 
     * @param string $_response
     * 
     * @return array
     */
    private function _toArray($_response) {
        return json_decode($_response->getBody()->getContents());
    }


    /**
     * Configures the default GuzzleHttp\Client.
     * 
     * @return GuzzleClient
     */
    private function setupClient() {
        return new GuzzleClient([
            'base_uri' => $this->apiBaseUrl,
            'timeout'  => $this->apiTimeout,
        ]);
    }

    /**
     * Retrieves a authentication token during the sign-in process.
     */
    public function signIn() {
        if (!$this->client) {
            $this->client = $this->setupClient();
        }

        $response = $this->client->request(
            'POST',
            "{$this->apiVersion}/developer/sign-in",
            [
                'json' => [
                    'apiKey'    => $this->apiKey,
                    'apiSecret' => $this->apiSecret
                ]
            ]
        );

        $this->setHeader($this->_toArray($response)->token);
    }

    /**
     * @return array
     */
    public function getActivities() {
        return $this->get('activities');
    }

    /**
     * @return array
     */
    public function getCurrentTracking() {
        return $this->get('tracking');
    }

    /**
     * @return array
     */
    public function getDevices() {
        return $this->get('devices');
    }

    /**
     * @return array
     */
    public function getTagsAndMentions() {
        return $this->get('tags-and-mentions');
    }

    /**
     * @return array
     */
    public function getTimeEntries(\DateTimeInterface $_stoppedAfter, \DateTimeInterface $_startedBefore) {
        $serializer    = new Serializer(array(new DateTimeNormalizer('Y-m-d')));
        $stoppedAfter  = "{$serializer->normalize($_stoppedAfter)}T00:00:00.000";
        $startedBefore = "{$serializer->normalize($_startedBefore)}T23:59:59.999";

        return $this->get("time-entries/{$stoppedAfter}/{$startedBefore}");
    }

    /**
     * Request methods
     */

    /**
     * @param string $_endpoint
     * 
     * @return array
     */
    private function get($_endpoint) {
        return $this->_toArray(
            $this->client->request(
                'GET',
                "{$this->apiVersion}/{$_endpoint}",
                [
                    'headers' => $this->getHeader(),
                ]
            )
        );
    }

    /**
     * @param string $_endpoint
     * @param array $_payload
     * 
     * @return array
     */
    private function post($_endpoint, $_payload) {
        return $this->_toArray(
            $this->client->request(
                'POST',
                "{$this->apiVersion}/{$_endpoint}",
                [
                    'headers' => $this->getHeader(),
                    'json'    => $_payload
                ]
            )
        );
    }


    /**
     * Getter & Setter
     */

    /**
     * @param $apiBaseUrl
     */
    public function setApiBaseUrl($apiBaseUrl) {
        $this->apiBaseUrl = $apiBaseUrl;
    }

    /**
     * @param $apiKey
     */
    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
    }

    /**
     * @param $apiSecret
     */
    public function setApiSecret($apiSecret) {
        $this->apiSecret = $apiSecret;
    }

    /**
     * @param $apiTimeout
     */
    public function setApiTimeout($apiTimeout) {
        $this->apiTimeout = $apiTimeout;
    }

    /**
     * @param $apiVersion
     */
    public function setApiVersion($apiVersion) {
        $this->apiVersion = $apiVersion;
    }

    /**
     * @param $_token
     */
    public function setHeader($_token) {
        $this->apiHeader = [
            'Authorization' => "Bearer {$_token}",        
            'Accept'        => 'application/json',
        ];
    }

    /**
     * @return string
     */
    public function getHeader() {
        return $this->apiHeader;
    }

}
