<?php
/**
 * Read the data from a given xml file
 *
 * @param SimpleXMLElement $file the xml file to read
 * @return array the data read from the file, at the last position it contains the name of the table
 */
function read_from_file(SimpleXMLElement $file)
{
    $features = array();
    $table_name = $file->xpath('//ogr:FeatureCollection/gml:featureMember/*')[0]->getName();
    foreach ($file->xpath('//ogr:FeatureCollection/gml:featureMember/*') as $feature) {
        $coordinates = $feature->xpath('.//ogr:geometryProperty/gml:Point/gml:coordinates');
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
        if (isset($coordinates[0])) {
            $attributesArray['coordinates'] = (string) $coordinates[0];
        }
        array_push($features, $attributesArray);
    }
    array_push($features, $table_name);
    return $features;
}