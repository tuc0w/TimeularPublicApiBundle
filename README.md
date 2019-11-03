# TimeularPublicApiBundle

[![Build Status](https://travis-ci.org/tuc0w/TimeularPublicApiBundle.svg?branch=master)](https://travis-ci.org/tuc0w/TimeularPublicApiBundle)

TimeularPublicApiBundle is a way to integrate your time tracking with
the Timeular ZEI° device into your Symfony application, but with a bit
less work, an easy configuration and predefined useful filters.

Install the package with:
```console
composer require tuc0w/timeular-public-api-bundle --dev
```

And that's it! If you're *not* using Symfony Flex, you'll also
need to enable the `Tuc0w\TimeularPublicApiBundle\Tuc0wTimeularPublicApiBundle`
in your `AppKernel.php` file.


## Configuration

A few things needs be configured directly by creating a new
`config/packages/tuc0w_timeular_public_api.yaml` file.
The default values are:
```yaml
# config/packages/tuc0w_timeular_public_api.yaml
tuc0w_timeular_public_api:
    timeular:
        api_base_url:    https://api.timeular.com
        api_key:         your_api_key
        api_secret:      your_api_secret
        api_timeout:     30.0
        api_version:     /api/v2
```


## Usage

This bundle provides a single service to access the API, which
you can autowire by using the `TimeularClient` type-hint:
```php
// src/Controller/SomeController.php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Tuc0w\TimeularPublicApiBundle\Service\Client as TimeularClient;

class SomeController extends AbstractController {
    private $timeular;

    public function __construct(TimeularClient $_timeular) {
        $this->timeular = $_timeular;
    }

    /**
     * @Route("/", name="app_homepage")
     */
    public function index() {
        $this->timeular->signIn();

        // get the time entries from today
        return new JsonResponse(
            $this->timeular->getTimeEntries(
                new \DateTime(),
                new \DateTime()
            )
        );
    }
}
```

You can also access this service directly using the id
`tuc0w_timeular_public_api.client`.
```php
$timeular = $container->get('tuc0w_timeular_public_api.client');
```


## Available methods

Currently there are only a few methods available:

| Method                                          | Parameter             | Description                                                |
| ----------------------------------------------- | --------------------- | ---------------------------------------------------------- |
| signIn()                                        | *none*                | Uses the api key/secret to generate a token to sign in.    |
| getTagsAndMentions()                            | *none*                | Returns a list of used tags and mentions.                  |
| getTimeEntries($_stoppedAfter, $_startedBefore) | DateTimeInterface     | Returns all time entries between the given start/end date. |


## Todos
- [x] Basic functionality
- [ ] Create custom filter methods
- [ ] Integrate missing endpoints


## Contributing
Please feel comfortable submitting issues or pull requests: all contributions
and questions are warmly appreciated :)
