<?php

class UserHelper
{

  private $db;

  private $config;

  public function __construct()
  {
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
   * return the user given the username
   *
   * @param  string $username the username of the user
   * @return array|null return the user or null if the user is not found
   */
  private function get_user(string $username)
  {
    $stmt = $this->db->prepare('SELECT * FROM utente WHERE username = ? LIMIT 1');

    $stmt->bind_param('s', $username);

    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
  }

  /**
   * Login the user given his username and password
   *
   * @param  string $username the username of the user
   * @param  string $password the password of the user
   * @return bool if the user is logged or no
   */
  public function login_user(string $username, string $password)
  {
    $user = $this->get_user($username);

    if ($user) {
      $_SESSION['user'] = $user;
    }

    return $user && password_verify($password, $user['password']);
  }


  /**
   * Log out the user
   */
  public function logout_user()
  {
    $_SESSION['user'] = null;
  }

  /**
   * @return bool return true if the user is logged
   */
  public function logged_user()
  {
    return isset($_SESSION['user']);
  }

  /**
   * @return bool return true if the user is admin
   */
  public function check_admin_logged()
  {
    return $this->logged_user() && $_SESSION['user']['isAdmin'] == 1;
  }


  /**
   * Register the user given his username and password
   *
   * @param  string $username the username of the user
   * @param  string $password the password of the user
   * @param  int $isAdmin if the user is admin or not
   * @return bool true if the user is now registered, else false
   *              if the username is already used
   */
  public function register_user(string $username, string $password, int $isAdmin = 0)
  {
    $user = $this->get_user($username);

    if ($user == null) {

      $stmt = $this->db->prepare('INSERT INTO utente (username, password, isAdmin) VALUES(?,?,?)');

      $crypted_password = password_hash($password, PASSWORD_DEFAULT);

      $stmt->bind_param('ssi', $username, $crypted_password, $isAdmin);

      $stmt->execute();

      $this->login_user($username, $password);

      return true;
    } else {
      return false;
    }
  }

  public function get_favourite()
  {
    $stmt = $this->db->prepare('SELECT idPoi FROM preferiti WHERE idUtente = ?');
    $stmt->bind_param('i', $_SESSION['user']['idUtente']);
    $stmt->execute();

    $result = $stmt->get_result();
    $idPoiArray = $result->fetch_all(MYSQLI_ASSOC);

    $singleArray = array_column($idPoiArray, 'idPoi');

    return $singleArray;
  }


  public function get_favourite_info(string $type = null)
  {
      // Get column names excluding 'tipologia' and 'descrizione'
      $columns_query = "
        SELECT GROUP_CONCAT(COLUMN_NAME) AS columns
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = '" . $this->config->db_name . "' AND
              TABLE_NAME = 'punto_di_interesse' AND
              COLUMN_NAME <> 'tipologia' AND
              COLUMN_NAME <> 'descrizione'";


      $columns_result = $this->db->query($columns_query);
      $columns_data = $columns_result->fetch_assoc();
      $columns = "poi." . $columns_data['columns'];

      // Prepare and execute the main query
      $query = "
          SELECT " . $columns .",
            tip.tipo AS tipologia,
            coor.latitudine,
            coor.longitudine
          FROM preferiti p
          JOIN
            punto_di_interesse poi ON p.idPoi = poi.idPoi
          JOIN
              tipologia tip ON poi.tipologia = tip.idTipologia
          LEFT JOIN
              coordinata coor ON poi.idPoi = coor.idPoi
          WHERE p.idUtente = ?
      ";

      if(isset($type)) {
          $query .= " AND tip.tipo = ?";
      }
      $stmt = $this->db->prepare($query);
      if(isset($type)){
          $stmt->bind_param('is', $_SESSION['user']['idUtente'], $type);
      } else {
          $stmt->bind_param('i', $_SESSION['user']['idUtente']);
      }
      $stmt->execute();

      $result = $stmt->get_result();

      return $result->fetch_all(MYSQLI_ASSOC);
  }


  public function add_favourite(string $idPoi)
  {
    $stmt = $this->db->prepare('INSERT INTO preferiti (idUtente, idPoi) VALUES(?,?)');
    $stmt->bind_param('is', $_SESSION['user']['idUtente'], $idPoi);
    $stmt->execute();
  }

  public function remove_favourite($idPoi)
  {
    $stmt = $this->db->prepare('DELETE FROM preferiti WHERE idUtente = ? AND idPoi = ?');
    $stmt->bind_param('is', $_SESSION['user']['idUtente'], $idPoi);
    $stmt->execute();
  }
}