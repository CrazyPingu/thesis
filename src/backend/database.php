<?php

set_time_limit(0);
ini_set("default_socket_timeout", -1);
ini_set("max_execution_time", 0);

require_once('./utils/read_from_file.php');

class DatabaseHelper
{
  private $db;
  private $batch_size = 7000;
  private $config;
  private $dictionary_table;
  private $dictionary_insert;
  private $dictionary_type;

  /**
   *  Constructor of the DatabaseHelper class from a config.json file in the same root
   */
  public function __construct()
  {
    $this->dictionary_table = require_once('./utils/dictionary_table.php');
    $this->dictionary_insert = require_once('./utils/dictionary_insert.php');
    $this->dictionary_type = require_once('./utils/dictionary_type.php');
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
      if (
        $file_info->isFile() && $file_info->getExtension() === $this->config->extension_dump
        // && $file_info->getFilename() === 'Percorso_escursionistico_ETRS89_UTM32.gml'
        && $file_info->getFilename() !== 'AAASmall.gml'
      ) {
        $start = microtime(true);
        echo 'Loading ' . $file_info->getFilename() . ' file<br>';
        $data_file = read_from_file(simplexml_load_file($this->config->xml_folder_dump . '/' . $file_info), $this->config->utmZone);

        // if (array_pop($data_file) === 'Percorso_escursionistico') {
        //   echo '<br><h1>Finito in ' . (microtime(true) - $start) . ' seconds</h1><br><br>';
        // }
        // break;
        // I need to pop the last element because it's the name of the table
        $table_name = array_pop($data_file);
        if ($table_name === 'Percorso_escursionistico') {
          echo '<br><h1>LETTO in ' . (microtime(true) - $start) . ' seconds</h1><br><br>';
        }

        // I need to pop the last element because it's the identifier of the table
        $identifier = array_pop($data_file);

        // I need to pop the last element because it's the coordinates of the table
        $coodinates = array_pop($data_file);

        // Load the identifier table
        $this->load_table('identificatore', $identifier);
        echo "Caricato identificatore<br>";

        if ($table_name === 'Museo' or $table_name === 'Fermata_bus') {
          $special_array = array_pop($data_file);
          $tables = explode(',', (string) $this->dictionary_table[$table_name]);
          $this->load_table('tipologia', array($table_name));
          $this->load_table($tables[0], $data_file);

          echo 'Loading info table ' . $tables[1] . '<br>';
          $this->load_table(trim($tables[1]), $special_array);
        } elseif ($table_name === 'Percorso_escursionistico') {
          $start  = microtime(true);
          $this->load_table($this->dictionary_table[$table_name], $data_file);
          echo '<br><h1>CARICATO I DATI in ' . (microtime(true) - $start) . ' seconds</h1><br><br>';
        } else {
          $this->load_table('tipologia', array($table_name));
          $this->load_table($this->dictionary_table[$table_name], $data_file);
        }
        $start  = microtime(true);
        echo 'Loading coordinates table<br>';
        $this->load_table('coordinata', $coodinates);
        if($table_name === 'Percorso_escursionistico'){
          echo '<br><h1>CARICATO LE COORDINATE in ' . (microtime(true) - $start) . ' seconds</h1><br><br>';
        }
      }
      echo '<br>';
    }
    echo 'Database loaded';
  }


  /**
   * Load the table given the correct data
   *
   * @param string $table_name the name of the table
   * @param array $data the data to load
   */
  private function load_table(string $table_name, array $data)
  {
    // Calculate the number of parameters to insert in each query
    $number_field = strlen($this->dictionary_type[$table_name]);

    // If the table is 'punto_di_interesse, I need to add the id_type
    if ($table_name === 'punto_di_interesse') {
      $id_type = $this->db->insert_id;

      // I add the id_type to the data array every $number_field elements
      $data = array_reduce($data, function ($result, $value) use ($id_type, $number_field) {
        if ((count($result) + 1) % $number_field == 0 && count($result) > 0) {
          $result[] = $id_type;
        }
        $result[] = $value;
        return $result;
      }, array());

      // I add the last id_type
      $data[] = $id_type;
    }

    // Calculate the number of parameters max for each query
    $batch_parameters = ceil($this->batch_size / $number_field) * $number_field;

    // Calculate the number of data to insert
    $total_rows = count($data);

    // If the number of parameters is less than the max, set the max to the number of parameters
    if ($total_rows < $batch_parameters) {
      $batch_parameters = count($data);
    }

    // Insert the data in the table
    for ($i = 0; $i < $total_rows; $i += $batch_parameters) {

      // Get the parameters to insert
      $params = array_slice($data, $i, $batch_parameters);

      // Get the string of the type of the parameters
      $params_type = str_repeat($this->dictionary_type[$table_name], count($params) / $number_field);

      // Create the query
      $query = 'INSERT INTO ' . $table_name . $this->dictionary_insert[$table_name] . ' VALUES ' .
        str_repeat('(' . str_repeat('?,', $number_field - 1) . '?),', count($params) / $number_field - 1)
        . '(' . str_repeat('?,', $number_field - 1) . '?)';

      // Prepare the query
      $this->prepare_query($query, $params, $params_type, $table_name === 'coordinata');

      // Unset the query
      unset($query);
    }
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
  //            Utils            //
  /////////////////////////////////


  /**
   * Wrapper function for prepare_query that automatically determines the parameter types based on the values passed
   *
   * @param string $query query to execute
   * @param array $params parameters of the query
   * @param string $params_type the string that contains the type of each parameter
   * @param bool $need_transaction if the query needs a transaction
   */
  private function prepare_query(string $query, array $params, string $params_type, bool $need_transaction = false)
  {
    // check if is a select or an insert
    if ($need_transaction) {
    $this->db->autocommit(false);
    $this->db->begin_transaction();
    }

    $stmt = $this->db->prepare($query);
    if (!$stmt) {
      // handle error
      error_log("Prepare failed: " . mysqli_error($this->db));
      return false;
    }

    $stmt->bind_param($params_type, ...$params);
    if (!$stmt->execute()) {
      // handle error
      error_log("Execute failed: " . mysqli_error($this->db));
      return false;
    }

    $stmt->close();
    if ($need_transaction) {
      $this->db->commit();
    }
  }
}