<?php
class NoSQL{
  const HOST = 'ec2-5-161-82-39.compute-1.amazonaws.com:27017';
  const DB = 'dbname';
  const TBL = 'colname';
  const USER = 'username';
  const PASSWD = 'password';

  private $_mongo, $_fs;

  public function __construct(){
    $this->connect();
  }

  public function __destruct(){
    $this->disconnect();
  }

  public function connect(){
    if ($this->is_connected()) return;
    $this->_mongo = new Mongo(self::HOST, array('connect'=>true, 'username'=>self::USER, 'password'=>self::PASSWD, 'db'=>self::DB));
    $db = $this->_mongo->selectDB(self::DB);
    $this->_fs = $db->getGridFS(self::TBL);
  }

  public function disconnect(){
    if ($this->is_connected())
      $this->_mongo->close();
  }

  public function is_connected(){
    return (isset($this->_mongo) && $this->_mongo->connected);
  }

  private function gen_id($userid, $dow, $h){
    return new MongoId(sprintf("%020d%02d%02d", $userid, $dow, $h));
  }

  public function save_file($userid, $file, $fsize){
    $f = fopen($file, 'r');
    $bytes = fread($f, $fsize);
    fclose($f);

    $dow = date('N');
    $daynew = $this->gen_id($userid, $dow, 0);
    $hournew = $this->gen_id($userid, $dow%3, date('h'));
    $allnew = $this->gen_id($userid, 0, 0);
    $d = new MongoDate();

    if (!$this->is_connected()) $this->connect();

    $this->_fs->remove(array('_id'=>array('$in'=>array($daynew,$hournew,$allnew))));

    $this->_fs->storeBytes($bytes, array('_id'=>$daynew, 'date'=>$d, 'owner'=>$userid));
    $this->_fs->storeBytes($bytes, array('_id'=>$hournew, 'date'=>$d, 'owner'=>$userid));
    $this->_fs->storeBytes($bytes, array('_id'=>$allnew, 'date'=>$d, 'owner'=>$userid));
  }
}
