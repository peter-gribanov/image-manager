﻿<?php
/**
 * ImageManager package
 *
 * @package   ImageManager
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace ImageManager;

/**
 * Manager
 *
 * @package ImageManager
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ImageManager
{
    /**
     * @var resource
     */
    private $image = null;

    /**
     * @var string
     */
    private $filename = '';


    /**
     * @param string
     */
    public function __construct($file=null){
        if ($file) $this->readImage($file);
    }

    /**
     * @param string
     *
     * @return boolen
     */
    public function readImage($file){
        $this->setFilename($file);
        if (!$this->validateSizeLimit($file)) return false;
        switch(pathinfo($file, PATHINFO_EXTENSION)){
            case 'jpg':
            case 'jpe':
            case 'jpeg':
                $this->image = imagecreatefromjpeg($file);
                break;
            case 'gif':
                $this->image = imagecreatefromgif($file);
                break;
            case 'png':
                $this->image = imagecreatefrompng($file);
                break;
            case 'bmp':
                $this->image = imagecreatefromwbmp($file);
                break;
            default:
                return false;
        }
        return (bool) $this->image;
    }

    /**
     * @param string
     */
    public function setFilename($file){
        $this->filename = $file;
    }

    /**
     * @return string
     */
    public function getFilename(){
        return $this->filename;
    }

    /**
     * @return resource
     */
    public function getImage(){
        return $this->image;
    }

    /**
     * @param integer
     * @param integer
     */
    public function newImage($width, $height){
        $this->image = imagecreatetruecolor($width, $height);
    }

    /**
     * @param ImageManager
     * @param integer
     * @param integer
     */
    public function compositeImage(ImageManager $composite_object, $x, $y){
//        $img = $composite_object->getImage();
    }

    /**
     * @param string
     *
     * @return boolen
     */
    public function writeImage($file=null){
        $file = $file ? $file : $this->getFilename();
        if (!$this->image) return false;
        switch(pathinfo($file, PATHINFO_EXTENSION)){
            case 'jpg':
            case 'jpe':
            case 'jpeg':
                $result = imagejpeg($this->image, $file, 100);
                break;
            case 'gif':
                $result = imagegif($this->image, $file);
                break;
            case 'png':
                $result = imagepng($this->image, $file);
                break;
            case 'bmp':
                $result = imagewbmp($this->image, $file);
                break;
            default:
                return false;
        }
        @chmod($file, 0777);
        return $result;
    }

    /**
     * @return integer
     */
    public function getWidth(){
        return $this->image ? imagesx($this->image) : 0;
    }

    /**
     * @return integer
     */
    public function getHeight(){
        return $this->image ? imagesy($this->image) : 0;
    }

    /**
     * @param integer
     * @param integer
     */
    public function resizeImage($width, $height){
        if (!$this->image) return;
        $image = imagecreatetruecolor($width, $height);
        $xi = $yi = 0;
        if ($this->getWidth()<$width || $this->getHeight()<$height){
            $xi = ceil(($width-$this->getWidth())/2);
            $yi = ceil(($height-$this->getHeight())/2);
        }
        imagecopyresampled($image,$this->image,$xi,$yi,0,0,$width,$height,$this->getWidth(),$this->getHeight());
        $this->image  = $image;
        unset($image);
    }

    /**
     * @param integer
     * @param integer
     * @param integer
     * @param integer
     */
    public function cropImage($width, $height, $x, $y){
        if (!$this->image) return;
        $image = imagecreatetruecolor($width, $height);
        $xi = $yi = 0;
        if ($this->getWidth()<$width || $this->getHeight()<$height){
            $xi = ceil(($width-$this->getWidth())/2);
            $yi = ceil(($height-$this->getHeight())/2);
        }
        imagecopyresized($image,$this->image,$xi,$yi,$x,$y,$width,$height,$width,$height);
        $this->image  = $image;
        unset($image);
    }

    /**
     * Calculate new image dimensions to new constraints
     * входящее изменение
     * создает миниатюру с размером входящим в заданный регион
     * одна сторона равна указанному размеру а вторая меньше него
     *
     * @param integer
     * @param integer
     *//*
     public function scaleImage($out_width, $out_height){
     // получение размера рисунка
     $in_width  = $up_width  = $this->getWidth();
     $in_height = $up_height = $this->getHeight();

     // размер исходного изображения меньше требуемого
     if ($in_width<=$out_width && $in_height<=$out_height) return;

     // вычисление пропорций
     if ($up_width > $out_width){
     $up_width  = $out_width;
     $up_height = ceil(($in_height*(($out_width*100)/$in_width))/100);
     }
     if ($up_height > $out_height){
     $up_height = $out_height;
     $up_width  = ceil(($in_width*(($out_height*100)/$in_height))/100);
     }
     // выполнение изменения размера
     $this->resizeImage($up_width, $up_height);
     }*/

    /**
     * @param integer
     * @param integer
     * @param boolen
     */
    public function thumbnailImage($out_width=null, $out_height=null, $bestfit=false){
        // не выбран выходной размер изображения
        if (!$out_width && !$out_height) return;

        // получение размера рисунка
        $in_width  = $this->getWidth();
        $in_height = $this->getHeight();

        // размер исходного изображения меньше требуемого
        if ($in_width<=$out_width && $in_height<=$out_height) return;

        if ($bestfit && $out_width && $out_height){
            // создание эскиза с сохранением размеров
            $up_width  = $in_width;
            $up_height = $in_height;
            // вычисление пропорций
            if ($up_width > $out_width){
                $up_width  = $out_width;
                $up_height = ceil(($in_height*(($out_width*100)/$in_width))/100);
            }
            // вычисление пропорций
            if ($up_height > $out_height){
                $up_height = $out_height;
                $up_width  = ceil(($in_width*(($out_height*100)/$in_height))/100);
            }
            list($out_width, $out_height) = array($up_width, $up_height);
            unset($up_width, $up_height);

        } else {
            // вычисление пропорций
            if (!$out_width){
                $out_width  = ceil(($in_width*(($out_height*100)/$in_height))/100);
            }
            // вычисление пропорций
            if (!$out_height){
                $out_height = ceil(($in_height*(($out_width*100)/$in_width))/100);
            }
        }

        // выполнение изменения размера
        $this->resizeImage($out_width, $out_height);
    }

    /**
     * @param integer
     * @param integer
     */
    public function cropThumbnailImage($out_width, $out_height){

        // получение размера рисунка
        $in_width  = $this->getWidth();
        $in_height = $this->getHeight();

        // размер исходного изображения меньше требуемого
        if ($in_width<=$out_width && $in_height<=$out_height) return;

        $up_height = $out_height;
        $up_width  = $out_width;

        // вычисление пропорций
        if ($in_width > $in_height){
            if (ceil(($in_width*(($out_height*100)/$in_height))/100) > $out_width){
                $up_width  = ceil(($in_width*(($out_height*100)/$in_height))/100);
            } else {
                $up_height = ceil(($in_height*(($out_width*100)/$in_width))/100);
            }
        } else {
            if (ceil(($in_height*(($out_width*100)/$in_width))/100) > $out_height){
                $up_height = ceil(($in_height*(($out_width*100)/$in_width))/100);
            } else {
                $up_width  = ceil(($in_width*(($out_height*100)/$in_height))/100);
            }
        }

        // выполнение изменения размера
        $this->resizeImage($up_width, $up_height);
        $this->cropImage($out_width, $out_height, ceil(($up_width-$out_width)/2), ceil(($up_height-$out_height)/2));
    }

    /**
     * @param \ImageManager\Pixel $color
     */
    public function setBackgroundColor(Pixel $color){
        list($r, $g, $b) = $color->getColor();
        $bg = imagecolorallocate($this->image, $r, $g, $b);
        imagefill($this->image, 0, 0, $bg);
        imagefill($this->image, $this->getWidth()-1, $this->getHeight()-1, $bg);
    }

    /**
     * @param string $file
     *
     * @return boolen
     */
    public function validateSizeLimit($file){
        list($width, $height) = getimagesize($file);
        $max = $width * $height * 4;
        $max =+ $max*0.25;
        return ($max < guepard\getMemoryLimit());
    }

    public function clear(){
        if ($this->image){
            imagedestroy($this->image);
            unset($this->image);
        }
        $this->image = null;
    }

    public function __destruct(){
        $this->clear();
    }
}
