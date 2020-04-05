<?php

namespace Tuc0w\TimeularPublicApiBundle\Service;

use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;
use Tuc0w\TimeularPublicApiBundle\Service\Filters as TimeularFilters;

class Client {
    /**
     * Configuration variables.
     */
    protected $apiBaseUrl;
    protected $apiHeader;
    protected $apiKey;
    protected $apiSecret;
    protected $apiTimeout;
    protected $apiVersion;

    private $client;

    public function __construct() {
        // nothing yet
    }

    /**
     * Converts the given $response string from JSON to array.
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     *
     * @param string $response
     *
     * @return array
     */
    private function _toArray($response) {
        return json_decode($response->getBody()->getContents());
    }

    /**
     * Configures the default GuzzleHttp\Client.
     *
     * @return GuzzleClient
     */
    private function setupClient() {
        return new GuzzleClient([
            'base_uri' => $this->apiBaseUrl,
            'timeout' => $this->apiTimeout,
        ]);
    }

    /**
     * Retrieves an authentication token during the sign-in process.
     */
    public function signIn() {
        if (!$this->client) {
            $this->client = $this->setupClient();
        }

        $response = $this->post(
            'developer/sign-in',
            [
                'json' => [
                    'apiKey' => $this->apiKey,
                    'apiSecret' => $this->apiSecret,
                ],
            ]
        );

        $this->setHeader($response->token);
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
     * @param $filters
     *
     * @throws ExceptionInterface
     *
     * @return TimeularFilters
     */
    public function getTimeEntries(
        \DateTimeInterface $stoppedAfter,
        \DateTimeInterface $startedBefore,
        $filters = null
    ) {
        $serializer = new Serializer([new DateTimeNormalizer('Y-m-d')]);
        $stoppedAfter = "{$serializer->normalize($stoppedAfter)}T00:00:00.000";
        $startedBefore = "{$serializer->normalize($startedBefore)}T23:59:59.999";

        $timeEntries = $this->get("time-entries/{$stoppedAfter}/{$startedBefore}")->timeEntries;
        $timeularFilters = new TimeularFilters($this);

        return $timeularFilters->applyFilters($filters, $timeEntries);
    }

    /**
     * This method is used to do GET requests.
     *
     * @param string $endpoint
     *
     * @return array
     */
    private function get($endpoint) {
        return $this->_toArray(
            $this->client->request(
                'GET',
                "{$this->apiVersion}/{$endpoint}",
                [
                    'headers' => $this->getHeader(),
                ]
            )
        );
    }

    /**
     * This method is be used to do POST requests.
     * The payload should contain something like this:
     * [
     *     'headers' => $this->getHeader(),
     *     'json' => $payload,
     * ].
     *
     * @param string $endpoint
     * @param array  $payload
     *
     * @return array
     */
    private function post($endpoint, $payload) {
        return $this->_toArray(
            $this->client->request(
                'POST',
                "{$this->apiVersion}/{$endpoint}",
                $payload
            )
        );
    }

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
     * @param $token
     */
    public function setHeader($token) {
        $this->apiHeader = [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ];
    }

    /**
     * @return string
     */
    public function getHeader() {
        return $this->apiHeader;
    }
}
