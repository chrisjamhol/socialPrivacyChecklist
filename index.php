﻿<?php
    error_reporting(E_ALL & ~E_NOTICE);
    session_start();
	
    if(isset($_GET['op'])){
        $functionName = $_GET['op'];
        dispatch($_GET['from'],$functionName);
    }else if(isset($_POST['op'])){
        $functionName = $_POST['op'];
        dispatch($_POST['from'],$functionName);
    }else{
        header( 'Location: index.html' ) ;
    }    

    function dispatch($from,$functionName){
        switch($from){
            case 'front': include 'controller/FrontendController.php'; call_user_func($functionName,$db); break;
            case 'back': include 'controller/BackendController.php'; call_user_func($functionName,$db); break;
        }
    }   
    
?>
