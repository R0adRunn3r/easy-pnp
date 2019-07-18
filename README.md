# EasyPnP

EasyPnP is a PHP class which allows to generate easily a PDF for PnP (Print and Play) Games. I made it for helping me generate precise pdf from a set of card and tokens images for some of my projects becouse I'd like to have more control, so i decided to share it, maybe someone will find it useful.

Obviously this class is far from perfect but gets the job done for me :) the only dependency is [fpdf](http://www.fpdf.org/)

## Installation

### Download from github

You can just download the class from github and use it in your project, the only dependency is [fpdf](http://www.fpdf.org/) so make sure to require it!
  
### Composer

If you're using Composer to manage dependencies, you can use

    $ composer require r0adrunn3r/easy-pnp

or you can include the following in your composer.json file:

```json
{
    "require": {
        "setasign/fpdf": ">=0.6.1"
    }
}
```
  
## How to use

The unit of measurement is mm (I'm Italian) but the underlying fpdf supports inches too, modify the class to support other units is not too hard, if someone needs it and doesn't know how, just open an issue and I will add it :)

Example use with various configurations:

```php
require_once('./fpdf.php');
require_once('./EasyPnP.php');

$card_width = 30; // 30mm
$card_height = 60; //60mm

//Arguments are the padding of the pages and the margins of the images respectively.
$epnp = new  EasyPnP(5,3);

//You can use URLs or Paths
$img_array = array(
    "http://mysite.com/cards/image1.png",
    "http://mysite.com/cards/image2.png",
    "./cards/image3.png"
);

$epnp->WriteImages($card_width, $card_height, $img_array);

//----------------------------------------------------

//Static helper method for generating an array of imgs paths from a folder
$img_array_folder = EasyPnP::FilesArrayFromDir("./cards");

$epnp->WriteImages($card_width, $card_height, $img_array_folder);

//----------------------------------------------------  

/*If your images are organized with a sequence number you can use the FilesArrayFromPattern static helper method
This call for exaple is equivalent to:
$array_img_pattern = array(
    "./tokens/token_2.png",
    "./tokens/token_3.png",
    "./tokens/token_4.png",
    "./tokens/token_5.png",
    "./tokens/token_6.png",
);

5 images, counter starts from 2
*/
$array_img_pattern = EasyPnP::FilesArrayFromPattern("./tokens/token_%.png",5, 2);

$epnp->WriteImages(20, 20, $array_img_pattern);

//----------------------------------------------------

//You can also use generated images (with PHP GD for example) but *BEFORE* use them make sure to specify the type of file to EasyPnP
$epnp->SetFileType('png');
$img_array_php = array("http://xyz.com/cards.php?id=45","http://xyz.com/cards.php?id=47");

$epnp->WriteImages($card_width, $card_height, $img_array_php);

//----------------------------------------------------

//You can also create a front & back pdf ready to print, the fourth argument can be a single image path or an array of backs, if so the array of front and the array of backs must have the same size
$epnp->WriteImagesDoubleSide($card_width, $card_height, $img_array, "./back.png"); 

//---------------------------------------------------- 

//After all the images are writed out you can output the pdf, the first argument is for forcing the download instead of opend it inside the browser.
$pdf->GeneratePDF(true, "printable.pdf");
```

You can find more examples in the `examples` folder, ready for testing (included a version of fpdf).

All the cards in the example folder are from https://code.google.com/archive/p/vector-playing-cards/