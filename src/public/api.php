<?php
require_once '/../../vendor/autoload.php';
require_once '../includes/DotEnv.php';
require_once '../includes/Database.php';
require_once '../models/Link.php';
require_once '../models/ShortlinkElement.php';

// Load environment variables
try {
    DotEnv::load(__DIR__ . '/../.env');
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Environment configuration error']);
    exit();
}

// Check if user is authenticated
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Check if the authenticated user is allowed
if ($_SESSION['user_id'] != $_ENV['ALLOWED_USER_ID']) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit();
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Handle API requests
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($method) {
    case 'GET':
        handleGetRequest($action);
        break;
    case 'POST':
        handlePostRequest($action);
        break;
    case 'PUT':
        handlePutRequest($action);
        break;
    case 'DELETE':
        handleDeleteRequest($action);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

function handleGetRequest($action) {
    global $db;
    
    switch ($action) {
        case 'links':
            $link = new Link($db);
            $stmt = $link->getAll();
            $links = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $links[] = [
                    'id' => $id,
                    'name' => $name,
                    'url' => $url,
                    'icon' => $icon_url,
                    'sort_order' => $sort_order
                ];
            }
            echo json_encode($links);
            break;
            
        case 'shortlink_elements':
            $element = new ShortlinkElement($db);
            $stmt = $element->getAll();
            $elements = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $elements[] = [
                    'id' => $id,
                    'name' => $name,
                    'url' => $url,
                    'icon_url' => $icon_url,
                    'description' => $description,
                    'element_type' => $element_type,
                    'parent_id' => $parent_id,
                    'section_id' => $section_id,
                    'sort_order' => $sort_order
                ];
            }
            echo json_encode($elements);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}

function handlePostRequest($action) {
    global $db;
    
    switch ($action) {
        case 'links':
            $link = new Link($db);
            
            // Get posted data
            $data = json_decode(file_get_contents("php://input"));
            
            $link->name = $data->name;
            $link->url = $data->url;
            $link->icon_url = $data->icon_url;
            $link->sort_order = $data->sort_order;
            
            if ($link->create()) {
                echo json_encode(['success' => true, 'message' => 'Link created successfully']);
            } else {
                http_response_code(503);
                echo json_encode(['success' => false, 'message' => 'Unable to create link']);
            }
            break;
            
        case 'shortlink_elements':
            $element = new ShortlinkElement($db);
            
            // Get posted data
            $data = json_decode(file_get_contents("php://input"));
            
            $element->name = $data->name;
            $element->url = $data->url;
            $element->icon_url = $data->icon_url;
            $element->description = $data->description;
            $element->element_type = $data->element_type;
            $element->parent_id = $data->parent_id;
            $element->section_id = $data->section_id;
            $element->sort_order = $data->sort_order;
            
            if ($element->create()) {
                echo json_encode(['success' => true, 'message' => 'Element created successfully']);
            } else {
                http_response_code(503);
                echo json_encode(['success' => false, 'message' => 'Unable to create element']);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}

function handlePutRequest($action) {
    global $db;
    
    switch ($action) {
        case 'links':
            $link = new Link($db);
            
            // Get posted data
            $data = json_decode(file_get_contents("php://input"));
            
            $link->id = $data->id;
            $link->name = $data->name;
            $link->url = $data->url;
            $link->icon_url = $data->icon_url;
            $link->sort_order = $data->sort_order;
            
            if ($link->update()) {
                echo json_encode(['success' => true, 'message' => 'Link updated successfully']);
            } else {
                http_response_code(503);
                echo json_encode(['success' => false, 'message' => 'Unable to update link']);
            }
            break;
            
        case 'shortlink_elements':
            $element = new ShortlinkElement($db);
            
            // Get posted data
            $data = json_decode(file_get_contents("php://input"));
            
            $element->id = $data->id;
            $element->name = $data->name;
            $element->url = $data->url;
            $element->icon_url = $data->icon_url;
            $element->description = $data->description;
            $element->element_type = $data->element_type;
            $element->parent_id = $data->parent_id;
            $element->section_id = $data->section_id;
            $element->sort_order = $data->sort_order;
            
            if ($element->update()) {
                echo json_encode(['success' => true, 'message' => 'Element updated successfully']);
            } else {
                http_response_code(503);
                echo json_encode(['success' => false, 'message' => 'Unable to update element']);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}

function handleDeleteRequest($action) {
    global $db;
    
    switch ($action) {
        case 'links':
            $link = new Link($db);
            
            // Get ID from request
            $link->id = isset($_GET['id']) ? $_GET['id'] : die();
            
            if ($link->delete()) {
                echo json_encode(['success' => true, 'message' => 'Link deleted successfully']);
            } else {
                http_response_code(503);
                echo json_encode(['success' => false, 'message' => 'Unable to delete link']);
            }
            break;
            
        case 'shortlink_elements':
            $element = new ShortlinkElement($db);
            
            // Get ID from request
            $element->id = isset($_GET['id']) ? $_GET['id'] : die();
            
            if ($element->delete()) {
                echo json_encode(['success' => true, 'message' => 'Element deleted successfully']);
            } else {
                http_response_code(503);
                echo json_encode(['success' => false, 'message' => 'Unable to delete element']);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}