<?php
require('make.php');

$verses = json_decode($_REQUEST['verses'], true);

$dir = make_dir($verses);

header('Content-type: text/x-tex');
header('Content-Disposition: attachment; filename="result.tex"');
readfile($dir . '/result.tex');

cleanup($dir);
