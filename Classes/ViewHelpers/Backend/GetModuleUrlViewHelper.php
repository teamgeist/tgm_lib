<?php

namespace TGM\TgmLib\ViewHelpers\Backend;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * GetModuleUrlViewHelper
 */
class GetModuleUrlViewHelper extends AbstractViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('action', 'string', 'The action for the record (edit, delete).', true);
		$this->registerArgument('table', 'string', 'The table of the record.', true);
		$this->registerArgument('uid', 'int', 'The uid of the record.', true);
	}

	/**
	 * Builds an url to a module for a record in the backend.
	 *
	 * @return string The final url
	 */
	public function render() {
		// TODO: check userrights, check compatibility with workspaces, check if uid exists

		$returnUrl = GeneralUtility::getIndpEnv('REQUEST_URI');

		switch ($this->arguments['action']) {
			case 'edit':
				$url = BackendUtility::getModuleUrl(
					'record_edit',
					[
						'edit' => [
							$this->arguments['table'] => [
								$this->arguments['uid'] => 'edit'
							]
						],
						'returnUrl' => $returnUrl
					]
				);
				break;
			case 'delete':
				$url = BackendUtility::getModuleUrl(
					'tce_db',
					[
						'cmd[' . $this->arguments['table'] . '][' . $this->arguments['uid'] . '][delete]' => 1,
						'redirect' => $returnUrl
					]
				);
				break;
			default:
				$url = '';
		}

		return $url;
	}
}