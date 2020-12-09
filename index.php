<?php
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

class Loader{

    private $parser;
    private $requests;
    private $routes;
    private $routeStops = [];
    private $mostcount = array();
    private $leastcount = array();
    private $routesDictionary = [];
    private $connectsDictionary = [];

    function __construct(){
        $this->parser = new Parser();
        $this->requests = new Requests();
    }
    
    
    //load the type 0, 1 routes
    function getSubwayRoutes(){   
        $routes = $this->requests->trigger("routes?filter[type]=0,1");
        $this->routes = $this->parser->attribCheckerFirst($routes, "data");
    }

    //load stops for a specified route
    function getStops($id){
        $stops = $this->requests->trigger("stops?filter[route]=".$id);

        return $this->parser->attribCheckerFirst($stops, "data");
    }


    function getSubwayStops(){

        foreach($this->routes as $key=>$row):{
            var_dump($this->parser->attributeChecker($row, "long_name"));
            $stops = $this->getStops($this->parser->attribCheckerFirst($row, "id"));
            $stopslength = intval(sizeof($stops));                
            if($key == 0) {
                $this->leastcount = array("count" => $stopslength);
                $this->mostcount = array("count" => $stopslength);
            }
            $this->setMaxMin($row, $stopslength);
            $this->routeStops[$this->parser->attribCheckerFirst($row, "id")] = $this->formatRouteStops($stops);
            
        }endforeach;
        
        var_dump($this->mostcount["long_name"]." ".$this->mostcount["count"] );
        var_dump($this->leastcount["long_name"]." ".$this->leastcount["count"] );     
        $this->createDictionary();
   }
   //update stops max/min
   function setMaxMin($row, $stopslength){
        if($stopslength> $this->mostcount["count"]) {
            $this->mostcount = array( 
                "count" => $stopslength,
                "long_name"=> $this->parser->attributeChecker($row, "long_name")
            );
        }

        if($stopslength < $this->leastcount["count"]) {
            $this->leastcount = array( 
                "count" => $stopslength,
                "long_name"=> $this->parser->attributeChecker($row, "long_name")
            );
        }
   }
   function formatRouteStops($stops){
       $formatted = [];
        foreach($stops as $key=>$stop):{
            $formatted[] = $this->parser->attributeChecker($stop,"name");
        }endforeach;
        return $formatted;
   }
   //Creates a list of stops and their respective routes
   function createDictionary(){
        $routesDictionary = [];
        $routeStops = $this->routeStops;
        foreach($routeStops as $key=>$stops):{
            for($j=0;$j<sizeof($stops);$j++ ){
                $routesDictionary[$stops[$j]][] = $key;
            }
        }endforeach;

        $this->routesDictionary = $routesDictionary;
   }
   //gets the connectiong stops for routes, filter dictionary and get stops with more than one route
   function getConnects(){
        $routesDictionary = $this->routesDictionary;   
        $connects = []; 
        foreach($routesDictionary as $stop=>$routes):{
            if(sizeof($routes) == 1 ) continue;
            
            $connects[$stop] = $routes;
        }endforeach;

        $this->connectsDictionary = $connects;
   }
   //get route between two paths
   function mapRoute(){
        list($stop, $stop2) = explode(' ', readline('Enter stops, seperate with a space: ')); 
        $map = [];
        $set = (isset($stop) && $stop2) ? 1: 0;
        
        if(!$set) return;

        $from = (isset($this->routesDictionary[$stop]))? $this->routesDictionary[$stop]: 0;
        $to =  (isset($this->routesDictionary[$stop2]))? $this->routesDictionary[$stop2]: 0;
        
        if($from && $to){
            foreach($from as $row1):{
                foreach($to as $row2):{
                    //scenario 1
                    //best route
                    if($row1 == $row2) { 
                        $map[] = $row1;
                        break;
                    }
                    
                    //scenario 2
                    //search for the presence of both routes in the connecting dictionary
                    $searchres = $this->searchConnections(array($row1, $row2));
                    
                    if($searchres)$map[] = $this->searchConnections(array($row1, $row2));
    
                }endforeach;
            }endforeach;
        }

        print_r("Connecting stops are: ");
        $print = $map? $map:"none";
        print_r($print);
       
   }
   //searched Connections library to see routes that share a stop
    private function searchConnections($searcharr){
        $common = [];
        foreach($this->connectsDictionary as $stop=>$routes):{
            $searchfrom = in_array($searcharr[0], $routes);
            $searchto = in_array($searcharr[1], $routes);

            if($searchfrom && $searchto) $common[$stop] = $searcharr;
        }endforeach;

        return $common;
    }
   
}

//execution commands
$loader = new Loader();
//ideally should be executed from different commands/scipt files
$loader->getSubwayRoutes();
$loader->getSubwayStops();
$loader->getConnects();
$loader->mapRoute();

?>