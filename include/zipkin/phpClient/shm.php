<?php
class shared {

    private $shm_id;
    private $shm_key = 0xff3;
    private $shm_size = 1024;

    function __construct() {
        $this->shm_id = shmop_open($this->shm_key, "c", 0644, $this->shm_size) or die('fail init..');
    }

    function __get($name) {
        $buf = shmop_read($this->shm_id, 0, $this->shm_size);
        $buf = unserialize(trim($buf));
        if ($name == '_all')
            return $buf;
        return isset($buf[$name]) ? $buf[$name] : false;
    }

    function __set($name, $value) {
        $buf = shmop_read($this->shm_id, 0, $this->shm_size);
        $buf = unserialize(trim($buf));
        $buf[$name] = $value;
        $buf = serialize($buf);
        if (strlen($buf) >= $this->shm_size)
            die('no more empty cache');
        shmop_write($this->shm_id, $buf, 0) or die('fail write');
    }

    function del() {
        shmop_delete($this->shm_id);
    }

}
//$shmopobj = new shared();

//if (isset($shmopobj->database) || $shmopobj->database == null) {
//    echo "</br>";
//    $shmopobj->database = "localhost";
//} else {
//    echo "get data";
//    echo "</br>";
//   echo $shmopobj->database;
//}
//$shmopobj->del();
?>
