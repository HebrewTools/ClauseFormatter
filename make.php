<?php
function get_word_text($word) {
  if ($word['deleted'])
    return '';
  $text = trim($word['text']);
  if (in_array('subject', $word['specials']))
    return '\subj{' . $text . '}';
  if (in_array('predicate', $word['specials']))
    return '\pred{' . $text . '}';
  return $text;
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

function cleanup($dir) {
  array_map('unlink', glob($dir . '/*'));
  rmdir($dir);
}

function new_dir() {
  do { $dir = 'tmp/tmp' . rand(); } while (file_exists($dir));
  mkdir($dir);
  return $dir;
}

function make_dir($verses) {
  $dir = new_dir();

  $tex = make_tex_verses($verses);
  
  copy('clauses.sty', $dir . '/clauses.sty');
  
  $format = file_get_contents('format.tex');
  $result = str_replace('%%%CLAUSES%%%', $tex, $format);
  $result = str_replace('%%%FIRSTVERSE%%%', $verses[0]['ref'] - 1, $result);
  file_put_contents($dir . '/result.tex', $result);

  return $dir;
}

function build($dir) {
  exec("cd $dir && PATH=/usr/bin:\$PATH TEXMFHOME=/home/camil/texmf xelatex result.tex",
    $output, $return_var);
  return $return_var;
}

