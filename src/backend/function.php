<?php

require_once('database.php');

// Return if no arguments are set
if (!isset($_POST["args"])) {
  echo json_encode(array("error" => "No arguments"));
  exit();
}

// Create the database helper
$db = new DatabaseHelper();

// Decode the arguments
$args = json_decode($_POST["args"], false);

// Redirect the function call
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