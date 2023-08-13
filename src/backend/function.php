<?php

session_start();

require_once('database.php');
require_once('user.php');

// Return if no arguments are set
if (!isset($_POST['args'])) {
  echo json_encode(array('error' => 'No arguments'));
  exit();
}

// Create the database helper
$db = new DatabaseHelper();
$userHelper = new UserHelper();

// Decode the arguments
$args = json_decode($_POST['args'], false);

// Redirect the function call
switch ($args->function) {

  case 'load_database':
    $output = json_encode($db->load_database());
    $userHelper->register_user('admin', 'admin', 1);
    echo $output;
    break;

  case 'get_path':
    echo json_encode($db->get_path());
    break;

  case 'get_path_info':
    echo json_encode($db->get_path_info($args->path_id));
    break;

  case 'get_path_near':
    echo json_encode($db->get_path_near($args->lat, $args->lng));
    break;

  case 'get_marker':
    echo json_encode($db->get_marker());
    break;

  case 'user_logged':
    echo json_encode($userHelper->logged_user());
    break;

  case 'login_user':
    echo json_encode($userHelper->login_user($args->username, $args->password));
    break;

  case 'logout_user':
    echo json_encode($userHelper->logout_user());
    break;

  case 'register_user':
    echo json_encode($userHelper->register_user($args->username, $args->password));
    break;

  case 'get_type':
    echo json_encode($db->get_type());
    break;

  case 'check_admin_logged':
    echo json_encode($userHelper->check_admin_logged());
    break;

  case 'get_favourite':
    echo json_encode($userHelper->get_favourite());
    break;

  case 'get_favourite_info':
    echo json_encode($userHelper->get_favourite_info());
    break;

  case 'get_favourite_info_filtered':
    echo json_encode($userHelper->get_favourite_info($args->type));
    break;

  case 'remove_favourite':
    echo json_encode($userHelper->remove_favourite($args->path_id));
    break;

  case 'add_favourite':
    echo json_encode($userHelper->add_favourite($args->path_id));
    break;

  case 'get_poi_field':
    echo json_encode($db->get_poi_field($args->id_poi));
    break;

  case 'add_poi_field':
    echo json_encode($db->add_poi_field($args->id_poi, $args->column, $args->value));
    break;

  case 'remove_poi_field':
    echo json_encode($db->remove_poi_field($args->column));
    break;

  default:
    echo json_encode(array('error' => 'Function not found'));
}

?>
