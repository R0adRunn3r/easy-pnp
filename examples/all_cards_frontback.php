<?php
require_once("fpdf/fpdf.php");
require_once("../EasyPnP.php");

$card_width = 55; 
$card_height = 75; 

$epnp = new  EasyPnP(5,3);

$img_array = EasyPnP::FilesArrayFromDir("./imgs/card_front");

$epnp->WriteImagesDoubleSide($card_width, $card_height, $img_array, "./imgs/card_back/back.png"); 

$epnp->GeneratePDF(false, "all_frontback.pdf");
?>