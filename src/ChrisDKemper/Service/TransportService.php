<?php namespace ChrisDKemper\Service;

use
	ChrisDKemper\Entity\Transport
;

class TransportService extends Service
{
    public function create($properties = array())
    {
        return new Transport($this->repository->create($properties));
    }

	public function one($id)
    {
        $node = $this->repository->one($id);

        if($node) {
            return new Transport($node);
        }

        return false;
    }

    public function find($property, $value)
    {
        $transport = $this->repository->find($property, $value);

        if (empty($transport)) {
           return array();
        }

        return new Transport($transport);
    }

    public function all()
    {
    	$nodes = $this->repository->all();

        if(empty($nodes)) {
            return array();
        }

    	$transports = array();

    	foreach ($nodes as $node)
    	{
    		$transports[] = new Transport($node);
    	}

    	return $transports;
    }
}