<?php

namespace TGM\TgmLib\ViewHelpers;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * GetRecordViewHelper
 *
 * Fetching a raw record from table=$table and uid=$uid.
 * If the table or/and the uid does not exist, it'll return false, otherwise, the record.
 */
class GetRecordViewHelper extends AbstractViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('table', 'string', 'The table of the record.', false, 'tt_content');
		$this->registerArgument('uid', 'int', 'The uid of the record.', true);
	}

	/**
	 * @return array|bool
	 */
	public function render() {
		$result = BackendUtility::getRecord($this->arguments['table'], $this->arguments['uid']);
		return (empty($result) === false) ? $result : false;
	}
}