<?php

require_once('./utils/read_from_file.php');
require_once('./utils/toll.php');

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
    $this->db = new mysqli(
      $this->config->host, $this->config->db_user,
      $this->config->db_password, $this->config->db_name, $this->config->port
    );
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
   *  Empty the database and load the data from the xml files
   */
  public function load_database()
  {
    $this->truncate_database();

    $this->create_table();

    // now that the database is empty we can load the data
    $iterator = new DirectoryIterator($this->config->xml_folder_dump);

    foreach ($iterator as $file_info) {
      if ($file_info->isFile() && $file_info->getExtension() === $this->config->extension_dump) {
        $data_file = read_from_file(simplexml_load_file($this->config->xml_folder_dump . '/' . $file_info));
        $table_name = array_pop($data_file);
        $coodinates_array = array();
        foreach ($data_file as $data) {
          if (isset($data['coordinates'])) {
            $tmp = explode(',', $data['coordinates']);
            $data['coordinates'] = ToLL(floatval(trim($tmp[1])), floatval(trim($tmp[0])), $this->config->utmZone);
            array_push($coodinates_array, array('OBJECTID' => $data['OBJECTID'], 'coordinates' => $data['coordinates']));
          }
        }
        $this->load_table($this->load_type($table_name), $data_file);
        $this->insert_coordinates($coodinates_array);
        break;
      }
    }
    echo 'Database loaded';
  }

  /**
   * Create an entry in the tipologia table if it doesn't exist
   *
   * @param string $type the type of the table
   */
  private function load_type($type)
  {
    $result = $this->prepare_query("SELECT * FROM tipologia WHERE tipo = ?;", "s", array($type));

    if (count($result) > 0) {
      return $result[0]['idTipologia'];
    }

    return $this->prepare_query("INSERT INTO tipologia (tipo) VALUES (?);", "s", array($type));
  }


  /**
   * Load the data from the xml files into the database
   *
   * @param int $id_type the id of the table "tipologia"
   * @param array $data_file the data to load
   */
  private function load_table($id_type, $data_file)
  {
    $query = "INSERT INTO punto_di_interesse(objectId,id_poi,descrizione,tipologia) VALUES ";
    $array_load = array();
    $array_type = '';
    foreach ($data_file as $row) {
      $query .= "(?, ?, ?, ?),";
      $array_type .= 'issi';
      array_push($array_load, $row['OBJECTID'], $row['ID_POI'], $row['DESCRIZIONE'], $id_type);
    }
    $this->db->autocommit(false);
    $this->db->begin_transaction();

    $query = substr($query, 0, -1);
    $stmt = $this->db->prepare($query);
    $stmt->bind_param($array_type, ...$array_load);
    $stmt->execute();
    $stmt->close();

    $this->db->commit();
  }

  private function insert_coordinates($data)
  {
    $sql = "INSERT INTO coordinata (objectid, latitudine, longitudine) VALUES ";

    $type_prepare = '';
    $value_concatenate = array();
    foreach ($data as $row) {
      $sql .= "(?, ?, ?),";
      $type_prepare .= 'iss';
      array_push($value_concatenate, $row['OBJECTID'], $row['coordinates']['lat'], $row['coordinates']['lon']);
    }
    $sql = substr($sql, 0, -1);

    $this->db->autocommit(false);

    $this->db->begin_transaction();

    $stmt = $this->db->prepare($sql);

    $stmt->bind_param($type_prepare, ...$value_concatenate);

    $stmt->execute();

    $stmt->close();

    $this->db->commit();
  }
  /**
   *  Create the tables of the database if those don't exist
   */
  private function create_table()
  {
    $sql = file_get_contents($this->config->dump_table);
    if ($result = $this->db->multi_query($sql)) {
      do {
        if ($result = $this->db->store_result()) {
          $result->free();
        }
      } while ($this->db->more_results() && $this->db->next_result());
    }
  }

  /**
   *  Truncate all the tables of the database
   */
  private function truncate_database()
  {
    // Disable foreign key checks
    $this->db->query('SET FOREIGN_KEY_CHECKS=0;');

    // Truncate tables
    $tables = array();
    $result = $this->db->query('SHOW TABLES;');
    while ($row = $result->fetch_array(MYSQLI_NUM)) {
      $tables[] = $row[0];
    }
    foreach ($tables as $table) {
      $this->db->query('TRUNCATE TABLE ' . $table . ';');
    }

    // Enable foreign key checks
    $this->db->query('SET FOREIGN_KEY_CHECKS=1;');
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
  private function execute_query(string $query, $params = null)
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
  public function get_point_of_interest(string $type, int $id)
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

  /////////////////////////////////
  //            Utils            //
  /////////////////////////////////


  /**
   *  Return the of rows of a table
   *
   *  @param string $query query to execute
   *  @param string $params_type type of the parameters like 'issi'
   *  @param array $params parameters of the query
   *  @return array|int rows of the table or the last id inserted
   */
  private function prepare_query($query, $params_type, $params)
  {
    $stmt = $this->db->prepare($query);
    $stmt->bind_param($params_type, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if(is_bool($result)){
      return $this->db->insert_id;
    }
    return $result->fetch_all(MYSQLI_ASSOC);
  }
}