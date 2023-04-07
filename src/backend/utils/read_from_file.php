<?php

require_once('./utils/toll.php');

/**
 * Read the data from a given xml file
 *
 * @package utils
 * @param SimpleXMLElement $file the xml file to read
 * @param string $timezone the timezone of the file
 * @return array the data read from the file; at the last position it contains the name of the table and in the
 *               second last position it contains the coordinates. If the table is Museo or Fermata_bus at the third last position
 *               it contains the array that contains the data for the table 'info_museo' or 'info_fermata'
 */
function read_from_file(SimpleXMLElement $file, string $timezone)
{
  $features = [];
  $coodinates_array = [];
  $special_table = [];
  $table_name = $file->xpath('//ogr:FeatureCollection/gml:featureMember/*')[0]->getName();

  foreach ($file->xpath('//ogr:FeatureCollection/gml:featureMember/*') as $feature) {
    $attributes = $feature->xpath('./*[not(self::ogr:geometryProperty)]');
    $attributes_array = [];
    $special_array = [];

    // Load attributes
    foreach ($attributes as $attribute) {
      $name = $attribute->getName();
      $value = json_decode($attribute) ?? (string) $attribute;

      if ($name === 'OBJECTID') {
        $special_array[$name] = $value;
        $attributes_array[$name] = $value;
      } elseif ($name === 'ID_POI' || $name === 'DESCRIZIONE') {
        $attributes_array[$name] = $value;
      } elseif ($name !== 'TIPO') {
        $special_array[$name] = $value;
      }
    }

    if ($table_name === 'Fermata_bus' || $table_name === 'Museo')
      $attributes_array['DESCRIZIONE'] ??= null;

    if ($table_name === 'Museo')
      $special_array['LINK'] ??= null;

    // Load coordinates
    if ($table_name === 'Percorso_escursionistico') {
      $coordinate = $feature->xpath('.//ogr:geometryProperty/gml:LineString/gml:coordinates');
      $result_array = [];

      foreach ($coordinate as $i => $coordinates) {
        $coordinates = explode(" ", $coordinates);

        foreach ($coordinates as $j => $coordinate_single) {
          $result_array = load_coordinates($coordinate_single, $result_array, $attributes_array['OBJECTID'], $timezone);
        }
      }

      $coodinates_array[] = $result_array;
    } else {
      $coordinate = $feature->xpath('.//ogr:geometryProperty/gml:Point/gml:coordinates');
      $coodinates_array = load_coordinates($coordinate[0], $coodinates_array, $attributes_array['OBJECTID'], $timezone);
    }

    if (!empty(array_diff_key($special_array, array_flip(['OBJECTID'])))) {
      array_push($special_table, $special_array);
    }

    $features[] = $attributes_array;
  }

  if (!empty($special_table)) {
    $features[] = $special_table;
  }


  $features[] = $coodinates_array;
  $features[] = $table_name;
  return $features;
}




/**
 * Load the coordinates of a feature
 *
 * @package utils
 * @param string $coordinate the coordinates of the feature
 * @param array $coordinate_array the array of coordinates
 * @param string $objectId the id of the feature
 * @param string $timezone the timezone of the file
 * @return array the array of coordinates
 */
function load_coordinates(string $coordinate, array $coordinate_array, string $objectId, string $timezone)
{
  sscanf($coordinate, "%f,%f", $latitude, $longitude);
  $tmp = ToLL(floatval(trim($latitude)), floatval(trim($longitude)), $timezone);
  $coordinate_array[] = array(
    'latitudine' => $tmp['lat'],
    'longitudine' => $tmp['lon'],
    'OBJECTID' => $objectId
  );
  return $coordinate_array;
}