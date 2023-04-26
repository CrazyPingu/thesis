<?php

require_once('database.php');

$db = new DatabaseHelper();

if (!isset($_POST["args"])) {
  echo json_encode(array("error" => "No arguments"));
  exit();
}

$args = json_decode($_POST["args"], false);

switch ($args->function) {

  case "load_database":
    echo json_encode($db->load_database());
    break;

  case "get_path":
    echo json_encode($db->get_path());
    break;

  default:
    echo json_encode(array("error" => "Function not found"));
}

?>