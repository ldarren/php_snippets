#! /usr/bin/php -q
<?php

require dirname(__FILE__).'/nas.class.php';
require dirname(__FILE__).'/nosql.class.php';

define('BUFFER', $argv[1]);
define('EOL', "\n");

$mongo = new NoSQL();
$nas = new Nas();

function movem_to($mongo, $nas){
  $h = opendir(BUFFER);
  if (empty($h)) return;
  while(false !== ($node = readdir($h))){
    $path = BUFFER.$node;
    switch(filetype($path)){
      case 'file':
      $fsize = filesize($path);
      if ($fsize < 24 || $fsize > 524289) break;

      $mongo->save_file($node, $path, $fsize);
      $nas->move_file($node, $path, $fsize);
      break;
    }
    usleep(30000);
  }
  closedir($h);
}

$fd = inotify_init();
$wd = inotify_add_watch($fd, BUFFER, IN_MOVED_TO | IN_CREATE);

$fs = array($fd);
$ws = null;
$es = null;

while (true){
  if (false === ($changes = stream_select($fs, $ws, $es, 86400))){
    echo 'program terminated'.EOL;
  } else {

    if ($changes > 0) {
      $evt = inotify_read($fd);
      print_r($evt);
      echo EOL.'Changes = '.$changes.EOL;
    }

    movem_to($mongo, $nas);

  }
}
