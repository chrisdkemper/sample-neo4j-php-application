<?php namespace ChrisDKemper\ControllerProvider;

use
    Silex\Application,
    Silex\ControllerProviderInterface,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response
;

class JourneyControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        /*
         * Post request to plan the journey
         */
        $controllers->post('/plan', function(Request $request, Application $app){
            /**Sample:JourneyControllerPlan**/
            $to = $request->get('to');
            $from = $request->get('from');

            $from_node = $app['journey.service']->closestTransport($from['lat'], $from['lon'], 10.0);
            $to_node = $app['journey.service']->closestTransport($to['lat'], $to['lon'], 10.0);

            $response = array(
                'results' => array()
            );

            //If a from station can't be found
            if( ! $from_node) {
                $response['content'] = 'No from station found';

                return $app->json($response);
            }

            //If there isn't a to node
            if( ! $to_node) {
                $response['content'] = 'No to station found';

                return $app->json($response);
            }

            $path = $app['journey.service']->shortestPath($from_node->id, $to_node->id);

            if ( ! $path) {
                $response['content'] = 'No journey found';

                return $app->json($response);
            }

            //Reverse the array as the path is currently backwards
            $path = array_reverse($path);

            $path_length = count($path);

            $stops = array();

            //This works out how many locations their are
            $path_stops = ($path_length - 1) / 4;

            /* Pattern used:
             *
             * place (start location)
             * relationship (time)
             * timetable (used to get the time/transport)
             * relationship (time)
             * place (end location)
             *
             */

            for($i = 0; $i < $path_stops; $i++) {
                $inc = ($i * 4);
                $journey_stop = array();

                //Starts with a place node
                $journey_stop['start_place'] = $path[0 + $inc];
                //Start time total journey time so far
                $journey_stop['journey_start_min'] = (int) $path[1 + $inc]['time'];
                //Timetable for the journey
                $journey_stop['timetable'] = $path[2 + $inc];
                //End time minutes
                $journey_stop['journey_end_min'] = (int) $path[3 + $inc]['time'];
                //Ends with another place node
                //This node will also be the start of the next group
                //If there are any
                $journey_stop['end_place'] = $path[4 + $inc];

                $stops[] = $journey_stop;
            }

            foreach($stops as &$journey)
            {
                //Get the timetable id
                $timetable_id = $journey['timetable']['id'];
                //Get the transport related to the Timetable

                //The relationship direction dictates which way round the sum goes
                //There should be a directon with the timetables,
                $journey['time'] = 0 == $journey_stop['journey_end_min'] ? $journey_stop['journey_start_min'] : $journey_stop['journey_end_min'] - $journey_stop['journey_start_min'];
                $journey['time'] = abs($journey['time']);

                $cypher = sprintf("MATCH (n:Timetable)--(t:Transport) WHERE id(n) = %s RETURN t", $timetable_id);
                $data = $app['client']->cypher($cypher);
                $transport = $data['results'][0]['data'][0]['row'][0];
                $journey['transport'] = $transport;
            }

            $response['results'] = $stops;

            return $app->json($response);
        });

        /*
         * Post request to find the closest
         */
        $controllers->post('/closest', function(Request $request, Application $app){
            /**Sample:JourneyControllerClosest**/
            $place = $app['journey.service']->closestTransport($request->get('lat'), $request->get('lon'), 10.0);

            $data = $place->toArray();

            return $app->json($data);
        });

        /*
         * Get route to serve the points to the frontend
         */

        $controllers->get('/points', function(Request $request, Application $app){
            /**Sample:JourneyControllerPoints**/
            $places = $app['place.service']->all();
            $data = array();
            foreach($places as $place)
            {
                $data[] = $place->toArray();
            }

            return $app->json($data);

        });

        return $controllers;
    }
}