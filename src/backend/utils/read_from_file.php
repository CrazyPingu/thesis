<?php

require_once('./utils/toll.php');

/**
 * Read the data from a given xml file
 *
 * @package utils
 * @param SimpleXMLElement $file the xml file to read
 * @param string $timezone the timezone of the file
 * @return array the data read from the file, at the last position it contains the name of the table and in the
 *              second last position it contains the coordinates or false if there are no coordinates
 */
function read_from_file(SimpleXMLElement $file, string $timezone)
{
    $features = array();
    $coodinates_array = array();
    $table_name = $file->xpath('//ogr:FeatureCollection/gml:featureMember/*')[0]->getName();
    foreach ($file->xpath('//ogr:FeatureCollection/gml:featureMember/*') as $feature) {
        $coordinate = $feature->xpath('.//ogr:geometryProperty/gml:Point/gml:coordinates');
        $attributes = $feature->xpath('./*[not(self::ogr:geometryProperty)]');
        $attributesArray = array();
        foreach ($attributes as $attribute) {
            $name = $attribute->getName();
            $value = json_decode($attribute);
            if (is_null($value)) {
                $value = (string) $attribute;
            }
            $attributesArray[$name] = $value;
        }
        if (isset($coordinate[0])) {
            $tmp = explode(',', (string) $coordinate[0]);
            $tmp = ToLL(floatval(trim($tmp[1])), floatval(trim($tmp[0])), $timezone);
            array_push(
                $coodinates_array,
                array(
                    'latitudine' => $tmp['lat'],
                    'longitudine' => $tmp['lon'],
                    'OBJECTID' => $attributesArray['OBJECTID']
                )
            );
        }
        array_push($features, $attributesArray);
    }
    if (empty($coodinates_array)) {
        array_push($features, false);
    } else {
        array_push($features, $coodinates_array);
    }

    array_push($features, $table_name);
    return $features;
}