<?php

namespace TGM\TgmLib\ViewHelpers\Backend;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * GetPageUrlViewHelper
 */
class GetPageUrlViewHelper extends AbstractViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('uid', 'int', 'The uid of the page to open.', true);
	}

	/**
	 * Builds an url to view a page in the backend.
	 *
	 * @return string The final url
	 */
	public function render() {
		// TODO: check userrights, check compatibility with workspaces, check if uid exists

		$url = '?M=' . $_GET['M'] . '&moduleToken=' . $_GET['moduleToken'] . '&id=' . $this->arguments['uid'];
		return $url;
	}
}