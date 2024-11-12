<?php
// Start session if needed
// session_start();

// Get the requested URL path
$request = $_SERVER['REQUEST_URI'];

// Remove the base path (e.g., /google-drive-clone) if it's there
$base_path = '/tsany-drive'; // Set this to the base path of your app

// Remove the base path from the request URI if it exists
$path = str_replace($base_path, '', parse_url($request, PHP_URL_PATH));

// Clean up any leading slash
$path = ltrim($path, '/');

// Parse URL to get query parameters
$query_params = [];
if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']) {
    parse_str($_SERVER['QUERY_STRING'], $query_params);
}

// Basic routing
switch ($path) {
    case '':
        // Load login page
        echo 'Test Session';
        break;

    case 'login':
        // Load login page
        include 'src/views/auth/login.php';
        break;

    case 'dashboard':
        // Get the 'parent_id' from the query parameters
        $parent_id = isset($query_params['parent_id']) ? $query_params['parent_id'] : null;
        
        // Include the dashboard page and pass the 'parent_id'
        // Assuming 'src/views/dashboard.php' can access $parent_id
        include 'src/views/dashboard.php'; // In the dashboard.php, you can use $parent_id to display or process the data
        break;

    case 'api/data':
        // API endpoint example
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'API data']);
        break;

    default:
        // Load 404 page if route not found
        http_response_code(404);
        echo 'Test 404';
        break;
}
