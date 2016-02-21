<?php
namespace ChrisDKemper\Command;

use
	Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Input\InputOption,
 	Symfony\Component\Console\Output\OutputInterface
;
class TimetableImportCommand extends Command
{
	protected function configure()
    {
        $this
            ->setName('timetable:import')
            ->setDescription('Import bus stops to Neo4j')
        ;
    }
	protected function execute(InputInterface $input, OutputInterface $output)
	{
        /**Sample:TimetableImportCommand**/
		$app = $this->getSilexApplication();

        $client = $app['client'];
        $timetable_service = $app['timetable.service'];
        $place_service = $app['place.service'];
        $transport_service = $app['transport.service'];

        //array of places names => time to get there
        $times = array(
            array(
                "Test 01" => 0,
                "Test 02" => 15,
                "Test 03" => 50,
            ),
            array(
                "Test 03" => 0,
                "Test 04" => 50,
                "Test 05" => 70,
                "Test 06" => 90,
            )
        );

        //Create timetable node with properties
        $timetable_data = array(
            array(
                'name' => 'midweek-A1',
                'days' => array('mon', 'tues', 'wed', 'thurs', 'friday'),
                'times' => array(
                    '08:00', '08:30',
                    '09:00', '09:30',
                    '10:00', '10:30',
                    '11:00', '11:30',
                    '12:00', '12:30',
                    '13:00', '13:30',
                    '14:00', '14:30',
                    '15:00', '15:30',
                    '16:00', '16:30',
                    '17:00', '17:30',
                    '18:00', '18:30',
                    '19:00', '19:30',
                    '20:00', '20:30',
                ),
            ),
            array(
                'name' => 'midweek-A2',
                'days' => array('mon', 'tues', 'wed', 'thurs', 'friday'),
                'times' => array(
                    '08:00', '08:30',
                    '09:00', '09:30',
                    '10:00', '10:30',
                    '11:00', '11:30',
                    '12:00', '12:30',
                    '13:00', '13:30',
                    '14:00', '14:30',
                    '15:00', '15:30',
                    '16:00', '16:30',
                    '17:00', '17:30',
                    '18:00', '18:30',
                    '19:00', '19:30',
                    '20:00', '20:30',
                ),
            )
        );

        $transport_data = array(
            array(
                'name' => 'A1',
                'type' => 'bus',
            ),
            array(
                'name' => 'A2',
                'type' => 'bus',
            ),
        );

        $timetables = array();
        foreach($timetable_data as $data)
        {
            $timetables[] = $timetable_service->create($data);
        }

        $transports = array();
        foreach($transport_data as $data)
        {
            $transports[] = $transport_service->create($data);
        }

        foreach($timetables as $key => $timetable)
        {
            foreach($times[ $key ] as $name => $time)
            {
                $place = $place_service->find('name', $name);

                if(empty($place)) {
                    $output->writeln(sprintf('Place "%s" doesn\'t exist, can\'t relate.', $name));

                    continue;
                }

                $cypher = sprintf("
                    MATCH (a:Timetable),(b:Place)
                    WHERE id(a) = %s AND id(b) = %s
                    CREATE UNIQUE (a)-[r:STOP_ON_JOURNEY {time : %s}]->(b)
                    RETURN r", $timetable->id, $place->id, $time);

                $data = $client->cypher($cypher);

                $output->writeln(sprintf('Just related %s to %s', $timetable->name, $place->name));
            }

            $transport = $transports[ $key ];

            //Create a service that runs the timetabl
            $cypher = sprintf("
                    MATCH (a:Transport),(b:Timetable)
                    WHERE id(a) = %s AND id(b) = %s
                    CREATE UNIQUE (a)-[r:RUNS_ON]->(b)
                    RETURN r", $transport->id, $timetable->id);

            $data = $client->cypher($cypher);
        }

		$output->writeln('Timetables imported');
	}
}