class php::cgi {

    include php
    
    $packages = [
        'php5-cgi',
        'spawn-fcgi'
    ]
    
    package { $packages:
        ensure => present,
        require => Exec['apt-get update']
    }

    service { 'php-fastcgi':
        ensure => running,
        require => Exec['update-rc.d php-fastcgi defaults']
    }

    file { '/etc/php5/cgi/php.ini':
        ensure => present,
        owner => 'root',
        group => 'root',
        source => 'puppet:///data/modules/php/templates/php.ini',
        require => Package['php5-cgi'],
    }
    
    file { '/etc/init.d/php-fastcgi':
        ensure => present,
        owner => 'root',
        group => 'root',
        source => 'puppet:///data/modules/php/templates/php-fastcgi',
        require => Package['php5-cgi'],
        mode => 777
    }
    
    exec { 'update-rc.d php-fastcgi defaults':
        unless => 'service --status-all 2>&1|grep php-fastcgi',
        command => 'update-rc.d php-fastcgi defaults',
        require => File['/etc/init.d/php-fastcgi']
    }
}