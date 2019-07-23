<?php
/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 23.07.2019
 * Time: 19:43
 */

namespace App\Services;


class XMLConverter
{
    private $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function xmlToArray()
    {
        if(!$this->isXml($this->file)) {
            throw new \Exception('File is not xml');
        }
        $route = [];
        $get = file_get_contents($this->file);
        $file = new \SimpleXMLElement($get);
        foreach ($file->AirSegments->AirSegment as $segment) {
            $route[] = [
                'departure' => ['date' => reset($segment->Departure['Date']), 'time' => reset($segment->Departure['Time'])],
                'arrival' => ['date' => reset($segment->Arrival['Date']), 'time' => reset($segment->Arrival['Time'])],
                'board' => reset($segment->Board['City']),
                'Off' => reset($segment->Off['City'])
            ];
        }
        return $route;
    }

    public function isXml($filename)
    {
        return pathinfo($filename, PATHINFO_EXTENSION) == 'xml';
    }

}