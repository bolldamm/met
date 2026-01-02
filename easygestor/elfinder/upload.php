<?php
/**
 * CKEditor Upload Handler for elFinder integration
 * Handles direct uploads from CKEditor's "Upload" tab
 */

// Error reporting (disable in production)
error_reporting(0);
ini_set('display_errors', 0);

// Configuration - where to upload files
$config = [
    'images' => [
        'path' => dirname(__DIR__) . '/../documentacion/images/',
        'url'  => '/documentacion/images/',
        'allowedExtensions' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'],
        'maxSize' => 16 * 1024 * 1024, // 16MB
    ],
    'files' => [
        'path' => dirname(__DIR__) . '/../documentacion/files/',
        'url'  => '/documentacion/files/',
        'allowedExtensions' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'txt', 'csv', 'rtf', 'zip', 'rar', '7z', 'mp3', 'mp4', 'avi', 'mov'],
        'maxSize' => 64 * 1024 * 1024, // 64MB
    ],
];

// Determine upload type from query parameter
$type = isset($_GET['type']) && $_GET['type'] === 'images' ? 'images' : 'files';
$settings = $config[$type];

// CKEditor callback function number
$funcNum = isset($_GET['CKEditorFuncNum']) ? $_GET['CKEditorFuncNum'] : '';

/**
 * Send response to CKEditor
 */
function sendResponse($funcNum, $url = '', $message = '') {
    header('Content-Type: text/html; charset=UTF-8');
    echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
    exit;
}

/**
 * Send JSON response (for newer CKEditor versions)
 */
function sendJsonResponse($uploaded, $url = '', $error = '') {
    header('Content-Type: application/json; charset=UTF-8');
    if ($uploaded) {
        echo json_encode([
            'uploaded' => 1,
            'fileName' => basename($url),
            'url' => $url
        ]);
    } else {
        echo json_encode([
            'uploaded' => 0,
            'error' => ['message' => $error]
        ]);
    }
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['upload']) || $_FILES['upload']['error'] !== UPLOAD_ERR_OK) {
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds server upload limit',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds form upload limit',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'Upload blocked by extension',
    ];
    $error = isset($_FILES['upload']) ? ($errorMessages[$_FILES['upload']['error']] ?? 'Unknown upload error') : 'No file uploaded';

    if ($funcNum) {
        sendResponse($funcNum, '', $error);
    } else {
        sendJsonResponse(false, '', $error);
    }
}

$file = $_FILES['upload'];
$originalName = $file['name'];
$tmpPath = $file['tmp_name'];
$fileSize = $file['size'];

// Get file extension
$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

// Validate extension
if (!in_array($extension, $settings['allowedExtensions'])) {
    $error = 'File type not allowed. Allowed types: ' . implode(', ', $settings['allowedExtensions']);
    if ($funcNum) {
        sendResponse($funcNum, '', $error);
    } else {
        sendJsonResponse(false, '', $error);
    }
}

// Validate file size
if ($fileSize > $settings['maxSize']) {
    $maxMB = $settings['maxSize'] / 1024 / 1024;
    $error = "File too large. Maximum size: {$maxMB}MB";
    if ($funcNum) {
        sendResponse($funcNum, '', $error);
    } else {
        sendJsonResponse(false, '', $error);
    }
}

// Additional security check for images
if ($type === 'images') {
    $imageInfo = @getimagesize($tmpPath);
    if ($imageInfo === false) {
        $error = 'Invalid image file';
        if ($funcNum) {
            sendResponse($funcNum, '', $error);
        } else {
            sendJsonResponse(false, '', $error);
        }
    }
}

// Create upload directory if it doesn't exist
if (!is_dir($settings['path'])) {
    mkdir($settings['path'], 0755, true);
}

// Generate safe filename (remove special characters, handle duplicates)
$safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
$safeName = substr($safeName, 0, 100); // Limit length
$fileName = $safeName . '.' . $extension;

// Handle duplicate filenames
$destPath = $settings['path'] . $fileName;
$counter = 1;
while (file_exists($destPath)) {
    $fileName = $safeName . '_' . $counter . '.' . $extension;
    $destPath = $settings['path'] . $fileName;
    $counter++;
}

// Move uploaded file
if (move_uploaded_file($tmpPath, $destPath)) {
    // Set file permissions
    chmod($destPath, 0644);

    // Build URL
    $fileUrl = $settings['url'] . $fileName;

    if ($funcNum) {
        sendResponse($funcNum, $fileUrl, '');
    } else {
        sendJsonResponse(true, $fileUrl);
    }
} else {
    $error = 'Failed to save uploaded file';
    if ($funcNum) {
        sendResponse($funcNum, '', $error);
    } else {
        sendJsonResponse(false, '', $error);
    }
}
