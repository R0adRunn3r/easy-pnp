<?php
require_once("fpdf/fpdf.php");
require_once("../EasyPnP.php");

$fiche_diameter = 30; 

$epnp = new  EasyPnP(5,3);

$img_array = array(
    "./imgs/tokens/token_black.png",
    "./imgs/tokens/token_blue.png",
    "./imgs/tokens/token_red.png",
    "./imgs/tokens/token_white.png"
);

$epnp->WriteImages($fiche_diameter, $fiche_diameter, $img_array);

$epnp->NewPage();

$epnp->WriteImages(50, 50, "https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png");

$epnp->GeneratePDF(false, "fiches_and_logo.pdf");
?>