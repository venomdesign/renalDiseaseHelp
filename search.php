<?php
/*
search and display foods that match keywords query
USDA Nutritional Database Wrapper ver 0.1
*/
include('ndbwrap.php');

$ndb = new ndbwrap();

$query = 'chickpea flour';

$search = $ndb->getSearch($query);

echo '<h4>NDB Food Search for ['.$search->list->q.'] '.'</h4>';

echo '<h5>Showing '.$search->list->end.' of '.$search->list->total.' items found</h5>';

foreach( $search->list->item as $item ){
    
    echo '['.$item->ndbno.'] '.$item->name.'<br>';
    
}

echo '<hr>';

echo 'database version '.$search->list->sr.'<br>';

echo $ndb->USDAcitation.'<br>';
?>
