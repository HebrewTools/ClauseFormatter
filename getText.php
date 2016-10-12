<?php
$book          = $_GET['book'];
$start_chapter = $_GET['start_chapter'];
$start_verse   = $_GET['start_verse'];
$end_chapter   = $_GET['end_chapter'];
$end_verse     = $_GET['end_verse'];

require('clauses.php');

print(json_encode(
	get_text($book, $start_chapter, $start_verse, $end_chapter, $end_verse)));
