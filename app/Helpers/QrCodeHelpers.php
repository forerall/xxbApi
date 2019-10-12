<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/11
 * Time: 10:35
 */
class  QrCodeHelpers extends \Endroid\QrCode\QrCode
{
    public function __construct($text)
    {
        parent::__construct($text);
    }

    //直接返回图片
    public function getQrCodePic($size = 300)
    {
        header('Content-Type: ' . $this->getContentType());
        $this->setSize($size);
        echo $this->writeString();
    }
    //获取图片base64
    public function getBase64Pic($size = 200)
    {
        $this->setSize($size);
        $str = "data:".$this->getContentType().";base64,";
        return  $str.base64_encode($this->writeString());
    }


}