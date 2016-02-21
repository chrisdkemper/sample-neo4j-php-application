<?php
namespace ChrisDKemper\Command;

use
	Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Input\InputOption,
 	Symfony\Component\Console\Output\OutputInterface
;
class BusStopImportCommand extends Command
{
	protected function configure()
    {
        $this
            ->setName('busstop:import')
            ->setDescription('Import bus stops to Neo4j')
        ;
    }
	protected function execute(InputInterface $input, OutputInterface $output)
	{
        /**Sample:BusStopImportCommand**/
		$app = $this->getSilexApplication();

        $client = $app['client'];
        $busstop_service = $app['busstop.service'];
        $place_service = $app['place.service'];

        $data = array(
            array(
                "name" => "Test 01",
                "lat"  => 51.50468231156003,
                "lon"  => -0.13475418090820312
            ),
            array(
                "name" => "Test 02",
                "lat"  => 51.51611390655047,
                "lon"  => -0.1407623291015625
            ),
            array(
                "name" => "Test 03",
                "lat"  => 51.51707531179727,
                "lon"  => -0.09029388427734374,
                "seats" => 9
            ),
            array(
                "name" => "Test 04",
                "lat"  => 51.55530141976372,
                "lon"  => -0.182647705078125
            ),
            array(
                "name" => "Test 05",
                "lat"  => 51.55060496921001,
                "lon"  => -0.19655227661132812
            ),
            array(
                "name" => "Test 06",
                "lat"  => 51.54323910441573,
                "lon"  => -0.1929473876953125,
            )
        );

        foreach($data as $values)
        {
            $place = $place_service->find('name', $values['name']);

            //Check if the node exists
            if(empty($place)) {
                //If the needed fields aren't there can't do it
                if( ! array_key_exists("lat", $values) && ! array_key_exists("lon", $values)) {
                    $output->writeln(sprintf('Cannot find "%s" and not enough info suppoed to creted Place', $values['name']));
                    continue;
                }

                //Crete a new Place
                $place_values = array(
                    "name" => $values['name'],
                    "lat" => $values['lat'],
                    "lon" => $values['lon']
                );

                //Create a new palce
                $place = $place_service->create($values);
                $output->writeln(sprintf('Just created %s', $place->name));

                //Add the place to the spatial index
                $node_uri = sprintf('%s/db/data/node/%s', $client->getBaseUrl(), $place->id);
                $result = $client->spatialAddNodeToLayer('geom', $node_uri);

                $output->writeln(sprintf('Just add %s to the index', $place->name));
            }

            //Unset the values so they aren't set on the station and the place
            unset($values['lat'], $values['lon']);
            $busstop = $busstop_service->create($values);

            $output->writeln(sprintf('Just created bus stop %s', $busstop->name));

            //Relate the bus stop to the place
            $cypher = sprintf("
                MATCH (a:BusStop),(b:Place)
                WHERE id(a) = %s AND id(b) = %s
                CREATE UNIQUE (a)-[r:LOCATED_AT]->(b)
                RETURN r", $busstop->id, $place->id);

            $data = $client->cypher($cypher);

            $output->writeln(sprintf('Just related %s to %s', $busstop->name, $place->name));
        }

		$output->writeln('All nodes imported');
	}
}