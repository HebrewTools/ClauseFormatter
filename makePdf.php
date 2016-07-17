<?php
require('make.php');

$verses = json_decode($_REQUEST['verses'], true);

$dir = make_dir($verses);

$return_var = build($dir);

if ($return_var != 0) {
	header('Content-type: text/plain');
	print('An unknown error occurred.');
} else {
	header('Content-type: application/pdf');
	header('Content-Disposition: attachment; filename="result.pdf"');
	readfile($dir . '/result.pdf');
}

cleanup($dir);
