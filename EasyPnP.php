<?php
/*******************************************************************************
* EasyPnP                                                                        *
*                                                                              *
* Version: 0.6                                                                *
* Date:    2019-05-15                                                          *
* Author:  Ivan Di Rienzo (R0adRunn3r)                                                    *
*******************************************************************************/
define('A4_WIDTH','210');
define('A4_HEIGHT','297');

class EasyPnP
{
    protected $x;
    protected $y;
    protected $page_padding;
    protected $img_margin;
    protected $fpdf;

    protected $fileType;

    protected $buffer;
    protected $row_max_height;

    /**
     * 
     */
    function __construct($page_padding = 5, $img_margin = 0, $existing_fpdf = null){
        $this->page_padding = $page_padding;
        $this->img_margin = $img_margin;
        $this->fileType = "";
        $this->row_max_height = 0;
        if (empty($existing_fpdf)) {
            $this->fpdf = new FPDF();
            $this->NewPage();
        } else {
            $this->fpdf = $existing_fpdf;
            $this->x = $this->page_padding;
            $this->y = $this->fpdf->GetY();
            if ($this->y < $this->page_padding) $this->y = $this->page_padding;
        }
    }

    function GetX(){
        return $this->x;
    }

    function GetY(){
        return $this->y;
    }

    function GetFpdf(){
        return $this->fpdf;
    }

    function GetPagePadding(){
        $this->page_padding;
    }

    function GetImgMargin(){
        $this->img_margin;
    }

    function GetFileType(){
        return $this->fileType;
    }

    function SetX($newX){
        if (is_numeric($newX) && $newX >= 0)
            $this->x = $newX;
        else $this->Error('Invalid x value');
    }

    function SetY($newY){
        if (is_numeric($newY) && $newY >= 0)
            $this->y = $newY;
        else $this->Error('Invalid y value');
    }

    function SetPagePadding($newPadding){
        if (is_numeric($newPadding) && $newPadding >= 0)
            $this->page_padding = $newPadding;
        else $this->Error('Invalid page padding value');
    }

    function SetImgMargin($newMargin){
        if (is_numeric($newMargin) && $newMargin >= 0)
            $this->img_margin = $newMargin;
        else $this->Error('Invalid images margin value');
    }

    function SetFileType($newFileType){
        //TODO add check
        $this->fileType = $newFileType;
    }

    function NewPage(){
        $this->fpdf->AddPage();
        $this->x = $this->page_padding;
        $this->y = $this->page_padding+$this->img_margin;
        $this->row_max_height = 0;
    }

    function WriteImages($width, $height, $imgs, $reverse = false){
        if (!is_array($imgs)) {
            if (is_string($imgs)) $imgs = array($imgs);
            else return;
        }
        if (!$this->_checkdim($width, $height)) $this->Error("width or height not valid");

        //$num_per_row = $this->_calcnumperrow($width);
        //$current_col = 0;

        foreach ($imgs as $img) {
            if ($this->x + $width + ($this->img_margin*2) > A4_WIDTH-$this->page_padding) $this->_newrow();
            if ($this->y + $height + ($this->img_margin) > A4_HEIGHT-$this->page_padding) $this->NewPage();

            $this->_checkmaxheight($height);

            //Add left margin
            $this->x += $this->img_margin;

            $calc_x = $reverse ? (A4_WIDTH - $this->x - $width) : $this->x;

            $this->fpdf->Image($img,$calc_x,$this->y,$width, $height, $this->fileType);

            //Add img width and rigth margin
            $this->x += $width + $this->img_margin;
        }

        //$this->_newrow($height);
        
    }

