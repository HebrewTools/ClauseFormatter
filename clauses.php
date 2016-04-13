<?php
function get_book_index($name) {
  $fh = fopen('book_names.txt', 'r');
  do {
    $line = explode("\t", trim(fgets($fh)));
  } while ($line[1] != $name && !feof($fh));
  fclose($fh);
  if ($line[1] == $name) {
    return $line[0];
  } else {
    return null;
  }
}

function get_all_books() {
  $fh = fopen('book_names.txt', 'r');
  $books = [];
  while (!feof($fh)) {
    $line = explode("\t", trim(fgets($fh)));
    $books[$line[0]] = $line[1];
  }
  fclose($fh);
  return $books;
}

function get_text($sbook, $schap, $sverse, $ebook, $echap, $everse) {
  $text = [];

  $fh = fopen('wlc_utf8.txt', 'r');
  do {
    $line = explode("\t", fgets($fh));
  } while (($line[0] != $sbook || $line[1] != $schap || $line[2] != $sverse)
            && !feof($fh));
  
  if ($line[0] != $sbook) {
    fclose($fh);
    return null;
  }

  $i = 0;
  $text[] = ['verse' => $line[2], 'text' => $line[5]];
  while (($line[0] != $ebook || $line[1] != $echap || $line[2] != $everse)
          && !feof($fh) && $i < 100) {
    $line = explode("\t", fgets($fh));
    $text[] = ['verse' => $line[2], 'text' => $line[5]];
  }

  fclose($fh);

  return $text;
}

