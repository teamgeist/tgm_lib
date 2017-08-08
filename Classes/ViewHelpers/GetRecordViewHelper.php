<?php

namespace TGM\TgmLib\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Backend\Utility\BackendUtility;

class GetRecordViewHelper extends AbstractViewHelper {

	/**
	 * TODO: languages, workspaces and userrights
	 * @param string  $table
	 * @param integer $uid
	 *
	 * @return array|bool
	 */
	public function render($table, $uid) {
		$result = BackendUtility::getRecord($table, $uid);
		return (empty($result) === false) ? $result : false;
	}
}