<?php


session_start();

function checkLogin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }
}

function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function logout() {
    session_destroy();
    header('Location: login.php');
    exit();
}

// admin/config/functions.php
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function uploadImage($file, $target_dir = '../uploads/') {
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_types)) {
        return array('success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.');
    }
    
    if ($file['size'] > 5000000) { // 5MB
        return array('success' => false, 'message' => 'File size too large. Maximum 5MB allowed.');
    }
    
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return array('success' => true, 'filename' => $new_filename);
    } else {
        return array('success' => false, 'message' => 'Error uploading file.');
    }
}

function showAlert($message, $type = 'info') {
    $alertClass = '';
    switch($type) {
        case 'success': $alertClass = 'alert-success'; break;
        case 'error': $alertClass = 'alert-error'; break;
        case 'warning': $alertClass = 'alert-warning'; break;
        default: $alertClass = 'alert-info'; break;
    }
    
    return "<div class='alert {$alertClass}'>{$message}</div>";
}

// Get pagination data
function getPaginationData($total_records, $records_per_page, $current_page) {
    $total_pages = ceil($total_records / $records_per_page);
    $offset = ($current_page - 1) * $records_per_page;
    
    return array(
        'total_pages' => $total_pages,
        'offset' => $offset,
        'current_page' => $current_page,
        'records_per_page' => $records_per_page
    );
}

?>