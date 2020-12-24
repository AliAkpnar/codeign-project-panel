<?php

function convertToSeo($text){

    $turkce = array("ç","Ç","ğ","Ğ","ü","Ü","ö","Ö","ı","İ","ş","Ş",".",",","!","'","\""," ","?","*","_","|","=","[","]","{","}","(",")");
    $convert = array("c","c","g","g","u","u","o","o","i","i","s","s", "-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-");

    return strtolower(str_replace($turkce , $convert , $text));

}

/* function getFileName($img_url){

    $file_name = $this->product_image_model->getById(
        array(
            "id"   => $id
        )
    );
    return $img_url;
} */