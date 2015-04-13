<?php
/**
 * ImageManager package
 *
 * @package   ImageManager
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace ImageManager;

use ImageManager\Pixel;

/**
 * Manager
 *
 * @package ImageManager
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Manager
{
    /**
     * Image resource
     *
     * @var resource|nell
     */
    protected $image;

    /**
     * Filename
     *
     * @var string
     */
    protected $filename = '';


    /**
     * @param string $file
     */
    public function __construct($file = '')
    {
        if ($file) {
            $this->readImage($file);
        }
    }

    /**
     * @param string $file
     *
     * @return boolen
     */
    public function read($file)
    {
        $this->setFilename($file);
        if (!$this->validateSizeLimit($file)) {
            return false;
        }
        switch (pathinfo($file, PATHINFO_EXTENSION)) {
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
        return (bool)$this->image;
    }

    /**
     * @param string $file
     */
    public function setFilename($file)
    {
        $this->filename = $file;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return resource
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param integer $width
     * @param integer $height
     */
    public function reset($width, $height)
    {
        $this->image = imagecreatetruecolor($width, $height);
    }

    /**
     * @param \ImageManager\Manager $composite
     * @param integer $x
     * @param integer $y
     */
    public function composite(Manager $composite, $x, $y)
    {
//        $img = $composite->getImage();
    }

    /**
     * @param string $file
     * @param integer $mode
     *
     * @return boolen
     */
    public function write($file = '', $mode = 0755)
    {
        $file = $file ?: $this->getFilename();
        if (!$this->image) {
            return false;
        }
        switch (pathinfo($file, PATHINFO_EXTENSION)) {
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
        chmod($file, $mode);
        return $result;
    }

    /**
     * @return integer
     */
    public function getWidth()
    {
        return $this->image ? imagesx($this->image) : 0;
    }

    /**
     * @return integer
     */
    public function getHeight()
    {
        return $this->image ? imagesy($this->image) : 0;
    }

    /**
     * @param integer $width
     * @param integer $height
     *
     * @return boolean
     */
    public function resize($width, $height)
    {
        if (!$this->image) {
            return false;
        }
        $image = imagecreatetruecolor($width, $height);
        $xi = $yi = 0;
        if ($this->getWidth() < $width || $this->getHeight() < $height) {
            $xi = ceil(($width - $this->getWidth()) / 2);
            $yi = ceil(($height - $this->getHeight()) / 2);
        }
        $result = imagecopyresampled(
            $image,
            $this->image,
            $xi,
            $yi,
            0,
            0,
            $width,
            $height,
            $this->getWidth(),
            $this->getHeight()
        );
        if ($result) {
            $this->image  = $image;
        }
        unset($image);
        return $result;
    }

    /**
     * @param integer $width
     * @param integer $height
     * @param integer $x
     * @param integer $y
     *
     * @return boolean
     */
    public function crop($width, $height, $x, $y)
    {
        if (!$this->image) {
            return false;
        }
        $image = imagecreatetruecolor($width, $height);
        $xi = $yi = 0;
        if ($this->getWidth() < $width || $this->getHeight() < $height) {
            $xi = ceil(($width - $this->getWidth()) / 2);
            $yi = ceil(($height - $this->getHeight()) / 2);
        }
        $result = imagecopyresized(
            $image,
            $this->image,
            $xi,
            $yi,
            $x,
            $y,
            $width,
            $height,
            $width,
            $height
        );
        if ($result) {
            $this->image  = $image;
        }
        unset($image);
        return $result;
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
     * @param integer $out_width
     * @param integer $out_height
     * @param boolean $bestfit
     *
     * @param boolen
     */
    public function thumbnail($out_width = 0, $out_height = 0, $bestfit = false)
    {
        // не выбран выходной размер изображения
        if ($out_width < 0 && $out_height < 0) {
            return false;
        }

        // получение размера рисунка
        $in_width  = $this->getWidth();
        $in_height = $this->getHeight();

        // размер исходного изображения меньше требуемого
        if ($in_width <= $out_width && $in_height <= $out_height) {
            return false;
        }

        if ($bestfit && $out_width && $out_height) {
            // создание эскиза с сохранением размеров
            $up_width  = $in_width;
            $up_height = $in_height;
            // вычисление пропорций
            if ($up_width > $out_width) {
                $up_width = $out_width;
                $up_height = ceil(($in_height * (($out_width * 100) / $in_width)) / 100);
            }
            // вычисление пропорций
            if ($up_height > $out_height) {
                $up_height = $out_height;
                $up_width = ceil(($in_width * (($out_height * 100) / $in_height)) / 100);
            }
            list($out_width, $out_height) = array($up_width, $up_height);
            unset($up_width, $up_height);
        } else {
            // вычисление пропорций
            if (!$out_width) {
                $out_width = ceil(($in_width * (($out_height * 100) / $in_height)) / 100);
            }
            // вычисление пропорций
            if (!$out_height) {
                $out_height = ceil(($in_height * (($out_width * 100) / $in_width)) / 100);
            }
        }

        // выполнение изменения размера
        return $this->resize($out_width, $out_height);
    }

    /**
     * @param integer $out_width
     * @param integer $out_height
     *
     * @return boolean
     */
    public function cropThumbnail($out_width, $out_height)
    {
        // получение размера рисунка
        $in_width  = $this->getWidth();
        $in_height = $this->getHeight();

        // размер исходного изображения меньше требуемого
        if ($in_width <= $out_width && $in_height <= $out_height) {
            return false;
        }

        $up_height = $out_height;
        $up_width  = $out_width;

        // вычисление пропорций
        if ($in_width > $in_height){
            if (ceil(($in_width * (($out_height * 100) / $in_height)) / 100) > $out_width) {
                $up_width = ceil(($in_width * (($out_height * 100) / $in_height)) / 100);
            } else {
                $up_height = ceil(($in_height * (($out_width * 100) / $in_width)) / 100);
            }
        } else {
            if (ceil(($in_height * (($out_width * 100) / $in_width)) / 100) > $out_height) {
                $up_height = ceil(($in_height * (($out_width * 100) / $in_width)) / 100);
            } else {
                $up_width = ceil(($in_width * (($out_height * 100) / $in_height)) / 100);
            }
        }

        // выполнение изменения размера
        if (!$this->resize($up_width, $up_height)) {
            return false;
        }
        return $this->crop(
            $out_width,
            $out_height,
            ceil(($up_width - $out_width) / 2),
            ceil(($up_height - $out_height) / 2)
        );
    }

    /**
     * @param \ImageManager\Pixel $color
     *
     * @return boolean
     */
    public function setBackgroundColor(Pixel $color)
    {
        list($r, $g, $b) = $color->getColor();
        $bg = imagecolorallocate($this->image, $r, $g, $b);
        imagefill($this->image, 0, 0, $bg);
        return imagefill($this->image, $this->getWidth() - 1, $this->getHeight() - 1, $bg);
    }

    /**
     * @param string $file
     *
     * @return boolen
     */
    public function validateSizeLimit($file)
    {
        list($width, $height) = getimagesize($file);
        $max = $width * $height * 4;
        $max =+ $max * 0.25;
        return $max < $this->getMemoryLimit();
    }

    /**
     * Возвращает максимальный объем данных в байтах, которые можно занести в память
     *
     * @return integer
     */
    protected function getMemoryLimit()
    {
        $memory_limit = (int)ini_get('memory_limit');
        if ($memory_limit) {
            preg_match('/^(\d+)(\w*)$/', strtolower($memory_limit), $match);
            switch (isset($match[2]) ? $match[2] : '') {
                case 'g':
                    $memory_limit = intval($memory_limit) * 1024 * 1024 * 1024;
                    break;
                case 'm':
                    $memory_limit = intval($memory_limit) * 1024 * 1024;
                    break;
                case 'k':
                    $memory_limit = intval($memory_limit) * 1024;
                    break;
                default:
                    $memory_limit = intval($memory_limit);
            }
        }
        return $memory_limit ?: 2097152; // 2Mb
    }

    /**
     * @return \ImageManager\Manager
     */
    public function clear()
    {
        if ($this->image) {
            imagedestroy($this->image);
            unset($this->image);
        }
        $this->image = null;
        return $this;
    }

    public function __destruct()
    {
        $this->clear();
    }
}
