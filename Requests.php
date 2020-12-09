<?php
class Requests{
    
    private $base = "https://api-v3.mbta.com/";
    private $APIKEY = "f01fbd4bcb95479db792afaec2fa3a56";
    
    function trigger($path){
        $headers = array(
            'Content-Type:application/json',
             'x-api-key: '.$this->APIKEY
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $this->base.$path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch); 

        return json_decode($output);
        // return [];
    }

} 
?>