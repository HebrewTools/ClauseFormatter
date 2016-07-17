<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require('make.php');

$verses = json_decode($_REQUEST['verses'], true);

$dir = make_dir($verses);

$return_var = build($dir);

if ($return_var != 0) {
  header('Content-type: text/plain');
  print('An unknown error occurred.');
} else {
	header('Content-type: text/x-tex');
	header('Content-Disposition: attachment; filename="result.tex"');
  readfile($dir . '/result.tex');
}

cleanup($dir);

