<?php

class DatabaseHelper
{
  private $db;
  private $config;

  private $specialTable = array(
    'museo' => 'info_museo',
    'fermata' => 'info_fermata',
    'coordinata' => 'coordinata'
  );

  /**
   *  Constructor of the DatabaseHelper class from a config.json file in the same root
   */
  public function __construct()
  {
    $this->config = json_decode(file_get_contents('config.json'));
    $this->db = new mysqli($this->config->host, $this->config->db_user, $this->config->db_password, $this->config->db_name, $this->config->port);
    if ($this->db->connect_error) {
      die('Connection failed: ' . $this->db->connect_error);
    }
  }

  /**
   *  Delete the database connection
   */
  public function __destruct()
  {
    $this->db->close();
  }


  /////////////////////////////////
  //        Load Database        //
  /////////////////////////////////



  /**
   *  Prepare the database to load the data
   */
  public function loadDatabase()
  {
    $iterator = new DirectoryIterator($this->config->xml_folder_dump);

    $this->truncateDatabase();

    $this->createTable();

    echo "Database created successfully";


    foreach ($iterator as $fileinfo) {
      if ($fileinfo->isFile() && $fileinfo->getExtension() === $this->config->extension_dump) {

        $data = $this->readFromFile(simplexml_load_file($this->config->xml_folder_dump . '/' . $fileinfo));

        // var_dump($data);
      }
    }
  }


  /**
   *  Create the tables of the database if those don't exist
   */
  private function createTable()
  {
    // $table_list = array("punto_di_interesse", "tipologia", "info_museo", "info_fermata", "coordinata", "percorso_escursionistico");
    $this->db->multi_query(file_get_contents($this->config->dump_table));
  }

  /**
   *  Truncate all the tables of the database
   */
  private function truncateDatabase()
  {
    // Disable foreign key checks
    $this->db->query('SET FOREIGN_KEY_CHECKS=0');

    // Truncate tables
    $tables = array();
    $result = $this->db->query('SHOW TABLES');
    while ($row = $result->fetch_array(MYSQLI_NUM)) {
      $tables[] = $row[0];
    }
    foreach ($tables as $table) {
      $this->db->query('TRUNCATE TABLE ' . $table);
    }

    // Enable foreign key checks
    $this->db->query('SET FOREIGN_KEY_CHECKS=1');
  }

  /**
   * Read the data from a given xml file
   *
   * @param SimpleXMLElement $file the xml file to read
   * @return array the data read from the file, at the last position it contains the name of the table
   */
  private function readFromFile(SimpleXMLElement $file)
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
  /////////////////////////////////
  //            Query            //
  /////////////////////////////////

  /**
   *  Execute a query
   *
   *  @param string $query query to execute
   *  @param array $params parameters of the query
   *  @return array result of the query
   */
  private function executeQuery(string $query, $params = null)
  {
    if (empty($query) or is_null($query)) {
      return array();
    }
    $stmt = $this->db->prepare($query);
    if ($params == null)
      return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $types = '';
    foreach ($params as $param) {
      if (is_int($param)) {
        $types .= 'i';
      } else if (is_double($param)) {
        $types .= 'd';
      } else if (is_string($param)) {
        $types .= 's';
      } else {
        $types .= 'b';
      }
    }
    $params = array_merge(array($types), $params);
    $stmt->bind_param(...$params);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  }


  /**
   *  Return the point of interest given the objectId or the id_poi
   *
   *  @param string $type it will be the type of the id (objectId or id_poi)
   *  @param int $id the id of the point of interest
   *  @return array result of the query or an empty array if the query fails
   */
  public function getPointOfInterest(string $type, int $id)
  {
    // Case that the type is not valid
    if ($type != 'objectId' && $type != 'id_poi') {
      return array();
    }
    $query = 'SELECT * FROM punto_di_interesse, tipologia WHERE ' . $type . ' = ?';

    $stmt = $this->db->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Case that the point of interest doesn't exist
    if ($result->num_rows == 0) {
      return array();
    }
    $result = $result->fetch_all(MYSQLI_ASSOC)[0];

    // ! TODO: ADD THE SPECIAL TABLES
    return $result;
  }
}