<?php

namespace TGM\TgmLib\ViewHelpers\Image;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ResponsiveBackgroundViewHelper
 *
 * Generates images in different sizes and css code for responsive background image.
 *
 *
 */
class ResponsiveBackgroundViewHelper extends AbstractViewHelper {

    use CompileWithRenderStatic;

	/**
	 * @return void
	 */
	public function initializeArguments() {
        $this->registerArgument('src', 'string', 'src', true);
        $this->registerArgument('treatIdAsReference', 'bool', 'given src argument is a sys_file_reference record', false, false);
        $this->registerArgument('image', 'object', 'image');
        $this->registerArgument('crop', 'string|bool', 'overrule cropping of image (setting to FALSE disables the cropping set in FileReference)');
        $this->registerArgument('cropVariant', 'string', 'select a cropping variant, in case multiple croppings have been specified or stored in FileReference', false, 'default');

        $this->registerArgument('absolute', 'bool', 'Force absolute URL', false, false);

        $this->registerArgument('cssSelector', 'string', 'css selector', true);
        $this->registerArgument('settings', 'array', '', false);
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
        $settings = $arguments['settings'];

        if ((is_null($src) && is_null($image)) || (!is_null($src) && !is_null($image))) {
            throw new Exception('You must either specify a string src or a File object.', 1502472301);
        }

        try {
            /** @var ObjectManager $objectManager */
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            /** @var ImageService $imageService */
            $imageService = $objectManager->get(ImageService::class);

//            DebuggerUtility::var_dump($settings);

            if(count($settings)) {
                foreach ($settings as $imageConf) {
                    $image = $imageService->getImage($src, $image, $treatIdAsReference);

                    if ($cropString === null && $image->hasProperty('crop') && $image->getProperty('crop')) {
                        $cropString = $image->getProperty('crop');
                    }

                    $cropVariantCollection = CropVariantCollection::create((string)$cropString);
                    $cropVariant = $arguments['cropVariant'] ?: 'default';
                    $cropArea = $cropVariantCollection->getCropArea($cropVariant);
                    $processingInstructions = [
                        'width' => $imageConf['width'] ? $imageConf['width'] : null,
                        'height' => $imageConf['height'] ? $imageConf['height'] : null,
                        'minWidth' => $imageConf['minWidth'] ? $imageConf['minWidth'] : null,
                        'minHeight' => $imageConf['minHeight'] ? $imageConf['minHeight'] : null,
                        'maxWidth' => $imageConf['maxWidth'] ? $imageConf['maxWidth'] : null,
                        'maxHeight' => $imageConf['maxHeight'] ? $imageConf['maxHeight'] : null,
                        'additionalParameters' => $imageConf['quality'] ? '-quality '. $imageConf['quality'] : null,
                        'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
                    ];

                    $processedImage = $imageService->applyProcessingInstructions($image, $processingInstructions);

                    $row = $arguments['cssSelector'] . '{background-image:url(\'' . $imageService->getImageUri($processedImage, $absolute) . '\');}';
                    if ($imageConf['mediaQuery']) {
                        $row = $imageConf['mediaQuery'] . '{' . $row . '}';
                    }
//                    DebuggerUtility::var_dump($row, 'ResposiveBackgroundViewHelper');
                    $result .= $row;
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