<?php

require_once('database.php');

$db = new DatabaseHelper();

$args = json_decode($_POST["args"], false);

switch ($args->function) {

  case "load_database":
    echo json_encode($db->load_database());
    break;

  case "get_path":
    echo json_encode(
      array(
        [44.5343768, 10.5664061],
        [45.2035418, 12.0933341],
        [44.3188888, 11.2002901],
        [44.2035418, 12.0933341],
        [44.2035418, 13.0933341],
      )
    );
    break;

  default:
    echo json_encode(array("error" => "Function not found"));
}

?>