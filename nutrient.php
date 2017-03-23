<?php
/*
request and display specific nutritional information for foods
USDA Nutritional Database Wrapper ver 0.1
*/
include('ndbwrap.php');

$ndb = new ndbwrap();

$ndb->addNutrient('304');
$ndb->addNutrient('305');
$ndb->addNutrient('306');
$ndb->addNutrient('307');

/*$ndb->addFoodGroup('0100');
$ndb->addFoodGroup('0500');*/

$nutrients = $ndb->getNutrient(10);

echo '<h4>NDB Nutrient Data</h4>';

echo '<h5>Showing '.$nutrients->report->end.' of '.$nutrients->report->total.' items found</h5>';

foreach( $nutrients->report->foods as $food ){
    
    echo '<strong>['.$food->ndbno.'] '.$food->name.'</strong><br>';
    
    echo 'Serving Size: '.$food->measure.'<br>';
    
    foreach( $food->nutrients as $nutrient ){
        
        echo '['.$nutrient->nutrient_id.'] '.$nutrient->nutrient.' '.$nutrient->value.$nutrient->unit.'<br>';
        
    }
    
}

echo '<hr>';

echo 'database version '.$nutrients->report->sr.'<br>';

echo $ndb->USDAcitation.'<br>';
?>
