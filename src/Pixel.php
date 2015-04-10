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

/**
 * Pixel
 *
 * Usage:
 * <code>
 *  $px = new Pixel('Cyanogen');
 *  $px->getColorAsString();
 * </code>
 *
 * @package ImageManager
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Pixel
{
    /**
     * Pixel color
     *
     * Default black
     *
     * <code>
     * {
     *     <red>,
     *     <green>,
     *     <blue>
     * }
     * <code>
     *
     * @var array
     */
    protected $color = array(0, 0, 0);

    /**
     * Color names
     *
     * @var array
     */
    protected $names = array(
        'aqua' => array(0, 255, 255),
        'aquamarine' => array(127, 255, 212),
        'azure' => array(240, 255, 255),
        'beige' => array(245, 245, 220),
        'bisque' => array(255, 228, 196),
        'black' => array(0, 0, 0),
        'blue' => array(0, 0, 255),
        'brown' => array(165, 42, 42),
        'chartreuse' => array(127, 255, 0),
        'chocolate' => array(210, 105, 30),
        'coral' => array(255, 127, 80),
        'cornsilk' => array(255, 248, 220),
        'crimson' => array(237, 164, 61),
        'cyan' => array(0, 255, 255),
        'darkorange' => array(255, 140, 0),
        'fuchsia' => array(255, 0, 255),
        'gainsboro' => array(220, 220, 220),
        'gold' => array(255, 215, 0),
        'gray' => array(128, 128, 128),
        'green' => array(0, 128, 0),
        'indigo' => array(75, 0, 130),
        'ivory' => array(255, 255, 240),
        'khaki' => array(240, 230, 140),
        'lavender' => array(230, 230, 250),
        'lime' => array(0, 255, 0),
        'linen' => array(250, 240, 230),
        'magenta' => array(255, 0, 255),
        'maroon' => array(128, 0, 0),
        'moccasin' => array(255, 228, 181),
        'navy' => array(0, 0, 128),
        'olive' => array(128, 128, 0),
        'orange' => array(255, 165, 0),
        'orchid' => array(218, 112, 214),
        'peru' => array(205, 133, 63),
        'pink' => array(255, 192, 203),
        'plum' => array(221, 160, 221),
        'purple' => array(128, 0, 128),
        'red' => array(255, 0, 0),
        'salmon' => array(250, 128, 114),
        'sienna' => array(160, 82, 45),
        'silver' => array(192, 192, 192),
        'snow' => array(255, 250, 250),
        'tan' => array(210, 180, 140),
        'teal' => array(0, 128, 128),
        'thistle' => array(216, 191, 216),
        'tomato' => array(255, 99, 71),
        'turquoise' => array(64, 224, 208),
        'violet' => array(238, 130, 238),
        'wheat' => array(245, 222, 179),
        'white' => array(255, 255, 255),
        'yellow' => array(255, 255, 0),
    );

    /**
     * Construct
     *
     * @param string
     */
    public function __construct($color = '')
    {
        if ($color) {
            $this->setColor($color);
        }
    }

    /**
     * Get color
     *
     * @return array
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Get color as string
     *
     * <code>
     *   255,255,255
     * </code>
     *
     * @return string
     */
    public function getColorAsString()
    {
        return implode(',', $this->color);
    }

    /**
     * Set color
     *
     * Understand color codes and names:
     *  - blue
     *  - indigo
     *  - #0000ff
     *  - #00f
     *  - rgb(0,0,255)
     *  - cmyk(100,100,100,10)
     *
     * @param string
     *
     * @return boolean
     */
    public function setColor($color)
    {
        if (!is_string($color) || !trim($color)) {
            return false;
        }

        $color = strtolower($color);
        // color names - blue
        if (isset($this->names[$color])){
            $this->color = $this->names[$color];
            return true;
        }

        if ($color[0] == '#') {
            // hex #0000ff
            if (preg_match('/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/', $color, $match)){
                $this->color = array(
                    hexdec($match[1]),
                    hexdec($match[2]),
                    hexdec($match[3])
                );
                return true;
            }
            // hex #00f
            if (preg_match('/^#([0-9a-f]{1})([0-9a-f]{1})([0-9a-f]{1})$/', $color, $match)){
                $this->color = array(
                    hexdec($match[1].$match[1]),
                    hexdec($match[2].$match[2]),
                    hexdec($match[3].$match[3])
                );
                return true;
            }
            return false;
        }

        // rgb(0, 0, 255)
        if (preg_match('/^rgb\(([0-9]{1,3}), ?([0-9]{1,3}), ?([0-9]{1,3})\)$/i', $color, $match)){
            $this->color = array($match[1], $match[2], $match[3]);
            return true;
        }

        // cmyk(100, 100, 100, 10)
        if (preg_match('/^cmyk\(([0-9]{1,3}), ?([0-9]{1,3}), ?([0-9]{1,3}), ?([0-9]{1,3})\)$/i', $color, $match)){
            $c = (255 * $match[1]) / 100;
            $m = (255 * $match[2]) / 100;
            $y = (255 * $match[3]) / 100;
            $k = (255 * $match[4]) / 100;

            $this->color = array(
                round((255 - $c) * (255 - $k) / 255),
                round((255 - $m) * (255 - $k) / 255),
                round((255 - $y) * (255 - $k) / 255)
            );
            return true;
        }

        return false;
    }

    /**
     * Clear color
     *
     * @return \ImageManager\Pixel
     */
    public function clear()
    {
        $this->color = array(0, 0, 0);
        return $this;
    }
}
