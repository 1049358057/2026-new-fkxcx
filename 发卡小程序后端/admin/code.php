<?php
/*
灵思发卡小程序
作者：灵思
qq：2309624439（添加注明来意！承接各种修改、搭建）
v：lisi0377
qq技术交流群：897443011
blog网址：http://blog.ailingsi.top/

*/

class ValidateCode
{

    private $charset = '1234567890';

    private $code;

    private $codelen = 5;

    private $width = 150;

    private $height = 36;

    private $img;


    private $fontsize = 40;

    private $fontcolor;

    

    public function __construct()
    {
    }

    

    private function createCode()
    {

        $_len = strlen($this->charset) - 1;

        for ($i = 0; $i < $this->codelen; $i++) {

            $this->code .= $this->charset[mt_rand(0, $_len)];

        }

    }

    

    private function createBg()
    {

        $this->img = imagecreatetruecolor($this->width, $this->height);

        $color = imagecolorallocate($this->img, 243, 251, 254);

        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);

    }

    

    private function createFont()
    {

        $_x = $this->width / $this->codelen;

        for ($i = 0; $i < $this->codelen; $i++) {

            $this->fontcolor = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));

            imagestring($this->img, $this->fontsize, $_x * $i + mt_rand(1, 5), $this->height / 3, $this->code[$i], $this->fontcolor);

        }

    }

    

    private function createLine()
    {

        

        for ($i = 0; $i < 6; $i++) {

            $color = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));

            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);

        }

        

        for ($i = 0; $i < 100; $i++) {

            $color = imagecolorallocate($this->img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));

            imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), "s", $color);

        }

    }

    

    private function outPut()
    {

        header('Content-type:image/png');

        imagepng($this->img);

        imagedestroy($this->img);

    }

    

    public function doimg()
    {

        $this->createBg();

        $this->createCode();

        $this->createLine();

        $this->createFont();

        $this->outPut();

    }

    

    public function getCode()
    {

        return strtolower($this->code);

    }

}


session_start();

$_vc = new ValidateCode();  

$_vc->doimg();

$_SESSION['vc_code'] = $_vc->getCode();

exit();