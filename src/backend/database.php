<?php

class DatabaseHelper
{
  private $db;

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
    $config = json_decode(file_get_contents('config.json'));
    $this->db = new mysqli($config->host, $config->db_user, $config->db_password, $config->db_name, $config->port);
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

  /**
   * Truncate all the tables of the database
   */
  public function truncateDatabase()
  {
    $tables = array();
    $result = $this->db->query('SHOW TABLES');
    while ($row = $result->fetch_array(MYSQLI_NUM)) {
      $tables[] = $row[0];
    }
    foreach ($tables as $table) {
      $this->db->query('TRUNCATE TABLE ' . $table);
    }
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