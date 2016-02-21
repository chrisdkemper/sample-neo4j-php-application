<?php namespace ChrisDKemper\ServiceProvider;

use
    ChrisDKemper\Repository\BusStopRepository,
    ChrisDKemper\Service\BusStopService
;

use
    Silex\Application,
    Silex\ServiceProviderInterface
;

class BusStopServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['busstop.repository'] = new BusStopRepository($app['client']);
        $app['busstop.service'] = new BusStopService($app['busstop.repository']);
    }

    public function boot(Application $app)
    {

    }
}