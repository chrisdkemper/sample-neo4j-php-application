<?php namespace ChrisDKemper\ServiceProvider;

use
    ChrisDKemper\Repository\TimetableRepository,
    ChrisDKemper\Service\TimetableService
;

use
    Silex\Application,
    Silex\ServiceProviderInterface
;

class TimetableServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['timetable.repository'] = new TimetableRepository($app['client']);
        $app['timetable.service'] = new TimetableService($app['timetable.repository']);
    }

    public function boot(Application $app)
    {

    }
}