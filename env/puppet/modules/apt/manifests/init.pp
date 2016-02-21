class apt {

	$mirror = 'uk.archive.ubuntu.com'

    exec { 'apt archive source':
        command => "sed -i 's/archive.ubuntu.com/$mirror/' /etc/apt/sources.list",
        unless => "grep -v -c '$mirror' /etc/apt/sources.list"
    }
    
    exec { 'apt security source':
        command => "sed -i 's/security.ubuntu.com/$mirror/' /etc/apt/sources.list",
        require => Exec['apt archive source']
    }

    exec { 'apt-get update':
        command => '/usr/bin/apt-get update --fix-missing',
        require => Exec['apt security source']
    }
}