    function WriteImagesDoubleSide($width, $height, $imgs, $backs){
        if (!is_array($imgs)) {
            if (is_string($imgs)) $imgs = array($imgs);
            else return;
        }
        if (!$this->_checkdim($width, $height)) $this->Error("width or height not valid");
        if (is_array($backs) && count($imgs) != count($backs))
            $this->Error("The number of backs images must be the same of the fronts");
        
        if (is_string($backs)) $backs = EasyPnP::MultiplyFilesArray($backs, count($imgs));
        else $backs = array_values($backs);

        $img_per_page = $this->_calcnumperrow($width) * $this->_calcnumpercol($height);
        $current_backs_pos = 0;

        if (!$this->_checkpage()) $this->NewPage();

        foreach ($imgs as $img) {
            if ($this->x + $width + ($this->img_margin*2) > A4_WIDTH-$this->page_padding) $this->_newrow();
            if ($this->y + $height + ($this->img_margin) > A4_HEIGHT-$this->page_padding) {
                $this->_addbacks($width, $height, $backs, $current_backs_pos, $img_per_page);
                $current_backs_pos += $img_per_page;
                $this->NewPage();
            }

            $this->_checkmaxheight($height);

            //Add left margin
            $this->x += $this->img_margin;

            $this->fpdf->Image($img,$this->x,$this->y,$width, $height, $this->fileType);

            //Add img width and rigth margin
            $this->x += $width + $this->img_margin;
        }
        $this->_addbacks($width, $height, $backs, $current_backs_pos, $img_per_page);
        $this->_newrow();

        
    }

    function GeneratePDF($download = false, $file_name = "print.pdf"){
        $this->fpdf->Output(($download ? "D" : "I"),$file_name);
    }


    function Error($msg){
        throw new Exception("EasyPnP Error: ".$msg);
    }

    /**
     * Protected Methods
     */

    protected function _checkdim($width, $height){
        if ($width + $this->img_margin*2 > A4_WIDTH - $this->page_padding*2 ||
            $height + $this->img_margin*2 > A4_HEIGHT - $this->page_padding*2) 
                return false;
        return true;
    }

    protected function _addbacks($width, $height, $backs, $current_backs_pos, $img_per_page){
        $this->NewPage();
        $curr_backs_arr = array_slice($backs, $current_backs_pos, $img_per_page);
        $this->WriteImages($width, $height, $curr_backs_arr, true);
    }

    protected function _calcnumperrow($width){
    $available_space = A4_WIDTH - ($this->page_padding*2);
    return floor($available_space / ($width + $this->img_margin*2));
    }

    protected function _calcnumpercol($height){
        $available_space = A4_HEIGHT - ($this->page_padding*2);
        return floor($available_space / ($height + $this->img_margin*2));
    }

    protected function _newrow() {
        $this->y += $this->row_max_height + ($this->img_margin*2);
        $this->x = $this->page_padding;

        if ($this->y > (A4_HEIGHT - $this->page_padding)) $this->NewPage();

        $this->row_max_height = 0;
    }

    protected function _checkpage(){
        if ($this->x == $this->page_padding && $this->y == $this->page_padding+$this->img_margin) return true;
        return false;
    }

    protected function _checkmaxheight($height){
        if ($this->row_max_height < $height) $this->row_max_height = $height;
    }

    /**
     * STATIC METHODS
     */

    static function FilesArrayFromPattern($string, $iterations, $from = 0) {
        if (!is_string($string) || count( $parts = explode("%", $string)) != 2) throw new Exception("Error generatig file array: not a valid string");
        
        $iterations += $from;

        $result = array();
        for ($i=$from; $i < $iterations; $i++) { 
            $result[] = $parts[0].$i.$parts[1];
        }
        return $result;
    }

    static function FilesArrayFromDir($path){
        if ($dir_arr = scandir($path)) {
            $dir_arr = array_diff($dir_arr, array('.', '..'));

            foreach ($dir_arr as $key => $file) {
                if (in_array(substr($file,-3), array("jpg", "png")))
                    $dir_arr[$key] = $path."/".$file;
                else 
                    unset($dir_arr[$key]);
            }

            return array_values($dir_arr);

        } else {
            throw new Exception("Error: not a valid directory");
        }
    }

    static function MultiplyFilesArray($files, $num) {
        if (is_string($files)) $files = array($files);
        if (!is_array($files)) throw new Exception("Error: invalid input files");

        $files = array_values($files); //force numeric keys

        $output = array_values($files);

        for ($i=1; $i < $num; $i++) { 
            $output = array_merge($output, $files);
        }

        return $output;
    }

}


?>