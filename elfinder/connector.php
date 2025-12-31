<?php
/**
 * elFinder connector
 */

// Autoload
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Documentation: https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options

$opts = [
    'roots' => [
        [
            'driver'        => 'LocalFileSystem',
            'path'          => dirname(__DIR__) . '/uploads/',  // Path to files folder
            'URL'           => '../uploads/',                    // URL to files folder
            'uploadDeny'    => ['all'],                         // Block all by default
            'uploadAllow'   => ['image', 'text/plain', 'application/pdf'], // Allow images, text, PDF
            'uploadOrder'   => ['deny', 'allow'],
            'accessControl' => 'access',                        // Access control function
            'attributes'    => [
                // Hide hidden files (starting with .)
                [
                    'pattern' => '/^\./',
                    'read'    => false,
                    'write'   => false,
                    'hidden'  => true,
                    'locked'  => false
                ]
            ]
        ]
    ]
];

// Access control function
function access($attr, $path, $data, $volume, $isDir, $relpath) {
    // Hide hidden files/folders
    return strpos(basename($path), '.') === 0
        ? !($attr === 'read' || $attr === 'write')
        : null;
}

// Run elFinder
$connector = new elFinderConnector(new elFinder($opts));
$connector->run();
