<?php

namespace TGM\TgmLib\ViewHelpers\Image;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ResponsiveImageViewHelper
 *
 * Generates images in different sizes and css code for responsive background image.
 *
 *
 */
class ResponsiveImageViewHelper extends AbstractTagBasedViewHelper {

    use CompileWithRenderStatic;

	/**
	 * @return void
	 */
	public function initializeArguments() {
        $this->registerArgument('src', 'string', 'src', true);
        $this->registerArgument('treatIdAsReference', 'bool', 'given src argument is a sys_file_reference record', false, false);
        $this->registerArgument('image', 'object', 'image');
        $this->registerArgument('absolute', 'bool', 'Force absolute URL', false, false);

        $this->registerArgument('cssClass', 'string', 'css class', true);
        $this->registerArgument('breakpoints', 'array', '', false);
        $this->registerArgument('title', 'string', 'title', false);
        $this->registerArgument('alt', 'string', 'alt', false);
	}

	/**
	 * @return array|bool
	 */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext) {

        $result = '';

        $src = $arguments['src'];
        $image = $arguments['image'];
        $treatIdAsReference = $arguments['treatIdAsReference'];
        $cropString = $arguments['crop'];
        $absolute = $arguments['absolute'];
        $breakpoints = $arguments['breakpoints'];

        if ((is_null($src) && is_null($image)) || (!is_null($src) && !is_null($image))) {
            throw new Exception('You must either specify a string src or a File object.', 1502472301);
        }

        try {
            /** @var ObjectManager $objectManager */
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            /** @var ImageService $imageService */
            $imageService = $objectManager->get(ImageService::class);

            if(count($breakpoints)) {
                foreach ($breakpoints as $imageSource) {
                    $imageSourceTag = "<source srcset=\"";

                    $image = $imageService->getImage($src, $image, $treatIdAsReference);

//                    DebuggerUtility::var_dump($imageSource);

                    $cropVariant = $imageSource['cropVariant'] ?: 'default';
                    $cropString = $image instanceof FileReference ? $image->getProperty('crop') : '';
                    $cropVariantCollection = CropVariantCollection::create((string)$cropString);
                    $cropArea = $cropVariantCollection->getCropArea($cropVariant);

                    $imageSourceSrcsetWidths = array_map('trim', explode(',', $imageSource['srcset']));

                    $imageSourceSrcsetItems = [];
                    foreach ($imageSourceSrcsetWidths as $imageSourceSrcsetWidth) {
                        $processingInstructions = [
                            'width' => $imageSource['width'] ? $imageSourceSrcsetWidth : null,
                            'additionalParameters' => $imageSource['quality'] ? '-quality '. $imageSource['quality'] : null,
                            'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
                        ];

                        $processedImage = $imageService->applyProcessingInstructions($image, $processingInstructions);

                        $imageSourceSrcsetItems[] = $imageService->getImageUri($processedImage, $absolute) . " " . $imageSourceSrcsetWidth . 'w';
                    }

                    $imageSourceTag .= implode(', ', $imageSourceSrcsetItems) . "\" media=\"" . $imageSource['media'] . "\"/>";

//                    DebuggerUtility::var_dump($row, 'ResposiveBackgroundViewHelper');
                    $result .= "<picture>" . $imageSourceTag . "</picture>";
                }
            }
        } catch (ResourceDoesNotExistException $e) {
            // thrown if file does not exist
        } catch (\UnexpectedValueException $e) {
            // thrown if a file has been replaced with a folder
        } catch (\RuntimeException $e) {
            // RuntimeException thrown if a file is outside of a storage
        } catch (\InvalidArgumentException $e) {
            // thrown if file storage does not exist
        }

		return $result;
	}
}