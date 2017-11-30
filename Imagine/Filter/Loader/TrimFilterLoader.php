<?php

namespace Liip\ImagineBundle\Imagine\Filter\Loader;

use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;
use Liip\ImagineBundle\Imagine\Filter\Trim;

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