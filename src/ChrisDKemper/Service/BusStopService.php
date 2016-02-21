<?php namespace ChrisDKemper\Service;

use
	ChrisDKemper\Entity\BusStop
;

class BusStopService extends Service
{
    public function create($properties = array())
    {
        /**Sample:serviceBusStopCreate**/
        $node = $this->repository->create($properties);

        if($node) {
            return new BusStop($node);
        }

        return false;
    }

	public function one($id)
    {
        /**Sample:serviceBusStopOne**/
        $node = $this->repository->one($id);

        if($node) {
            return new BusStop($node);
        }

        return false;
    }

    public function find($property, $value)
    {
        /**Sample:serviceBusStopFind**/
        $node = $this->repository->find($property, $value);

        if($node) {
            return new BusStop($node);
        }

        return false;
    }

    public function all()
    {
        /**Sample:serviceBusStopAll**/
    	$nodes = $this->repository->all();

        if(empty($nodes)) {
            return array();
        }

    	$busstops = array();

    	foreach ($nodes as $node)
    	{
    		$busstops[] = new BusStop($node);
    	}

    	return $busstops;
    }
}