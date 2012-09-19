<?php
// Sanity check
if(!is_dir("build")) {
    die("\n*** Run 'make wurbel' first.\n");
}

set_include_path("..");
require_once("tentackle/tentackle.php");
require_once("NetworkStatus.php");


$dburl = "mysql://outsmart:outsmart@localhost/uwe";

$db = TentackleFactory::fromURL($dburl)->newDb();

// Get the current time
date_default_timezone_set('America/New_York');
$dateTime = new DateTime("now");

// Saving a new entity
for($i = 0; $i < 10; $i++) {
    $ns = new NetworkStatus($db);
    $ns->setWhen(date_format($dateTime, 'Y-m-d H:i:s'));
    $ns->setInterfaceName("eth0");
    $ns->setRxTotalBytes(1000000);
    $ns->setRxRelativeBytes($i);
    $ns->save();
}

// list 5 entities, skipping 2
echo "List 5 entities, skipping 2\n";
foreach($ns->selectByInterface(5, 2, 'eth0') as $ns) {
    echo "Network status: $ns\n";
}


// delete the 'eth0' entries
//echo "Delete 'eth0' entries\n";
//$ns->deleteByInterface('eth0');

?>
