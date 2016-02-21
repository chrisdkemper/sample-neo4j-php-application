<?php namespace ChrisDKemper;

use
    Silex\Application as BaseApplication,
    Silex\Provider\TwigServiceProvider,
    Silex\Provider\ServiceControllerServiceProvider
;

use
    LewisB\PheanstalkServiceProvider\PheanstalkServiceProvider
;

class Application extends BaseApplication
{
    public function __construct(array $config = array())
    {
        parent::__construct();

        $this->values = array_merge($this->values, $config);

        $this->register(new TwigServiceProvider(), array(
            'twig.path' => __DIR__ . '/../../app/views',
        ));

        $this->register(new ServiceControllerServiceProvider);
    }
}