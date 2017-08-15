<?php
namespace TGM\TgmLib\TypoScript;

use TYPO3\CMS\Core\Configuration\TypoScript\ConditionMatching\AbstractCondition;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Vergleicht den Wert von dem Field im ersten Parameter mit dem zweiten Parameter.
 */
class MyCondition extends AbstractCondition {

    /**
     * Evaluate condition
     *
     * @param array $conditionParameters
     * @return bool
     */
    public function matchCondition(array $conditionParameters) {
        $result = FALSE;

//        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($conditionParameters);
//        $getParams = GeneralUtility::_GET();
//        if($getParams['edit']['tt_content']) {
//            $field = BackendUtility::getRecord('tt_content',(int)key($getParams['edit']['tt_content']));
//            $flexform = GeneralUtility::xml2array($field['pi_flexform']);
//            $flexform = GeneralUtility::xml2array($field['pi_flexform']);
//            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($flexform);
//        }

//        if(!empty($getParams['columnsOnly'])){
//            //Wir befinden und in der Listen und bearbeiten nur die vom Redakteur gesetzten felder, hier können wir auf den einzelnen Datensatz keinen einfluss nehmen
//            return false;
//        }
//        if($getParams['defVals']['tt_content']){
//            //the element is a new one so we can check only the defVals
//            if($getParams['defVals']['tt_content'][$conditionParameters[0]] == $conditionParameters[1]) return true;
//        }else if($getParams['edit']['tt_content']){
//            reset($getParams['edit']['tt_content']);
//            $first_key = key($getParams['edit']['tt_content']);
//            if((int)$first_key > 0){
//                $field = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord('tt_content',(int)$first_key,$conditionParameters[0].',CType');
//                //Check the flux field 'tx_fed_fcefile' only when the ce is from type fluidcontent_content
//                if($field['CType'] !== 'fluidcontent_content' && $conditionParameters[0] === 'tx_fed_fcefile'){
//                    return false;
//                }
//                if(is_array($field) && $field[key($field)] === $conditionParameters[1]){
//                    return true;
//                }
//            }
//        };
        $result = true;
        return $result;
    }
}