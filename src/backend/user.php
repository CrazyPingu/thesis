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
}