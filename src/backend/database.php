<?php

set_time_limit(0);
ini_set("default_socket_timeout", -1);
ini_set("max_execution_time", 0);


require_once('./utils/read_from_file.php');

class DatabaseHelper
{
  private $db;
  private $config;
  private $dictionary_table;
  private $dictionary_insert;

  /**
   *  Constructor of the DatabaseHelper class from a config.json file in the same root
   */
  public function __construct()
  {
    $this->dictionary_table = require_once('./utils/dictionary_table.php');
    $this->dictionary_insert = require_once('./utils/dictionary_insert.php');
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
        && $file_info->getFilename() !== 'Percorso_escursionistico_ETRS89_UTM32.gml'
        && $file_info->getFilename() !== 'AAASmall.gml'
      ) {
        echo 'Loading ' . $file_info->getFilename() . ' file<br>';
        $data_file = read_from_file(simplexml_load_file($this->config->xml_folder_dump . '/' . $file_info), $this->config->utmZone);
        $table_name = array_pop($data_file);
        $identifier = array_pop($data_file);
        $coodinates = array_pop($data_file);

        echo 'Loading ' . $table_name . ' table<br>';

        if ($table_name === 'Museo' or $table_name === 'Fermata_bus') {
          $special_array = array_pop($data_file);
          $this->load_table('identificatore', $identifier);
          $tables = explode(',', (string) $this->dictionary_table[$table_name]);
          $this->load_table($tables[0], $data_file, $this->load_type($table_name));

          echo 'Loading info table ' . $tables[1] . '<br>';
          $this->load_table(str_replace(' ', '', $tables[1]), $special_array);
        } elseif ($table_name === 'Percorso_escursionistico') {
          $this->load_table('identificatore', $identifier);
          echo "Caricato identificatore<br><br>";
          $this->load_table($this->dictionary_table[$table_name], $data_file);
        } else {
          $this->load_table('identificatore', $identifier);
          $this->load_table($this->dictionary_table[$table_name], $data_file, $this->load_type($table_name));
        }
        echo 'Loading coordinates table<br>';
        $this->load_table('coordinata', $coodinates);
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
  private function load_type(string $table_name)
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
  private function load_table(string $table_name, array $data, int $id_type = null)
  {
    $query = 'INSERT INTO ' . $table_name . $this->dictionary_insert[$table_name] . ' VALUES ';


    $number_field = count(explode(',', (string) $this->dictionary_insert[$table_name]));
    $start = microtime(true);

    $query .= str_repeat('(' . str_repeat('?,', $number_field - 1) . '?),', count($data) - 1) . '(' . str_repeat('?,', $number_field - 1) . '?);';

    if ($table_name !== 'coordinata') {
      $value = [];
      foreach ($data as $row) {
        $value = [...$value, ...array_values($row)];
        if (isset($id_type)) {
          $value[] = $id_type;
        }
      }
      $this->prepare_query($query, $value, $table_name);
    } else {
      $this->prepare_query("a", $data, 'coordinata', $number_field);
    }
    echo "<h3>". $table_name." loaded in ". microtime(true) - $start."</h3><br>";
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
   * @return array|int|bool rows of the table or the last id inserted, or false if there was an error
   */
  private function prepare_query(string $query, array $params, $table = null, $num = null)
  {
    if ($table === 'coordinata') {
      // define the batch size
      $batch_size = 10002;

      // get the total number of rows
      $total_rows = count($params);

      // loop through the data in batches
      for ($i = 0; $i < $total_rows; $i += $batch_size) {

        $this->db->autocommit(false);
        $this->db->begin_transaction();

        // get the current batch of data
        $batch_params = array_slice($params, $i, $batch_size);
        $query = 'INSERT INTO ' . $table . $this->dictionary_insert[$table] . ' VALUES ' .
          str_repeat('(' . str_repeat('?,', $num - 1) . '?),', count($batch_params) / $num - 1) . '(' . str_repeat('?,', $num - 1) . '?);';
        // prepare the query for the current batch
        $stmt = $this->db->prepare($query);
        // continue with binding and execution of statement
        $batch_params_type = str_repeat('s', count($batch_params));
        // echo '<br><br>' . count($batch_params) . ' e l"altro' . substr_count($query, '?') . ' e ' . strlen($batch_params_type) . ' e tabella ' . $table . ' <br><br>';
        $stmt->bind_param($batch_params_type, ...$batch_params);
        // execute the statement
        $stmt->execute();
        $stmt->close();
        $this->db->commit();
      }
    } else {
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

      // echo '<br><br>' . count($params) . ' e l"altro' . substr_count($query, '?') . ' e ' . strlen($params_type) . ' e tabella ' . $table . ' <br><br>';
      $stmt = $this->db->prepare($query);
      if (!$stmt) {
        // handle error
        error_log("Prepare failed: " . mysqli_error($this->db));
        return false;
      }

      // continue with binding and execution of statement
      $stmt->bind_param($params_type, ...$params);

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
}