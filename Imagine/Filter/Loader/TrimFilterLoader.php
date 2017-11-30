<?php

namespace Liip\ImagineBundle\Imagine\Filter\Loader;

use Imagine\Gmagick\Image;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;
use Imagine\Image\ImageInterface;

/**
 * Class TrimFilterLoader
 * @package Liip\ImagineBundle\Imagine\Filter\Loader
 * @author Igor Mukhin <igor.mukhin@gmail.com>
 */
class TrimFilterLoader implements LoaderInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ImageInterface $image, array $options = array())
    {
        if (!empty($options['fuzz'])) {
            $fuzz = $options['fuzz'];
        }

        $filter = new Trim($fuzz);

        return $filter->apply($image);
    }
}