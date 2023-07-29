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
      $this->config->db_password, $this->config->db_name, $this->config->port ?? 3306
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
  //            Utils            //
  /////////////////////////////////

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
      $this->prepare_query($query, $params, $params_type);

      // Unset the query
      unset($query);
    }
  }

  /**
   *  Create the tables of the database if those don't exist
   */
  private function create_table()
  {
    $sql = file_get_contents('./database/dump_table.sql');
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

  /**
   * Wrapper function for prepare_query that automatically determines the parameter types based on the values passed
   *
   * @param string $query query to execute
   * @param array $params parameters of the query
   * @param string $params_type the string that contains the type of each parameter
   */
  private function prepare_query(string $query, array $params, string $params_type)
  {

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
  }

  ///////////////////
  //   Function   //
  //////////////////


  /**
   * Empty the database and load the data from the xml files
   * @return array an array that contains the name of the file
   *    (at the position 'file_name'), the type of table (at the position 'type') and the
   *    time to load the data (at the position 'time') for each file read
   */
  public function load_database()
  {
    $result_array = array();

    $this->truncate_database();

    $this->create_table();

    // now that the database is empty we can load the data
    $iterator = new DirectoryIterator($this->config->xml_folder_dump);

    foreach ($iterator as $file_info) {
      if (
        $file_info->isFile() && $file_info->getExtension() === 'gml'
        // && $file_info->getFilename() !== 'Percorso_escursionistico_ETRS89_UTM32.gml'
      ) {
        // Start the timer
        $time_start = microtime(true);

        // Read the data from the file
        $data_file = read_from_file(simplexml_load_file($this->config->xml_folder_dump . '/' . $file_info), $this->config->utmZone);

        // I need to pop the last element because it's the name of the table
        $table_name = array_pop($data_file);

        // I need to pop the second last element because it's the identifier of the table
        $identifier = array_pop($data_file);

        // I need to pop the third last element because it's the coordinates of the table
        $coodinates = array_pop($data_file);

        // Load the identifier table
        $this->load_table('identificatore', $identifier);

        // Load the table
        if ($table_name === 'Museo' or $table_name === 'Fermata_bus') {
          $special_array = array_pop($data_file);
          $tables = explode(',', (string) $this->dictionary_table[$table_name]);
          $this->load_table('tipologia', array($table_name));
          $this->load_table($tables[0], $data_file);

          $this->load_table(trim($tables[1]), $special_array);
        } elseif ($table_name === 'Percorso_escursionistico') {
          $this->load_table($this->dictionary_table[$table_name], $data_file);
        } else {
          $this->load_table('tipologia', array($table_name));
          $this->load_table($this->dictionary_table[$table_name], $data_file);
        }

        // Load the coordinates table
        $this->load_table('coordinata', $coodinates);

        // I add the result to the array
        $result_array[] = array(
          'file_name' => $file_info->getFilename(),
          'type' => $table_name,
          'time' => number_format(microtime(true) - $time_start, 2, '.', '')
        );
      }
    }
    return $result_array;
  }


  /**
   * Method to obtain the path of the excursionist
   * @return array an array that contains the path of the excursionist
   */
  public function get_path()
  {
    // I obtain all of the id of the excursionist path
    $ids = $this->db->query("
      SELECT idPercorso
      FROM percorso_escursionistico
    ")->fetch_all(MYSQLI_NUM);

    // For each id I obtain the coordinates
    foreach ($ids as $id) {
      $result[] = $this->db->query("
        SELECT c.latitudine, c.longitudine, pe.difficolta
        FROM percorso_escursionistico pe, coordinata c, identificatore id
        WHERE pe.idPercorso = id.idPoi and id.idPoi = c.idPoi
          and pe.idPercorso = '" . $id[0] . "'
        ORDER BY c.idCoordinata
      ")->fetch_all(MYSQLI_NUM);
    }

    return $result;
  }


  /**
   * Method to obtain all the marker
   * @return array an array that contains another array at the position
   *    of the marker's type that contains the coordinates of the marker;
   *    Watch out that some field might be empty or null
   */
  public function get_marker()
  {
    return $this->db->query("
      SELECT pt.idPoi, pt.descrizione, t.tipo, c.latitudine, c.longitudine,
        IF(f.idPoi IS NOT NULL, f.gestore, '') AS gestore_fermata,
        IF(f.idPoi IS NOT NULL, f.linea, '') AS linea_fermata,
        IF(m.idPoi IS NOT NULL, m.nome, '') AS nome_museo,
        IF(m.idPoi IS NOT NULL, m.globalId, '') AS globalId_museo,
        IF(m.idPoi IS NOT NULL, m.link, '') AS link_museo
      FROM punto_di_interesse pt

      JOIN tipologia t ON t.idTipologia = pt.tipologia
      JOIN identificatore id ON id.idPoi = pt.idPoi
      JOIN coordinata c ON c.idPoi = pt.idPoi
      LEFT JOIN info_fermata f ON f.idPoi = pt.idPoi
      LEFT JOIN info_museo m ON m.idPoi = pt.idPoi

      ORDER BY t.tipo;
    ")->fetch_all(MYSQLI_ASSOC);
  }
}