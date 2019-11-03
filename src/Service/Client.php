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
    protected $apiKey;
    protected $apiSecret;
    protected $apiTimeout;
    protected $apiVersion;

    private $client;
    private $token;


    public function __construct() {
        // nothing yet
    }


    private function _toArray($_response) {
        return json_decode($_response->getBody()->getContents());
    }


    /**
     * Configures the default GuzzleHttp\Client.
     * 
     * @return GuzzleHttp\Client
     */
    private function setupClient() {
        return new GuzzleClient([
            'base_uri' => $this->apiBaseUrl,
            'timeout'  => $this->apiTimeout,
        ]);
    }


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

        $this->setToken($this->_toArray($response)->token);
    }


    public function getTagsAndMentions() {
        $response = $this->client->request(
            'GET',
            "{$this->apiVersion}/tags-and-mentions",
            [
                'headers' => [
                    'Authorization' => "Bearer {$this->getToken()}",        
                    'Accept'        => 'application/json',
                ],
            ]
        );
        return $this->_toArray($response);
    }


    public function getTimeEntries(\DateTimeInterface $_stoppedAfter, \DateTimeInterface $_startedBefore) {
        $serializer    = new Serializer(array(new DateTimeNormalizer('Y-m-d')));
        $stoppedAfter  = "{$serializer->normalize($_stoppedAfter)}T00:00:00.000";
        $startedBefore = "{$serializer->normalize($_startedBefore)}T23:59:59.999";

        $response = $this->client->request(
            'GET',
            "{$this->apiVersion}/time-entries/{$stoppedAfter}/{$startedBefore}",
            [
                'headers' => [
                    'Authorization' => "Bearer {$this->getToken()}",        
                    'Accept'        => 'application/json',
                ],
            ]
        );
        return $this->_toArray($response);
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
    public function setToken($_token) {
        $this->token = $_token;
        return $this;
    }

    public function getToken() {
        return $this->token;
    }

}