class nginx {
    
    package { 'nginx':
        ensure => present,
        require => Exec['apt-get update']
    }
    
    service { 'nginx':
        ensure => running,
        require => Package['nginx']
    }
    
    file { '/etc/nginx/sites-enabled/default':
        ensure => absent,
        require => Package['nginx']
    }

    file { '/vagrant/logs/access.log':
        ensure => present,
        owner => 'root',
        group => 'root',
        require => Package['nginx'],
        notify => Service['nginx'] 
    }

    file { '/vagrant/logs/error.log':
        ensure => present,
        owner => 'root',
        group => 'root',
        require => Package['nginx'],
        notify => Service['nginx']
    }
    
    file { '/etc/nginx/nginx.conf':
        ensure => present,
        owner => 'root',
        group => 'root',
        source => 'puppet:///data/modules/nginx/templates/nginx.conf',
        require => Package['nginx'],
        notify => Service['nginx']
    }
    
    file { '/etc/nginx/sites-enabled/vagrant.conf':
        ensure => present,
        owner => 'root',
        group => 'root',
        source => 'puppet:///data/modules/nginx/templates/vagrant.conf',
        require => Package['nginx'],
        notify => Service['nginx']
    }
}
