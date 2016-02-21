<?php namespace ChrisDKemper\ServiceProvider;

use
    ChrisDKemper\Service\JourneyService
;

use
    Silex\Application,
    Silex\ServiceProviderInterface
;

class JourneyServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['journey.service'] = new JourneyService($app['client']);
    }

    public function boot(Application $app)
    {

    }
}