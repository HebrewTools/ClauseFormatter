<?php
require('make.php');

$verses = json_decode($_REQUEST['verses'], true);

$dir = make_dir($verses);
$dirname = str_replace('tmp/', '', $dir);

$return_var = build($dir);

if ($return_var != 0) {
  header('Content-type: text/plain');
  print('An unknown error occurred.');
} else {
  $zip = new ZipArchive();
  $zip->open($dir . '/clauses.zip', ZipArchive::CREATE);
  $zip->addEmptyDir('clauses');
  $zip->addFile($dir . '/result.tex', 'clauses/text.tex');
  $zip->addFile($dir . '/clauses.sty', 'clauses/clauses.sty');
  $zip->addFile($dir . '/result.pdf', 'clauses/text.pdf');
  $zip->close();
  
  header('Content-type: application/zip');
  header('Content-disposition: attachment; filename="clauses.zip"');
  readfile($dir . '/clauses.zip');
}

cleanup($dir);

