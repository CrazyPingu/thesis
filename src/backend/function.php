<?php

require_once('database.php');

$db = new DatabaseHelper();

$args = json_decode($_POST["args"], false);

switch ($args->function) {

  case "carica_database":
    echo json_encode($db->load_database());
    break;

  default:
    echo json_encode(array("error" => "Function not found"));
}

?>