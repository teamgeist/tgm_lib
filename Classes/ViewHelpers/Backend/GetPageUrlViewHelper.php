<?php

namespace TGM\TgmLib\ViewHelpers\Backend;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class GetPageUrlViewHelper extends AbstractViewHelper {

	/**
	 * Builds an url to view a page in the backend.
	 *
	 * @param int   $uid    The uid of the page to open.
	 *
	 * @return string The final url
	 */
	public function render($uid) {
        // TODO: check userrights, check compatibility with workspaces, check if uid exists

		$url = '?M=' . $_GET['M'] . '&moduleToken=' . $_GET['moduleToken'] . '&id=' . $uid;

		return $url;
	}
}