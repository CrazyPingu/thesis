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
  $features = array();
  $coodinates_array = array();
  $special_table = array();
  $table_name = $file->xpath('//ogr:FeatureCollection/gml:featureMember/*')[0]->getName();
  foreach ($file->xpath('//ogr:FeatureCollection/gml:featureMember/*') as $feature) {
    $attributes = $feature->xpath('./*[not(self::ogr:geometryProperty)]');
    $attributes_array = array();
    $special_array = array();

    // Load attributes
    if (!strcmp($table_name, "Museo") or !strcmp($table_name, "Fermata_bus")) {
      // case Museo, where you have to fill out the table 'punto_di_interesse' and 'info_museo'
      // or Fermata_bus, where you have to fill out the table 'punto_di_interesse' and 'info_fermata'
      foreach ($attributes as $attribute) {
        $name = $attribute->getName();
        $value = json_decode($attribute) ?? (string) $attribute;
        if ($name === 'OBJECTID') {
          $special_array[$name] = $value;
          $attributes_array[$name] = $value;
        } elseif ($name === 'ID_POI' or $name === 'DESCRIZIONE') {
          $attributes_array[$name] = $value;
        } elseif ($name !== 'TIPO') {
          $special_array[$name] = $value;
        }

        $attributes_array['DESCRIZIONE'] ?? $attributes_array['DESCRIZIONE'] = null;
        $table_name === 'Museo' and $special_array['LINK'] ?? $special_array['LINK'] = null;
      }
    } else {
      // normal case, where you just have to fill out the table 'punto_di_interesse'
      foreach ($attributes as $attribute) {
        $attributes_array[$attribute->getName()] = json_decode($attribute) ?? (string) $attribute;
      }
    }

    // Load coordinates
    if (!strcmp($table_name, "Percorso_escursionistico")) {
      $coordinate = $feature->xpath('.//ogr:geometryProperty/gml:LineString/gml:coordinates');

      $result_array = [];

      // Loop through each element in the array
      foreach ($coordinate as $i => $coordinates) {
        // Split the string into individual coordinates
        $coordinates = explode(" ", $coordinates);

        // Loop through each coordinate
        foreach ($coordinates as $j => $coordinate_single) {
          $result_array = load_coordinates($coordinate_single, $result_array, $attributes_array['OBJECTID'], $timezone);
        }
      }
      array_push($coodinates_array, $result_array);
    } else {
      $coordinate = $feature->xpath('.//ogr:geometryProperty/gml:Point/gml:coordinates');
      $coodinates_array = load_coordinates($coordinate[0], $coodinates_array, $attributes_array['OBJECTID'], $timezone);
    }
    !empty($special_array) && array_push($special_table, $special_array);
    array_push($features, $attributes_array);
  }

  // add the $special_table to the $features array if it's not empty
  !empty($special_table) && array_push($features, $special_table);

  array_push($features, $coodinates_array);
  if ($table_name === "Percorso_escursionistico") {
    echo var_dump($coodinates_array) . "<br><br>";
  }
  array_push($features, $table_name);
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
