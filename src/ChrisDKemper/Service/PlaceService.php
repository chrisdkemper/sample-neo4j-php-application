<?php namespace ChrisDKemper\Service;

use
    ChrisDKemper\Repository\PlaceRepository,
	ChrisDKemper\Entity\Place,
    ChrisDKemper\Client\Client
;

class PlaceService extends Service
{
    public function __construct(PlaceRepository $repository, Client $client)
    {
        $this->client = $client;
        $this->repository = $repository;
    }

    public function create($properties = array())
    {
        return new Place($this->repository->create($properties));
    }

	public function one($id)
    {
        $node = $this->repository->one($id);

        if($node) {
            return new Place($node);
        }

        return false;
    }

    public function find($property, $value)
    {
        $place = $this->repository->find($property, $value);

        if (empty($place)) {
           return array();
        }

        return new Place($place);
    }

    public function all()
    {
        /**Sample:servicePlaceAll**/
    	$nodes = $this->repository->all();

        if(empty($nodes)) {
            return array();
        }

    	$places = array();

    	foreach ($nodes as $node)
    	{
            $place = new Place($node);
            $cypher = sprintf("MATCH (n)-[:LOCATED_AT]-(t) WHERE id(n) = %s RETURN labels(t)", $place->id);
            $data = $this->client->cypher($cypher);

            foreach($data['results'][0]['data'] as $row)
            {
                $label = $row['row'][0][0];

                if( ! in_array($label, $place->label)) {
                    $place->label[] = $label;
                }
            }

            $places[] = $place;
        }

    	return $places;
    }
}