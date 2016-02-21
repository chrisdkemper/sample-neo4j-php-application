<?php namespace ChrisDKemper\Entity;

class Entity
{
    public function __construct($properties)
    {
        foreach($properties as $key => $value)
        {
            $this->{$key} = $value;
        }
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}