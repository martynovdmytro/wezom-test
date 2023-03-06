<?php

namespace App\Services;


class ApiService
{
    /**
     * Provides retrieving data from remote api
     *
     * @param  string  $url
     *
     */
    public function __invoke($url) {
        $query = http_build_query(array($url));
        $opts = array('http' =>
                          array(
                              'header' =>
                                  "Content-Type: application/x-www-form-urlencoded\r\n".
                                  "Content-Length: ".strlen($query)."\r\n".
                                  "User-Agent:MyAgent/1.0\r\n",
                              'method' => 'GET',
                              'content' => $query
                          )
        );
        $apiURL = $url;
        $context = stream_context_create($opts);
        $fp = fopen($apiURL, 'rb', false, $context);
        if(!$fp)
        {
            return "error";
        }
        $data = @stream_get_contents($fp);
        if($data == false)
        {
            return "error";
        }

        $decodeData = json_decode($data, true);

        return $decodeData;
    }
}