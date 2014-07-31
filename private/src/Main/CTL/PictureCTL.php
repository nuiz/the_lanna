<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/21/14
 * Time: 3:22 PM
 */

namespace Main\CTL;
use Main\DataModel\Image;

/**
 * @Restful
 * @uri /picture
 */
class PictureCTL extends BaseCTL {
    /**
     * @GET
     * @uri /[h:id]
     */
    public function index(){
        $imgModel = Image::findOne($this->reqInfo->urlParam('id'));
        $file = 'private/pictures/'.$imgModel->getPath();
        $img = $this->convertImage($file);

        if($this->reqInfo->hasInput('width') && $this->reqInfo->hasInput('height')){
            $img = $this->grab($img);
        }

        header('Content-Type: image/jpeg');
        header('Ram: '.memory_get_usage(true));
        imagejpeg($img);
        exit();
    }

    function convertImage($originalImage)
    {
        // jpg, png, gif or bmp?
        $exploded = explode('.',$originalImage);
        $ext = $exploded[count($exploded) - 1];

        if (preg_match('/jpg|jpeg/i',$ext))
            $imageTmp=imagecreatefromjpeg($originalImage);
        else if (preg_match('/png/i',$ext))
            $imageTmp=imagecreatefrompng($originalImage);
        else if (preg_match('/gif/i',$ext))
            $imageTmp=imagecreatefromgif($originalImage);
        else if (preg_match('/bmp/i',$ext))
            $imageTmp=imagecreatefrombmp($originalImage);
        else
            return 0;

        return $imageTmp;
    }

    function grab($img){
        $owidth = imagesx($img);
        $oheight = imagesy($img);

        $width = $this->reqInfo->input('width');
        $height = $this->reqInfo->input('height');

        // ausschnitt berechnen
        $grab_width = $owidth;
        $ratio = $grab_width / $width;

        if ($height * $ratio <= $oheight) {
            $grab_height = round($height * $ratio);
            $src_x = 0;
            $src_y = round(($oheight - $grab_height) / 2);
        } else {
            $grab_height = $oheight;
            $ratio = $grab_height / $height;
            $grab_width = round($width * $ratio);
            $src_x = round(($owidth - $grab_width) / 2);
            $src_y = 0;
        }

        //var_dump($width, $height);
        //exit();

        $newImg = imagecreatetruecolor($width, $height);
        imagecopyresized($newImg, $img, 0, 0, $src_x, $src_y, $width, $height, $grab_width, $grab_height);

        return $newImg;
    }
} 