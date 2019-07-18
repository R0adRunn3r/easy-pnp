<?php
require_once("fpdf/fpdf.php");
require_once("../EasyPnP.php");

$card_width = 60; 
$card_height = 80;

$epnp = new  EasyPnP(5,3);

$img_array_folder = EasyPnP::FilesArrayFromDir("./imgs/card_front");

$epnp->WriteImages($card_width, $card_height, $img_array_folder);
$epnp->GeneratePDF(false, "all_front.pdf");
?>