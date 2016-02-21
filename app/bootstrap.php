<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new ChrisDKemper\Application(array('debug' => true));

/*
 * Register the client to communicate with Neo4j
 */

$app->register(new ChrisDKemper\ServiceProvider\ClientServiceProvider(), array(
    'client.username'   => 'neo4j',
    'client.password'   => 'password',
    'client.transport'  => 'localhost',
    'client.port'       => 7474,
    'client.https'      => false
));

/*
 * Register the console application
 */

$app->register(new Knp\Provider\ConsoleServiceProvider(), array(
    'console.name'              => 'App',
    'console.version'           => '1.0.0',
    'console.project_directory' => __DIR__.'/..'
));

/*
 * Register the Place service provider
 */

$app->register(new ChrisDKemper\ServiceProvider\PlaceServiceProvider());

/*
 * Register the BusStop service provider
 */

$app->register(new ChrisDKemper\ServiceProvider\BusStopServiceProvider());

/*
 * Register the Timetable service provider
 */

$app->register(new ChrisDKemper\ServiceProvider\TimetableServiceProvider());

/*
 * Register the Transport service provider
 */

$app->register(new ChrisDKemper\ServiceProvider\TransportServiceProvider());

/*
 * Register the Journey service provider
 */

$app->register(new ChrisDKemper\ServiceProvider\JourneyServiceProvider());

/*
 * Since the homepage is static, pointless putting it in a provider
 */
$app->get('/', function () use ($app) {
    return $app['twig']->render('index.twig');
});

/*
 * Journey routes
 */
$app->mount('/journey', new ChrisDKemper\ControllerProvider\JourneyControllerProvider());


return $app;