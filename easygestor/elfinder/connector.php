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
        // ============================================
        // Root uploads folder (main site content)
        // ============================================
        // Files volume
        array(
            'driver'        => 'LocalFileSystem',
            'path'          => dirname(__DIR__) . '/../uploads/files/',
            'URL'           => '/uploads/files/',
            'trashHash'     => 't2_Lw',
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
            'alias'         => 'Uploads files',
            'disabled'      => array(),
            'treeDeep'      => 3,
        ),
        // Images volume
        array(
            'driver'        => 'LocalFileSystem',
            'path'          => dirname(__DIR__) . '/../uploads/images/',
            'URL'           => '/uploads/images/',
            'trashHash'     => 't2_Lw',
            'winHashFix'    => DIRECTORY_SEPARATOR !== '/',
            'uploadDeny'    => array('all'),
            'uploadAllow'   => array('image/x-ms-bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'),
            'uploadOrder'   => array('deny', 'allow'),
            'accessControl' => 'access',
            'alias'         => 'Uploads images',
            'disabled'      => array(),
            'treeDeep'      => 3,
            'tmbPath'       => '.tmb',
            'tmbURL'        => '/uploads/images/.tmb/',
            'tmbSize'       => 128,
            'tmbCrop'       => false,
        ),
        // Trash volume
        array(
            'id'            => '2',
            'driver'        => 'Trash',
            'path'          => dirname(__DIR__) . '/../uploads/.trash/',
            'tmbURL'        => '/uploads/.trash/.tmb/',
            'winHashFix'    => DIRECTORY_SEPARATOR !== '/',
            'uploadDeny'    => array('all'),
            'uploadAllow'   => array('image/x-ms-bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/webp', 'text/plain'),
            'uploadOrder'   => array('deny', 'allow'),
            'accessControl' => 'access',
        ),
        // ============================================
        // Documentacion folder
        // ============================================
        // Files volume
        array(
            'driver'        => 'LocalFileSystem',
            'path'          => dirname(__DIR__) . '/../documentacion/files/',
            'URL'           => '/documentacion/files/',
            'winHashFix'    => DIRECTORY_SEPARATOR !== '/',
            'uploadDeny'    => array('all'),
            'uploadAllow'   => array(
                'image/x-ms-bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/webp', 'image/tiff',
                'application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.oasis.opendocument.text',
                'application/vnd.oasis.opendocument.spreadsheet',
                'application/zip', 'application/x-rar-compressed', 'application/gzip', 'application/x-7z-compressed',
                'text/plain', 'text/csv', 'text/rtf',
                'audio/mpeg', 'audio/x-wav', 'audio/x-aiff',
                'video/mp4', 'video/mpeg', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv',
                'application/x-shockwave-flash'
            ),
            'uploadOrder'   => array('deny', 'allow'),
            'accessControl' => 'access',
            'alias'         => 'Documentacion files',
            'disabled'      => array(),
            'treeDeep'      => 3,
        ),
        // Images volume
        array(
            'driver'        => 'LocalFileSystem',
            'path'          => dirname(__DIR__) . '/../documentacion/images/',
            'URL'           => '/documentacion/images/',
            'winHashFix'    => DIRECTORY_SEPARATOR !== '/',
            'uploadDeny'    => array('all'),
            'uploadAllow'   => array('image/x-ms-bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/webp'),
            'uploadOrder'   => array('deny', 'allow'),
            'accessControl' => 'access',
            'alias'         => 'Documentacion images',
            'disabled'      => array(),
            'treeDeep'      => 3,
            'tmbPath'       => '.tmb',
            'tmbURL'        => '/documentacion/images/.tmb/',
            'tmbSize'       => 128,
            'tmbCrop'       => false,
        ),
    )
);

// Run elFinder
$connector = new elFinderConnector(new elFinder($opts));
$connector->run();
