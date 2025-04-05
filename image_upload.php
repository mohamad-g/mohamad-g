<?php
// Include necessary files
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/auth.php';

// Function to handle image upload
function uploadImage($file, $targetDir, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']) {
    // Check if file was uploaded without errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'message' => 'Error uploading file: ' . getUploadErrorMessage($file['error'])
        ];
    }
    
    // Check file type
    if (!in_array($file['type'], $allowedTypes)) {
        return [
            'success' => false,
            'message' => 'Invalid file type. Allowed types: ' . implode(', ', array_map(function($type) {
                return str_replace('image/', '', $type);
            }, $allowedTypes))
        ];
    }
    
    // Check file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return [
            'success' => false,
            'message' => 'File is too large. Maximum size is 5MB.'
        ];
    }
    
    // Create target directory if it doesn't exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '.' . $fileExtension;
    $targetPath = $targetDir . '/' . $fileName;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return [
            'success' => true,
            'message' => 'File uploaded successfully',
            'fileName' => $fileName,
            'filePath' => $targetPath
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to move uploaded file'
        ];
    }
}

// Function to get upload error message
function getUploadErrorMessage($errorCode) {
    switch ($errorCode) {
        case UPLOAD_ERR_INI_SIZE:
            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
        case UPLOAD_ERR_FORM_SIZE:
            return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
        case UPLOAD_ERR_PARTIAL:
            return 'The uploaded file was only partially uploaded';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing a temporary folder';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk';
        case UPLOAD_ERR_EXTENSION:
            return 'A PHP extension stopped the file upload';
        default:
            return 'Unknown upload error';
    }
}

// Function to delete image
function deleteImage($fileName, $targetDir) {
    $filePath = $targetDir . '/' . $fileName;
    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            return [
                'success' => true,
                'message' => 'File deleted successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to delete file'
            ];
        }
    } else {
        return [
            'success' => false,
            'message' => 'File does not exist'
        ];
    }
}

// Function to get image URL
function getImageUrl($fileName, $targetDir, $defaultImage = null) {
    if (!empty($fileName) && file_exists($targetDir . '/' . $fileName)) {
        return str_replace($_SERVER['DOCUMENT_ROOT'], '', $targetDir) . '/' . $fileName;
    } else {
        return $defaultImage;
    }
}
