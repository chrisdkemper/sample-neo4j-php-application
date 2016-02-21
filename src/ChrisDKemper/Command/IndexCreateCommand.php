<?php
namespace ChrisDKemper\Command;

use
	Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Input\InputOption,
 	Symfony\Component\Console\Output\OutputInterface
;
class IndexCreateCommand extends Command
{
	protected function configure()
    {
        $this
            ->setName('index:create')
            ->setDescription('Creates the needed indexes')
        ;
    }
	protected function execute(InputInterface $input, OutputInterface $output)
	{
        /**Sample:IndexCreateCommand**/
		$app = $this->getSilexApplication();

        $client = $app['client'];

        $spatial_data = $client->createSpatialIndex('geom');

        $cypher_index = $client->createCypherIndexForSpatial('geom');

		$output->writeln('Indexes added');
	}
}