<?php namespace ChrisDKemper\Service;

use
    ChrisDKemper\Entity\Place
;

class JourneyService
{
    protected
        $client
    ;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function closestPlace($lat, $lon, $km = 5.0)
    {
        /**Sample:serviceJourneyClosestPlace**/
        $cypher = sprintf('START n=node:geom("withinDistance:[%s,%s,%s]") RETURN n, id(n), labels(n)', $lat, $lon, $km);

        $data = $this->client->cypher($cypher);
        $row = $data['results'][0]['data'][0]['row'];

        $node = $row[0];
        $node['id'] = $row[1];
        $node = array_merge($node, $row[2]);
        $node['label'] = $row[3];
        $place = new Place($node);

        return $place;
    }

    public function closestTransport($lat, $lon, $km = 10.0, $type = '')
    {
        /**Sample:serviceJourneyClosestTransport**/
        if( ! empty($type)) {
            $type = $type . ":";
        }

        $cypher = sprintf('START place=node:geom("withinDistance:[%s,%s,%s]") WITH place MATCH (place)-[:LOCATED_AT]-(transport%s) RETURN place, id(place), labels(place), transport, labels(transport) LIMIT 1', $lat, $lon, number_format($km, 1), $type);

        $data = $this->client->cypher($cypher);
        $result = $data['results'][0]['data'];

        if(empty($result)) {
            return false;
        }

        $row = $result[0]['row'];

        $node = $row[0];

        $node = array_merge($node, $row[3]);
        $node['id'] = $row[1];

        $node['label'] = $row[4];
        $place = new Place($node);

        return $place;
    }

    public function shortestPath($from_nid, $to_nid)
    {
        /**Sample:serviceJourneyShortestPath**/
        $cypher = sprintf('MATCH (from:Place),(to:Place), p = shortestPath((from)-[:STOP_ON_JOURNEY*..15]-(to)) WHERE id(to) = %s AND id(from) = %s RETURN p', $from_nid, $to_nid);

        $result = $this->client->cypher($cypher);

        $data = $result['results'][0]['data'];

        if( ! empty($data)) {
            return $data[0]['row'][0];
        }

        return false;
    }
}