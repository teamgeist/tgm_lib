<?php

namespace TGM\TgmLib\ViewHelpers\Backend;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class GetModuleUrlViewHelper extends AbstractViewHelper {

	/**
	 * Builds an url to a module for a record in the backend.
	 *
     * @param string $modul The action for the record (edit, delete).
     * @param string $table The table of the record.
	 * @param int    $uid   The uid of the record.
	 *
	 * @return string The final url
	 */
	public function render(string $action, string $table, int $uid) {
		// TODO: check userrights, check compatibility with workspaces, check if uid exists

        $returnUrl = GeneralUtility::getIndpEnv('REQUEST_URI');

        switch($action) {
            case 'edit':
                $url = BackendUtility::getModuleUrl(
                    'record_edit',
                    [
                        'edit' => [
                            $table => [$uid => 'edit']
                        ],
                        'returnUrl' => $returnUrl
                    ]
                );
                break;
            case 'delete':
                $url = BackendUtility::getModuleUrl(
                    'tce_db',
                    [
                        'cmd[' . $table . '][' . $uid . '][delete]' => 1,
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