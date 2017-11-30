<?php

namespace Liip\ImagineBundle\Imagine\Filter;

use Imagine\Filter\FilterInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Imagick\Image as ImagickImage;
use Imagine\Gmagick\Image as GmagickImage;

/**
 * A trim filter
 *
 * @see http://zavaboy.com/2007/10/06/trim_an_image_using_php_and_gd
 * @author Igor Mukhin <igor.mukhin@gmail.com>
 */
class Trim implements FilterInterface
{
    private $fuzz;

    /**
     * @param integer $fuzz How much tolerance is acceptable to consider two colors as the same
     */
    public function __construct($fuzz = 0)
    {
        $this->fuzz = $fuzz;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        if ($image instanceof ImagickImage) {
            $imagick = $image->getImagick();
            $range = $imagick->getQuantumRange();
            $imagick->trimImage($this->fuzz / 100 * $range['quantumRangeLong']);

            return $image;
        }

        if ($image instanceof GmagickImage) {
            $gmagick = $image->getGmagick();

            $gmagick->trimimage($this->fuzz);

            return $image;
        }

        $size = $image->getSize();
        $origWidth = $size->getWidth();
        $origHeight = $size->getHeight();

        $xMin = $origWidth;
        $xMax = 0;

        // Scanning for the edges
        for ($iy = 0; $iy < $origHeight; $iy++) {
            $first = true;
            for ($ix = 0; $ix < $origWidth; $ix++) {
                $checkColor = $image->getColorAt(new Point($ix, $iy));

                // Take color of top-left corner as background
                if ($iy == 0 && $ix == 0) {
                    $background = $checkColor;
                }

                if (!$this->isSimilar($background, $checkColor)) {
                    if ($xMin > $ix) {
                        $xMin = $ix;
                    }

                    if ($xMax < $ix) {
                        $xMax = $ix;
                    }

                    if (!isset($yMin)) {
                        $yMin = $iy;
                    }

                    $yMax = $iy;

                    if ($first) {
                        $ix = $xMax;
                        $first = false;
                    }
                }
            }
        }

        if (!isset($yMin)) {
            $yMin = 1;
        }

        if (!isset($yMax)) {
            $yMax = $origHeight;
        }

        // The new width and height of the image. (not including padding)
        $croppedWidth = 1+$xMax-$xMin;
        $croppedHeight = 1+$yMax-$yMin;

        $start = new Point($xMin, $yMin);
        $size = new Box($croppedWidth, $croppedHeight);

        return $image->crop($start, $size);
    }

    /**
     * @param  ColorInterface $color1
     * @param  ColorInterface $color2
     * @return boolean
     */
    private function isSimilar(ColorInterface $color1, ColorInterface $color2) {

        $components = array(
            ColorInterface::COLOR_RED,
            ColorInterface::COLOR_GREEN,
            ColorInterface::COLOR_BLUE
        );

        foreach ($components as $component) {
            if (abs($color1->getValue($component) - $color2->getValue($component)) > $this->fuzz) return false;
        }

        return true;
    }
}