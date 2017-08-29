<?php

namespace TGM\TgmLib\ViewHelpers\Image;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

/**
 * PictureViewHelper
 *
 * Generates images in different sizes and css code for responsive background image.
 *
 *
 */
class PictureViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\ImageViewHelper {

    /**
     * Image Service
     *
     * @var \TYPO3\CMS\Extbase\Service\ImageService
     * @inject
     */
    protected $imageService;

    /**
     * Object Manager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     * @inject
     */
    protected $objectManager;

    /**
     * Initialize arguments.
     */
	public function initializeArguments() {
        parent::initializeArguments();
        $this->registerArgument('sources', 'array', 'settings for source-tags', false);
	}

    /**
     * Renders an image as picture
     *
     * @return string Rendered tag
     */
    public function render() {

        $src = $this->arguments['src'];
        $image = $this->arguments['image'];
        $treatIdAsReference = $this->arguments['treatIdAsReference'];

        if ((is_null($src) && is_null($image)) || (!is_null($src) && !is_null($image))) {
            throw new Exception('You must either specify a string src or a File object.', 1502472301);
        }

        // Fall back to TYPO3 default if no responsive image feature was selected
        if (!$this->arguments['sources']) {
            return parent::render();
        }

        try {
            /** @var FileInterface|FileReference $imageObject */
            $imageObject = $this->imageService->getImage($src, $image, $treatIdAsReference);

            $this->tag = $this->objectManager->get(TagBuilder::class, 'picture');
            $this->tag->setContent($this->renderPicture($imageObject));

            return $this->tag->render();

        } catch (ResourceDoesNotExistException $e) {
            // thrown if file does not exist
        } catch (\UnexpectedValueException $e) {
            // thrown if a file has been replaced with a folder
        } catch (\RuntimeException $e) {
            // RuntimeException thrown if a file is outside of a storage
        } catch (\InvalidArgumentException $e) {
            // thrown if file storage does not exist
        }

        return '';
    }

	/**
	 * @return string Rendered tag
	 */
    public function renderPicture(FileReference $image) {

        $absolute = $this->arguments['absolute'];
        $imageSources = $this->arguments['sources'];

        $result = '';

        if(count($imageSources)) {
            foreach ($imageSources as $imageSource) {

//                DebuggerUtility::var_dump($this->arguments);

                $cropVariant = $imageSource['cropVariant'] ?: 'default';
                $cropString = $image instanceof FileReference ? $image->getProperty('crop') : '';
                $cropVariantCollection = CropVariantCollection::create((string)$cropString);
                $cropArea = $cropVariantCollection->getCropArea($cropVariant);

                $imageSourceSrcsetWidths = array_map('trim', explode(',', $imageSource['srcset']));
//                DebuggerUtility::var_dump($imageSourceSrcsetWidths, 'PictureViewHelper');

                $imageSrcsetItems = [];
                foreach ($imageSourceSrcsetWidths as $imageSourceSrcsetWidth) {
                    $processingInstructions = [
                        'width' => $imageSourceSrcsetWidth ? $imageSourceSrcsetWidth : null,
                        'additionalParameters' => $imageSource['quality'] ? '-quality '. $imageSource['quality'] : null,
                        'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
                    ];

//                    DebuggerUtility::var_dump($processingInstructions, 'PictureViewHelper');
                    $processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);

                    $imageSrcsetItems[] = $this->imageService->getImageUri($processedImage, $absolute) . " " . $imageSourceSrcsetWidth . 'w';
                }

                /** @var TagBuilder $sourceTag */
                $sourceTag = GeneralUtility::makeInstance(TagBuilder::class);

                $sourceTag->addAttribute('srcset', implode(', ', $imageSrcsetItems));
                if($imageSource['sizes']) {
                    $sourceTag->addAttribute('sizes', $imageSource['sizes']);
                }

                if($imageSource['media']) {
                    $sourceTag->setTagName('source');
                    $sourceTag->addAttribute('media', $imageSource['media']);
                } else {
                    $sourceTag->setTagName('img');
                    $sourceTag->addAttribute('title', $this->arguments['title']);
                    $sourceTag->addAttribute('alt', $this->arguments['alt']);
                    $sourceTag->addAttribute('class', $this->arguments['class']);
                }

//                DebuggerUtility::var_dump($sourceTag->render(), 'PictureViewHelper');
                $result .= $sourceTag->render();
            }
        }
		return $result;
	}
}