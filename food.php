<?php
/*
request and display food information
USDA Nutritional Database Wrapper ver 0.1
*/
include('ndbwrap.php');

$ndb = new ndbwrap();

$ndbNumber = '01009'; //Cheese, cheddar

$food = $ndb->getFood($ndbNumber);

echo '<h4>NDB Food Data for ['.$food->report->food->ndbno.'] '.$food->report->food->name.'</h4>';

echo '<h5>Nutrients per 100 gram portion</h5>';

foreach( $food->report->food->nutrients as $nutrient ){
    
    echo '['.$nutrient->nutrient_id.'] '.$nutrient->name.': '.$nutrient->value.$nutrient->unit.'<br>';
    
}

echo '<hr>';

echo 'database version '.$food->report->sr.'<br>';

echo $ndb->USDAcitation.'<br>';
?>
