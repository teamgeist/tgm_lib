<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "vhlib"
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'ViewHelperLibrary',
	'description' => 'ViewHelperLibrary',
	'category' => 'misc',
	'author' => 'Steffen Thierock',
	'author_email' => 'st@teamgeist-medien.de',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'version' => '0.0.1',
	'constraints' => array(
		'depends' => array(
			'typo3' => '8.7.0-8.7.99'
		),
		'conflicts' => array(),
		'suggests' => array(),
	),
);