#Download the archive
wget --output-document=/vagrant/spatial.zip -N -q https://github.com/neo4j-contrib/m2/blob/master/releases/org/neo4j/neo4j-spatial/0.15-neo4j-2.3.0/neo4j-spatial-0.15-neo4j-2.3.0-server-plugin.zip?raw=true

#Find unzip
apt-cache search unzip -qq

#Install unzip
apt-get install unzip -f -qq

#Unzup the archive
unzip -qq -o /vagrant/spatial.zip -d /vagrant/bin

#Stop Neo4j
service neo4j-service stop

#Load the plugin
mv -f /vagrant/bin/*.jar /var/lib/neo4j/plugins

#Start Neo4j
service neo4j-service start

#Create the indexes
php /vagrant/bin/console index:create

#Import the busstops
php /vagrant/bin/console busstop:import

#Import the timetables
php /vagrant/bin/console timetable:import