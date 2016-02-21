<?php namespace ChrisDKemper\ServiceProvider;

use
    ChrisDKemper\Repository\PlaceRepository,
    ChrisDKemper\Service\PlaceService
;

use
    Silex\Application,
    Silex\ServiceProviderInterface
;

class PlaceServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['place.repository'] = new PlaceRepository($app['client']);
        $app['place.service'] = new PlaceService($app['place.repository'], $app['client']);
    }

    public function boot(Application $app)
    {

    }
}