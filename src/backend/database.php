<?php

class DatabaseHelper{
    private $db;

    private $specialTable = array(
        ['museo'] => 'info_museo',
        ['fermata'] => 'info_fermata',
        ['coordinata'] => 'coordinata'
    );
    /**
     *  Constructor of the DatabaseHelper class
     *
     *  @param string $hostname hostname of the database
     *  @param string $username username of the database
     *  @param string $password password of the database
     *  @param string $database name of the database
     *  @param int $port port of the database
     */
    public function __construct(string $hostname,string $username,string $password,string $database,int $port = 3306) {
        $this->db = new mysqli($hostname, $username, $password, $database, $port);
        if ($this->db->connect_error) {
            die('Connection failed: ' . $this->db->connect_error);
        }
    }

    /**
     *  Delete the database connection
     */
    public function __destruct() {
        $this->db->close();
    }

    /**
     * Truncate all the tables of the database
     */
    public function truncateDatabase() {
			$tables = array();
			$result = $this->db->query('SHOW TABLES');
			while($row = $result->fetch_array(MYSQLI_NUM)) {
				$tables[] = $row[0];
			}
			foreach($tables as $table) {
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
    private function executeQuery(string $query, ...$params = null) {
        if(empty($query) or is_null($query)) {
            return array();
        }
        $stmt = $this->db->prepare($query);
        if($params == null)
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $types = '';
        foreach($params as $param) {
            if(is_int($param)) {
                $types .= 'i';
            } else if(is_double($param)) {
                $types .= 'd';
            } else if(is_string($param)) {
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
     *  @param int $id it will be the objectId or the id_poi
     *  @param string $type it will be the type of the id (objectId or id_poi)
     *  @return array result of the query
     */
    public function getPointOfInterest(int $id, string $type) {
        if(strcmp($type, 'objectId') == 0) {
            $query = 'SELECT * FROM punto_di_interesse WHERE objectid = ?';
        } else if(strcmp($type, 'id_poi') == 0) {
            $query = 'SELECT * FROM punto_di_interesse WHERE id_poi = ?';
        } else {
            return array();
        }
        $result = $this->executeQuery($query, $id);
        if(in_array($result[0]['tipologia'], array_keys($this->specialTable))) {
            $result[0][$result[0]['tipologia']] = $this->executeQuery('
            SELECT * FROM ' . $this->specialTable[$result[0]['tipologia']] . '
            WHERE objectid = ?', $result[0]['objectid']);
        }
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}