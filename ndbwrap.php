<?php
/*
ndpwrap class - USDA Nutritional Database Wrapper
version 0.1 beta 10/1/2015

API reference at http://ndb.nal.usda.gov/ndb/api/doc

Copyright (c) 2015, Wagon Trader

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class ndbwrap{
    
    //*********************************************************
    // Settings
    //*********************************************************
    
    //Your data.gov API key
    //Available at http://ndb.nal.usda.gov/ndb/api/doc (select sign up now in Gaining Access section)
    private $datagovKey = 'TaY2PcFE7GL1aPyoSvemcCxEZq2BWdXi2TtJZ2Ij';
    
    //json or xml
    //currently only json is supported
    private $ndbFormat = 'json';
    
    //Recommended citation from the USDA
    public $USDAcitation = 'U.S. Department of Agriculture, Agricultural Research Service. USDA National Nutrient Database for Standard Reference, Release . Nutrient Data Laboratory Home Page, http://www.ars.usda.gov/nutrientdata';
    
    //URL to REST API
    private $ndbDomain = 'http://api.nal.usda.gov/ndb/';
    
    private $requestResult = array();
    
    private $nutrientList = array();
    
    private $foodGroupList = array();
    
    /*
    method:  getNutrient
    usage:   getNutrient([int max=50][,int offset=0][,string sort='f'][string ndbNumber=null][,int subset=0][,bool assoc=false]);
    params:  max = maximum number of records to retrieve
             offset = first record to retrieve for paged results
             sort = sort records by
                f = food name
                c = nutrient content (sorted on first nutrient supplied)
             ndbNumber = limit results to this single food item
             subset = flag for food set to query
                0 = all foods
                1 = only commonly consumed USA foods
             assoc = flag to return array instead of an object
    
    This method uses the nutrients endpoint to get requested nutritional information for food.
    You must first supply at least one, and up to 20, nutrients to retrieve by using the addNutrient method.
    You may also limit the results to specific food groups by adding at least one, and up to 10, food groups
    by using the addFoodGroup method.
    
    returns: object of request results or multi-dimensional array if assoc flag set to true
    */
    public function getNutrient($max=50,$offset=0,$sort='f',$nbdNumber='',$subset=0,$assoc=false){
        
        if( empty($this->nutrientList) ){
            return null;
        }
        
        $nutrientQuery = '';
        foreach( $this->nutrientList as $nutrient ){
            $nutrientQuery .= '&nutrients='.$nutrient;
        }
        
        $foodGroupQuery = '';
        foreach( $this->foodGroupList as $foodGroup ){
            $foodGroupQuery .= '&fg='.$foodGroup;
        }
        
        $nbdNumberQuery = ( empty($ndbNumber) ) ? '' : '&nbdno='.$nbdNumber;
        
        $request = $this->ndbDomain.'nutrients/?format='.$this->ndbFormat.$nutrientQuery.$foodGroupQuery.$nbdNumberQuery.'&max='.$max.'&offset='.$offset.'&sort='.$sort.'&subset='.$subset.'&api_key='.$this->datagovKey;
        
        $return = $this->sendRequest($request,$assoc);
        
        return $return;
    }
    
    /* 
    method:  addNutrient
    usage:   addNutrient(string nutrient);
    params:  nutrient = nutrient ID to add
    
    This method will add the nutrient to the nutrientList property for use with getNutrient method.
    
    returns: void
    */
    public function addNutrient($nutrient){
        $this->nutrientList[] = $nutrient;
    }
    
    /* 
    method:  clearNutrientList
    usage:   clearNutrientList(void);
    params:  none
    
    This method will reset the nutrientList property for use with getNutrient method.
    
    returns: void
    */
    public function clearNutrientList(){
        $this->nutrientList = null;
    }
    
    /* 
    method:  addFoodGroup
    usage:   addFoodGroup(string foodGroup);
    params:  foodGroup = food group ID to add
    
    This method will add the food group to the foodGroupList property for use with getNutrient method.
    
    returns: void
    */
    public function addFoodGroup($foodGroup){
        $this->foodGroupList[] = $foodGroup;
    }
    
    /* 
    method:  clearFoodGroupList
    usage:   clearFoodGroupList(void);
    params:  none
    
    This method will reset the foodGroupList property for use with getNutrient method.
    
    returns: void
    */
    public function clearFoodGroupList(){
        $this->foodGroupList = null;
    }
    
    /*
    method:  getFood
    usage:   getFood(string ndbNumber[,string reportType='b'][,bool assoc=false]);
    params:  ndbNumber = nutritional database number for food item
             reportType = type of information to retrieve
                b = basic
                f = full
                s = stats
             assoc = flag to return array instead of an object
    
    This method uses the reports endpoint to return nutritional information of the requested food item.
    
    returns: object of request results or multi-dimensional array if assoc flag set to true
    */
    public function getFood($nbdNumber,$reportType='b',$assoc=false){
        $request = $this->ndbDomain.'reports/?format='.$this->ndbFormat.'&ndbno='.$nbdNumber.'&type='.$reportType.'&api_key='.$this->datagovKey;
        $return = $this->sendRequest($request,$assoc);
        return $return;
    }
    
    /*
    method:  getList
    usage:   getList([string listType='f'][,int max=0][,int offset=0][,string sort='f'][,bool assoc=false]);
    params:  listType = type of list to retrieve
                f = food
                n = all nutrients
                ns = specialty nutrients
                nr = standard release nutrients
                g = food group
             max = maximum number of records to retrieve (0 = all)
             offset = first record to retrieve for paged results
             sort = sort records by
                n = name
                id = id number
             assoc = flag to return array instead of an object
    
    This method uses the list endpoint to return the specified list type.
    
    returns: object of request results or multi-dimensional array if assoc flag set to true
    */
    public function getList($listType='f',$max=0,$offest=0,$sort='n',$assoc=false){
        $request = $this->ndbDomain.'list/?format='.$this->ndbFormat.'&lt='.$listType.'&max='.$max.'&offset='.$offest.'&sort='.$sort.'&api_key='.$this->datagovKey;
        $return = $this->sendRequest($request,$assoc);
        return $return;
    }
    
    /*
    method:  getSearch
    usage:   getSearch(string query[,int max=50][,int offset=0][,string sort='f'][string foodGroup=null][,bool assoc=false]);
    params:  query = search keywords
             max = maximum number of records to retrieve
             offset = first record to retrieve for paged results
             sort = sort records by
                r = relevance
                n = food name
             foodGroup = limit results to this food group
             assoc = flag to return array instead of an object
    
    This method uses the search endpoint to return foods with the provided keyword(s).
    
    returns: object of request results or multi-dimensional array if assoc flag set to true
    */
    public function getSearch($query,$max=50,$offset=0,$sort='r',$foodGroup='',$assoc=false){
        $request = $this->ndbDomain.'search/?format='.$this->ndbFormat.'&q='.urlencode($query).'&max='.$max.'&offest='.$offset.'&sort='.$sort.'&fg='.$foodGroup.'&api_key='.$this->datagovKey;
        $return = $this->sendRequest($request,$assoc);
        return $return;
    }
    
    /* 
    method:  sendRequest
    usage:   sendRequest(string request);
    params:  request = full endpoint for REST request
             assoc = flag to return array instead of an object
    
    This method sends the REST request and decodes the response.
    
    returns: object of request results or multi-dimensional array if assoc flag set to true
    */
    public function sendRequest($request,$assoc){
        $this->requestResult = file_get_contents($request);
        $return = json_decode($this->requestResult,$assoc);
        return $return;
    }
}
?>