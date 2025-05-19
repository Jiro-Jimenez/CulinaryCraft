<?php
// Include config file
require_once 'config.php';
require_once 'functions.php';

// Check if request is AJAX
header('Content-Type: application/json');

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = array('success' => false);
    
    // Check if user is logged in
    if (!isLoggedIn()) {
        $response['redirect'] = 'login.php';
        echo json_encode($response);
        exit;
    }
    
    // Get action
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Handle different actions
    switch ($action) {
        case 'add_favorite':
            if (isset($_POST['recipe_id']) && is_numeric($_POST['recipe_id'])) {
                $user_id = $_SESSION['user_id'];
                $recipe_id = $_POST['recipe_id'];
                
                if (addToFavorites($user_id, $recipe_id)) {
                    $response['success'] = true;
                }
            }
            break;
            
        case 'remove_favorite':
            if (isset($_POST['recipe_id']) && is_numeric($_POST['recipe_id'])) {
                $user_id = $_SESSION['user_id'];
                $recipe_id = $_POST['recipe_id'];
                
                if (removeFromFavorites($user_id, $recipe_id)) {
                    $response['success'] = true;
                }
            }
            break;
            
        default:
            $response['error'] = 'Invalid action';
            break;
    }
    
    echo json_encode($response);
}
?>