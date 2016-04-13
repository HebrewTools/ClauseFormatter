<?php
function get_word_text($word) {
  if ($word['deleted'])
    return '';
  return trim($word['text']);
}

function make_tex_clause($clause) {
  $tex  = '';
  $tex .= '\clausei' . $clause['indent'] . '{';
  $tex .= implode(' ', array_map('get_word_text', $clause['words'])) . '}';
  return $tex;
}

function make_tex_clauses($verse) {
  $tex = '';
  if (count($verse['clauses']) == 1) {
    $tex .= make_tex_clause($verse['clauses'][0]) . "\n";
  } else {
    $tex .= '\clauses{' . "\n";
    foreach ($verse['clauses'] as $clause) {
      $tex .= make_tex_clause($clause) . "\n";
    }
    $tex .= "}\n";
  }
  return $tex;
}

function make_tex_verses($verses) {
  return implode("\n", array_map('make_tex_clauses', $verses));
}

$verses = json_decode($_REQUEST['verses'], true);

$tex = make_tex_verses($verses);

do { $dir = 'tmp/tmp' . rand(); } while (file_exists($dir));
mkdir($dir);
copy('clauses.sty', $dir . '/clauses.sty');

$format = file_get_contents('format.tex');
$result = str_replace('%%%CLAUSES%%%', $tex, $format);
file_put_contents($dir . '/result.tex', $result);

exec("cd $dir && PATH=/usr/bin:\$PATH TEXMFHOME=/home/camil/texmf xelatex result.tex",
    $output, $return_var);
if ($return_var != 0) {
  header('Content-type: text/plain');
  print('An unknown error occurred.');
} else {
  header('Content-type: application/pdf');
  readfile($dir . '/result.pdf');
}

array_map('unlink', glob($dir . '/*'));
rmdir($dir);

