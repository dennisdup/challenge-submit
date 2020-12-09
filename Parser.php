<?php
class Parser{

    private $schema = ['$data', '$row', '$attributes'];
    
    //first degree attributes
    function attribCheckerFirst($row,$field){
        try{            
            if(!empty($row) ) {
                if(property_exists($row, $field)) return $row->$field;
                else new Exception(0);
            }else throw new Exception(0);
            
        }catch(Exception $e){
            print_r("Unexpected data");
            exit;
        }        
    }
    //secod degree attributes
    function attributeChecker($row,$field){
        try{
            if(property_exists($row->attributes, $field)) return $row->attributes->$field;
            else return 0;
        }catch(Exception $e){
            print_r("Unexpected data");
            exit;
        }        
    }
}

?>