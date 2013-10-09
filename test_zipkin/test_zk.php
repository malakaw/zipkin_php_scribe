<?php
 
class ZookeeperDemo extends Zookeeper {
 
  public function watcher( $i, $type, $key ) {
    echo "Insider Watcher\n";
 
    // Watcher gets consumed so we need to set a new one
    $this->get( '/test', array($this, 'watcher' ) );
  }
 
}
echo "--test zookeeper--<br/>";
$zoo = new ZookeeperDemo('10.101.0.91:2181');
echo $zoo->get( '/test');
?>