<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

call_user_func(static function () {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
        "@import 'EXT:hh_widget_hhnews/Configuration/TypoScript/setup.typoscript'"
    );

    // Add UserTS config as default for all BE users
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig(
        "@import 'EXT:hh_widget_hhnews/Configuration/TsConfig/User/0100_default.typoscript'"
    );
});
