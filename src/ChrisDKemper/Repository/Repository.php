<?php namespace ChrisDKemper\Repository;

use
    ChrisDKemper\Client\Client
;

/**
* The base repository
*/
class Repository
{
    protected
        $client,
        $label = ''
    ;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function create($properties)
    {
        /**Sample:repositoryCreate**/
        $query_data = array();

        foreach($properties as $key => $value)
        {
            $value = is_string($value) ? sprintf('"%s"', $value) : $value;

            if(is_array($value)) {
                if(is_numeric($value[0])) {
                    $value = sprintf('[%s]', implode(',', $value));
                } else {
                    $value = sprintf('["%s"]', implode('", "', $value));
                }
            }

            $query_data[] = sprintf('%s : ', $key) . $value;
        }

        $query_string = implode(", ", $query_data);

        //Run the create query
        $cypher = sprintf("CREATE (n:%s {%s}) RETURN id(n);", $this->getLabelQuery(), $query_string);

        $data = $this->client->cypher($cypher);

        $id = $data['results'][0]['data'][0]['row'][0];

        //Update the node to have a self refercing id
        //Run the create query
        $cypher = sprintf("MATCH (n) WHERE id(n) = %s SET n.id = id(n) RETURN n, id(n), labels(n);", $id);

        $data = $this->client->cypher($cypher);

        $node = $data['results'][0]['data'][0]['row'][0];
        $node['id'] = $data['results'][0]['data'][0]['row'][1];
        $node['label'] = $data['results'][0]['data'][0]['row'][2];

        return $node;
    }

    public function one($id)
    {
        /**Sample:repositoryOne**/
        $query_string = sprintf("MATCH (n:%s) WHERE id(n) = %s RETURN n, id(n), labels(n);", $this->getLabelQuery(), $id);

        $data = $this->client->cypher($query_string);

        if(empty($data['results'][0]['data'])) {
            return array();
        }

        $node = $data['results'][0]['data'][0]['row'][0];
        $node['id'] = $data['results'][0]['data'][0]['row'][1];
        $node['label'] = $data['results'][0]['data'][0]['row'][2];

        return $node;
    }

    public function all()
    {
        /**Sample:repositoryAll**/
        $query_string = sprintf("MATCH (n:%s) RETURN n, id(n), labels(n);", $this->getLabelQuery());

        $data = $this->client->cypher($query_string);

        $nodes = array();

        foreach($data['results'][0]['data'] as $row)
        {
            $node = $row['row'][0];
            $node['id'] = $row['row'][1];
            $node['label'] = $row['row'][2];
            $nodes[] = $node;
        }

        return $nodes;
    }

    public function find($property, $value)
    {
        /**Sample:repositoryFind**/
        if(empty($value)) {
            return array();
        }

        if(is_array($value)) {
            if(is_numeric($value[0])) {
                $value_string = sprintf('IN [%s]', implode(' ,', $value));
            } else {
                $value_string = sprintf('IN [\'%s\']', implode('\' ,\'', $value));
            }
        } else {
            if(is_int($value)) {
                $value_string = sprintf(' = %s', $value);
            } else {
                $value_string = sprintf(' = \'%s\'', $value);
            }

        }

        $query_string = sprintf("MATCH (n:%s) WHERE n.%s %s RETURN n, id(n), labels(n);", $this->getLabelQuery(), $property, $value_string);

        $data = $this->client->cypher($query_string);

        if(empty($data['results'][0]['data'])) {
            return array();
        }

        $node = $data['results'][0]['data'][0]['row'][0];
        $node['id'] = $data['results'][0]['data'][0]['row'][1];
        $node['label'] = $data['results'][0]['data'][0]['row'][2];

        return $node;
    }

    private function getLabelQuery()
    {
        return is_array($this->label) ? implode(":", $this->label) : $this->label;
    }
}