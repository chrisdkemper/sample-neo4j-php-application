class php {

    $packages = [
        'php5-cli',
        'php5-curl'
    ]
    
    package { $packages:
        ensure => present,
        require => Exec['apt-get update']
    }
    
    file { '/etc/php5/cli/php.ini':
        ensure => present,
        owner => 'root',
        group => 'root',
        source => 'puppet:///data/modules/php/templates/php.ini',
        require => Package['php5-cli'],
    }
}