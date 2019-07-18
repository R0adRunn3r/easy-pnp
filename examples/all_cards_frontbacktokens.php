<?php
require_once("fpdf/fpdf.php");
require_once("../EasyPnP.php");

$card_width = 55; 
$card_height = 75; 
$tokens_diameter = 25;

$epnp = new  EasyPnP(5,3);

$img_fronts = EasyPnP::FilesArrayFromDir("./imgs/card_front");

$epnp->WriteImagesDoubleSide($card_width, $card_height, $img_fronts, "./imgs/card_back/back.png");

$img_tokens = EasyPnP::FilesArrayFromDir("./imgs/tokens");

$epnp->WriteImages($tokens_diameter, $tokens_diameter, $img_tokens);

$epnp->GeneratePDF(false, "all_and_tokens.pdf");
?>