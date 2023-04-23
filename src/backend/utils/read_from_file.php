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
  $identifier = [];
  $coodinates_array = [];
  $special_table = [];
  $table_name = $file->xpath('//ogr:FeatureCollection/gml:featureMember/*')[0]->getName();

  foreach ($file->xpath('//ogr:FeatureCollection/gml:featureMember/*') as $feature) {
    $attributes = $feature->xpath('./*[not(self::ogr:geometryProperty)]');
    $flag_special_table = false;
    $attributes_array = [];
    $special_array = [];
    $identifier_array = [];

    // Load attributes
    foreach ($attributes as $attribute) {
      $name = $attribute->getName();
      $value = json_decode($attribute) ?? (string) $attribute;

      if ($name === 'ID_POI' || $name === 'ID_PERCORSO') {
        $special_array[$name] = $value;
        $attributes_array[$name] = $value;
        $identifier_array[$name] = $value;
      } elseif ($name === 'OBJECTID') {
        $identifier_array[$name] = $value;
      } elseif ($name === 'DESCRIZIONE') {
        $attributes_array[$name] = $value;
      } elseif ($name !== 'TIPO') {
        $flag_special_table = true;
        $special_array[$name] = $value;
      }
    }

    if ($table_name === 'Percorso_escursionistico') {
      $special_array['SEGNAVIA'] ??= null;
      $special_array['TEMPO_ANDATA'] ??= null;
      $special_array['LINK'] ??= null;
      $special_array['ALTRO_SEGNAVIA'] ??= null;
      $special_array['TEMPO_RITORNO'] ??= null;
      $special_array['GESTORE'] ??= null;
      $special_array['LUNGHEZZA'] ??= 0;
    } elseif ($table_name === 'Fermata_bus') {
      $attributes_array['DESCRIZIONE'] ??= null;
    } elseif ($table_name === 'Museo') {
      $special_array['LINK'] ??= null;
      $attributes_array['DESCRIZIONE'] ??= null;
    }

    // Load coordinates
    if ($table_name === 'Percorso_escursionistico') {
      // Case Percorso_escursionistico
      $coordinate = $feature->xpath('.//ogr:geometryProperty/gml:LineString/gml:coordinates');
      foreach ($coordinate as $i => $coordinates) {
        $coordinates = explode(" ", $coordinates);
        foreach ($coordinates as $j => $coordinate) {
          $coodinates_array = load_coordinates($coordinate, $coodinates_array, $attributes_array['ID_PERCORSO'], $timezone);
        }
      }
    } else {
      // Case normal
      $coordinate = $feature->xpath('.//ogr:geometryProperty/gml:Point/gml:coordinates');
      $coodinates_array = load_coordinates($coordinate[0], $coodinates_array, $attributes_array['ID_POI'], $timezone);
    }
    // Check if $special_array has any keys other than 'ID_POI'
    if ($flag_special_table) {
      $special_table = [...$special_table, ...array_values($special_array)];
    }

    $identifier = [...$identifier, ...array_values($identifier_array)];
    $features = [...$features, ...array_values($attributes_array)];

    unset($attributes_array);
    unset($special_array);
    unset($identifier_array);
  }

  if ($table_name === 'Percorso_escursionistico') {
    $features = $special_table;
  } else if (!empty($special_table)) {
    $features[] = $special_table;
  }

  $features[] = $coodinates_array;
  $features[] = $identifier;
  $features[] = $table_name;

  return $features;
}




/**
 * Load the coordinates of a feature
 *
 * @package utils
 * @param string $coordinate the coordinates of the feature
 * @param array $coordinate_array the array of coordinates
 * @param string $idPoi the id of the feature
 * @param string $timezone the timezone of the file
 * @return array the array of coordinates
 */
function load_coordinates(string $coordinate, array $coordinate_array, string $idPoi, string $timezone)
{
  sscanf($coordinate, "%f,%f", $longitude, $latitude);
  $tmp = ToLL(floatval(trim($latitude)), floatval(trim($longitude)), $timezone);
  $coordinate_array[] = $tmp['lat'];
  $coordinate_array[] = $tmp['lon'];
  $coordinate_array[] = $idPoi;

  return $coordinate_array;
}