<?php
define('FILENAME','game.bin');

class Nas{
  const NAS_SIZE = 3;

  public function __construct(){}
  public function __destruct(){}

  private function _get_path($id){
    return sprintf("/nas_%01d/%d/", $id%self::NAS_SIZE, $id );
  }

  public function move_file($id, $file, $fsize){
    $path = $this->_get_path($id);

    if(!is_dir($path )) mkdir($path, 0777, true);
    $dst_path = $path.FILENAME.date('YmdHis');
    if ( !copy($file, $dst_path)) {
      throw new Exception("Failed to move $file");
    }
  }
}

