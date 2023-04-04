<?php

require_once('./utils/read_from_file.php');
require_once('./utils/toll.php');

class DatabaseHelper
{
  private $db;
  private $config;

  private $dictionary_table;

  /**
   *  Constructor of the DatabaseHelper class from a config.json file in the same root
   */
  public function __construct()
  {
    $this->dictionary_table = require_once('./utils/dictionary_table.php');
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
      if ($file_info->isFile() && $file_info->getExtension() === $this->config->extension_dump && $file_info != 'Percorso_escursionistico_ETRS89_UTM32.gml') { // TODO: ADD FILE PERCORSO ESCURSIONISTICO
        $data_file = read_from_file(simplexml_load_file($this->config->xml_folder_dump . '/' . $file_info));
        $table_name = array_pop($data_file);
        $coodinates_array = array();
        foreach ($data_file as $data) {
          if (isset($data['coordinates'])) {
            $tmp = explode(',', $data['coordinates']);
            $data['coordinates'] = ToLL(floatval(trim($tmp[1])), floatval(trim($tmp[0])), $this->config->utmZone);
            array_push(
              $coodinates_array,
              array(
                'latitudine' => $data['coordinates']['lat'],
                'longitudine' => $data['coordinates']['lon'],
                'OBJECTID' => $data['OBJECTID']
              )
            );
          }
        }

        // ! ADJUST THIS PART FOR BETTER CLARITY
        if ($table_name != 'Museo' and $table_name != 'Fermata_bus') {
          echo 'Loading ' . $table_name . ' table<br>';
          $table_name_sql = $this->dictionary_table[$table_name];
          $this->load_table($table_name_sql, $data_file, $this->load_type($table_name));
          echo 'Loading coordinates table<br>';
          // load the coordinates table
          $this->load_table('coordinata', $coodinates_array);
          // break;
        }

      }
    }
    echo 'Database loaded';
  }

  /**
   * Create an entry in the tipologia table if it doesn't exist
   *
   * @param string $table_name the name of the table
   * @return int the id of the type
   */
  private function load_type($table_name)
  {
    $result = $this->prepare_query("SELECT * FROM tipologia WHERE tipo = ?;", array($table_name));

    if (count($result) > 0) {
      return $result[0]['idTipologia'];
    }

    return $this->prepare_query("INSERT INTO tipologia (tipo) VALUES (?);", array($table_name));
  }

  /**
   * Load the table given the correct data
   *
   * @param string $table_name the name of the table
   * @param array $data the data to load
   * @param int $id_type the id of the table "tipologia", if it's not a poi table it's null
   */
  private function load_table($table_name, $data, $id_type = null)
  {
    if($table_name !== 'coordinata') {
      $query = 'INSERT INTO ' . $table_name . ' VALUES ';
    }else{
      $query = 'INSERT INTO ' . $table_name . '(latitudine, longitudine, objectId) VALUES ';
    }

    $value = array();
    foreach ($data as $row) {
      $query .= '(';
      foreach ($row as $cell) {
        if ($table_name !== 'punto_di_interesse' || $cell !== $row['coordinates']) {
          $query .= '?,';
          $value[] = $cell;
        }
      }
      if ($id_type) {
        $query .= '?,';
      }
      $query = substr($query, 0, -1);
      $query .= '),';
      if ($id_type) {
        array_push($value, $id_type);
      }
    }

    $query = substr($query, 0, -1);

    $this->db->autocommit(false);

    $this->db->begin_transaction();

    $result = $this->prepare_query($query, $value);

    $this->db->commit();

    echo $result . '<br>';
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
   * Wrapper function for prepare_query that automatically determines the parameter types based on the values passed
   *
   * @param string $query query to execute
   * @param array $params parameters of the query
   * @return array|int|bool rows of the table or the last id inserted, or false if there was an error
   */
  private function prepare_query($query, $params)
  {
    // Determine parameter types based on the values passed
    $params_type = "";
    foreach ($params as $param) {
      if (is_int($param)) {
        $params_type .= "i";
      } elseif (is_float($param)) {
        $params_type .= "d";
      } elseif (is_string($param)) {
        $params_type .= "s";
      } else {
        $params_type .= "b";
      }
    }

    $stmt = $this->db->prepare($query);
    if (!$stmt) {
      // handle error
      error_log("Prepare failed: " . mysqli_error($this->db));
      return false;
    }

    $bindParams = [$params_type];
    foreach ($params as &$param) {
      $bindParams[] = &$param;
    }
    call_user_func_array([$stmt, 'bind_param'], $bindParams);

    if (!$stmt->execute()) {
      // handle error
      error_log("Execute failed: " . mysqli_error($this->db));
      return false;
    }

    $result = $stmt->get_result();
    $stmt->close();

    if (is_bool($result)) {
      return $this->db->insert_id;
    }

    return $result->fetch_all(MYSQLI_ASSOC);
  }


}
