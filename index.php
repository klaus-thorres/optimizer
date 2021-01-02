<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'Model.php';
require 'View.php';
require 'Controller.php';

// Merge $_GET und $_POST
$request = array_merge($_GET, $_POST);
// Create controller object
$Controller = new Controller($request);
//Output the content
echo $Controller->getOutput();
