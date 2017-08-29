<?php

namespace TGM\TgmLib\ViewHelpers\Media;

/**
 * ImageViewHelper
 *
 * Extends vhs image viewhelper by sizes attribute.
 */
class ImageViewHelper extends \FluidTYPO3\Vhs\ViewHelpers\Media\ImageViewHelper {


    /**
     * Initialize arguments.
     */
	public function initializeArguments() {
        parent::initializeArguments();
        $this->registerArgument('sizes', 'string', 'sizes', false);
	}

    /**
     * @return string
     */
    public function renderTag()
    {
        if (false === empty($this->arguments['srcset'])) {
            $srcSetVariants = $this->addSourceSet($this->tag, $this->mediaSource);
        }

        if ($this->hasArgument('canvasWidth') || $this->hasArgument('canvasHeight')) {
            $width = $this->arguments['canvasWidth'];
            $height = $this->arguments['canvasHeight'];
            $src = $this->mediaSource;
        } elseif (false === empty($srcSetVariants) && false === empty($this->arguments['srcsetDefault'])) {
            $srcSetVariantDefault = $srcSetVariants[$this->arguments['srcsetDefault']];
            $src = $srcSetVariantDefault['src'];
            $width = $srcSetVariantDefault['width'];
            $height = $srcSetVariantDefault['height'];
        } else {
            $src = static::preprocessSourceUri($this->mediaSource, $this->arguments);
            $width = $this->imageInfo[0];
            $height = $this->imageInfo[1];
        }


        $this->tag->addAttribute('width', $width);
        $this->tag->addAttribute('height', $height);
        $this->tag->addAttribute('src', $src);

        if (true === empty($this->arguments['title'])) {
            $this->tag->addAttribute('title', $this->arguments['alt']);
        }

        if (false === empty($this->arguments['sizes'])) {
            $this->tag->addAttribute('sizes', $this->arguments['sizes']);
        }

        return $this->tag->render();
    }
}