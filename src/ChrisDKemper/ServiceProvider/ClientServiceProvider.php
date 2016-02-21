<?php namespace ChrisDKemper\ServiceProvider;

use
    ChrisDKemper\Repository\TrainerRepository,
    ChrisDKemper\Service\TrainerService,
    ChrisDKemper\Client\Client
;

use
    Silex\Application,
    Silex\ServiceProviderInterface
;

class ClientServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['client'] = $app->share(function() use ($app) {
            $client = new Client(
                $app['client.username'],
                $app['client.password'],
                $app['client.transport'],
                $app['client.port'],
                $app['client.https']
            );

            return $client;
        });
    }

    public function boot(Application $app)
    {

    }
}