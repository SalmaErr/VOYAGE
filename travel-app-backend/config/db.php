<?php
$host = "localhost" ;
$dbname = "voyage" ;
$user = "root" ; 
$pw = "" ;

try{
        $cnx = new PDO("mysql:host=$host;dbname=$dbname",$user,$pw) ; 
        $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }catch(PDOException $e){
        echo "Error connection : " . $e -> getMessage() ;
    }


?>