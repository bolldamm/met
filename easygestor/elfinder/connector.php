<?php
/**
 * elFinder PHP Connector for easygestor
 * Integrates with CKEditor 4.x
 */

error_reporting(0);

// Load elFinder classes from Composer vendor directory
require_once dirname(__DIR__) . '/../vendor/autoload.php';

// Enable FTP connector netmount (optional)
elFinder::$netDrivers['ftp'] = 'FTP';

/**
 * Access control function
 * Disable access to files/folders starting with '.' (dot)
 */
function access($attr, $path, $data, $volume, $isDir, $relpath) {
    $basename = basename($path);
    return $basename[0] === '.'
        && strlen($relpath) !== 1
        ? !($attr == 'read' || $attr == 'write')
        : null;
}

// elFinder configuration
$opts = array(
    'debug' => false,
    'roots' => array(
        // Files volume
        array(
            'driver'        => 'LocalFileSystem',
            'path'          => dirname(__DIR__) . '/uploads/files/',
            'URL'           => dirname($_SERVER['PHP_SELF'], 2) . '/uploads/files/',
            'trashHash'     => 't1_Lw',
            'winHashFix'    => DIRECTORY_SEPARATOR !== '/',
            'uploadDeny'    => array('all'),
            'uploadAllow'   => array(
                'image/x-ms-bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/webp',
                'application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/zip', 'application/x-rar-compressed',
                'text/plain', 'text/csv'
            ),
            'uploadOrder'   => array('deny', 'allow'),
            'accessControl' => 'access',
            'alias'         => 'Files'
        ),
        // Images volume
        array(
            'driver'        => 'LocalFileSystem',
            'path'          => dirname(__DIR__) . '/uploads/images/',
            'URL'           => dirname($_SERVER['PHP_SELF'], 2) . '/uploads/images/',
            'trashHash'     => 't1_Lw',
            'winHashFix'    => DIRECTORY_SEPARATOR !== '/',
            'uploadDeny'    => array('all'),
            'uploadAllow'   => array('image/x-ms-bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'),
            'uploadOrder'   => array('deny', 'allow'),
            'accessControl' => 'access',
            'alias'         => 'Images',
            // Thumbnail settings
            'tmbPath'       => '.tmb',
            'tmbURL'        => dirname($_SERVER['PHP_SELF'], 2) . '/uploads/images/.tmb/',
            'tmbSize'       => 128,
            'tmbCrop'       => false,
        ),
        // Trash volume
        array(
            'id'            => '1',
            'driver'        => 'Trash',
            'path'          => dirname(__DIR__) . '/uploads/.trash/',
            'tmbURL'        => dirname($_SERVER['PHP_SELF'], 2) . '/uploads/.trash/.tmb/',
            'winHashFix'    => DIRECTORY_SEPARATOR !== '/',
            'uploadDeny'    => array('all'),
            'uploadAllow'   => array('image/x-ms-bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/webp', 'text/plain'),
            'uploadOrder'   => array('deny', 'allow'),
            'accessControl' => 'access',
        ),
    )
);

// Run elFinder
$connector = new elFinderConnector(new elFinder($opts));
$connector->run();
