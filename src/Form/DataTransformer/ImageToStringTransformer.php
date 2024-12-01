<?php

namespace App\Form\DataTransformer;

use App\Entity\Image;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;

class ImageToStringTransformer implements DataTransformerInterface
{
    public function transform($images): array
    {
        if (!$images instanceof Collection) {
            return [];
        }

        return $images->map(fn (Image $image) => $image->getUrl())->toArray();
    }

    public function reverseTransform($imageUrls): array
    {
        if (!$imageUrls || !is_array($imageUrls)) {
            return [];
        }

        $images = [];
        foreach ($imageUrls as $url) {
            $image = new Image();
            $image->setUrl($url);
            $images[] = $image;
        }

        return $images;
    }
}
