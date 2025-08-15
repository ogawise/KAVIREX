<?php


function redirect($path, $query=null){
    if($query==null){
        header("Location: $path");
        exit;
    }else {
        header("Location: $path?$query");
        exit;
    }
}