<?php namespace ChrisDKemper\Client;

class Client
{
	protected $curl_headers = array();
	protected $base_url = 'http://localhost:7474';
	protected $cypher_uri = 'db/data/transaction/commit';
	protected $spatial_uri = 'db/data/ext/SpatialPlugin/graphdb/addSimplePointLayer';
	protected $index_uri = 'db/data/index/node/';
	protected $spatial_node_index = 'db/data/ext/SpatialPlugin/graphdb/addNodeToLayer';

	public function __construct($username, $password, $transport = 'localhost', $port = 7474, $https = false)
	{
        /**Sample:Clientconstruct**/
		//Set the default headers
		$this->curl_headers = array(
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array(
	            'Content-Type: application/json'
	        )
		);

		//Set the base_url
		$this->base_url = sprintf("%s://%s:%s",
			false == $https ? 'http' : 'https',
			$transport,
			$port
		);

		//Set auth header
		$this->curl_headers[CURLOPT_USERPWD] = sprintf("%s:%s", $username, $password);
	}

	public function cypher($cypher_query)
	{
        /**Sample:Cyphermethod**/
		//Set up a statement for the transaction
		$data = array(
            'statements' => array(
                array('statement' => $cypher_query)
            )
        );

        //Use the preset cypher uri to send the query
		return $this->send($this->cypher_uri, $data);
	}

	public function createSpatialIndex($name = "geom", $lat = "lat", $lon  = "lon")
	{
        /**Sample:createSpatialIndex**/
		//Set up a statement for the transaction
		$data = array(
            "layer" => $name,
            "lat"   => $lat,
            "lon"   => $lon
        );

        //Use the preset cypher uri to send the query
		return $this->send($this->spatial_uri, $data);
	}

	public function createCypherIndexForSpatial($name = "geom", $lat = "lat", $lon = "lon")
	{
        /**Sample:createCypherIndexForSpatial**/
		$data = array(
            "name" => $name,
            "config" => array(
                "provider" => "spatial",
                "geometry_type" => "point",
                "lat" => $lat,
                "lon" => $lon
            )
        );

        return $this->send($this->index_uri, $data);
	}

	public function spatialAddNodeToLayer($name, $node)
	{
        /**Sample:spatialAddNodeToLayer**/
		$data = array(
            'layer' => $name,
            'node'  => $node,
        );

        return $this->send($this->spatial_node_index, $data);
	}

	public function getBaseUrl()
	{
		return $this->base_url;
	}

	protected function send($uri, $data)
	{
        /**Sample:clientSend**/
		$data_string = json_encode($data);

		$url = $this->base_url . "/" . $uri;
		$query = curl_init($url);

		//Add the post data to the query
		$this->curl_headers[CURLOPT_HTTPHEADER][] = 'Content-Length: ' . strlen($data_string);

		//Add the headers to the query
		foreach($this->curl_headers as $header => $value)
		{
			curl_setopt($query, $header, $value);
		}

		curl_setopt($query, CURLOPT_POSTFIELDS, $data_string);

		$result = curl_exec($query);
        curl_close($query);

        return json_decode($result, true);
	}
}