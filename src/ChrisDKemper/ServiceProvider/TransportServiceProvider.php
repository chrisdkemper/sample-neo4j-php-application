<?php namespace ChrisDKemper\ServiceProvider;

use
    ChrisDKemper\Repository\TransportRepository,
    ChrisDKemper\Service\TransportService
;

use
    Silex\Application,
    Silex\ServiceProviderInterface
;

class TransportServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['transport.repository'] = new TransportRepository($app['client']);
        $app['transport.service'] = new TransportService($app['transport.repository']);
    }

    public function boot(Application $app)
    {

    }
}