<?php
$start_book     = $_GET['start_book'];
$start_chapter  = $_GET['start_chapter'];
$start_verse    = $_GET['start_verse'];
$end_book       = $_GET['end_book'];
$end_chapter    = $_GET['end_chapter'];
$end_verse      = $_GET['end_verse'];

require('clauses.php');

print(json_encode(get_text($start_book, $start_chapter, $start_verse,
  $end_book, $end_chapter, $end_verse)));

