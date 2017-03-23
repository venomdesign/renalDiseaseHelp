<?php
/*
request and display data lists
USDA Nutritional Database Wrapper ver 0.1
*/
include('ndbwrap.php');

$ndb = new ndbwrap();

$list = $ndb->getList('g',0);

echo '<h4>NDB List of Food Groups</h4>';

foreach( $list->list->item as $item ){
    
    echo '['.$item->id.'] '.$item->name.'<br>';
    
}

echo '<hr>';

echo 'database version '.$list->list->sr.'<br>';

echo $ndb->USDAcitation.'<br>';
?>
