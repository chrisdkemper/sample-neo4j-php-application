<?php namespace ChrisDKemper\Service;

use
	ChrisDKemper\Entity\Timetable
;

class TimetableService extends Service
{
    public function create($properties = array())
    {
        return new Timetable($this->repository->create($properties));
    }

	public function one($id)
    {
        $node = $this->repository->one($id);

        if($node) {
            return new Timetable($node);
        }

        return false;
    }

    public function find($property, $value)
    {
        $timetable = $this->repository->find($property, $value);

        if (empty($timetable)) {
           return array();
        }

        return new Timetable($timetable);
    }

    public function all()
    {
    	$nodes = $this->repository->all();

        if(empty($nodes)) {
            return array();
        }

    	$timetables = array();

    	foreach ($nodes as $node)
    	{
    		$timetables[] = new Timetable($node);
    	}

    	return $timetables;
    }
}