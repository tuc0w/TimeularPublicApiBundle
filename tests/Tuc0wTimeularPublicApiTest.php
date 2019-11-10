<?php

namespace Tuc0w\TimeularPublicApiBundle\Tests;

use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Tuc0w\TimeularPublicApiBundle\Service\Client as TimeularClient;

class Tuc0wTimeularPublicApiTest extends TestCase {
    public function testSignIn() {
        /**
         * since we don't have public api keys/secrets available for testing
         * we expect a 401 reponse which will lead to a GuzzleHttp\Exception\ClientException.
         */
        $this->expectException(ClientException::class);

        $timeular = new TimeularClient();
        $timeular->setApiBaseUrl('https://api.timeular.com');
        $timeular->setApiKey('your_api_key');
        $timeular->setApiSecret('your_api_secret');
        $timeular->setApiTimeout(30.0);
        $timeular->setApiVersion('/api/v2');

        $timeular->signIn();
    }
}
