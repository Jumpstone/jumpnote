<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../includes/DotEnv.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../models/Link.php';
require_once __DIR__ . '/../models/ShortlinkElement.php';

// Load environment variables
try {
    DotEnv::load(__DIR__ . '/../.env');
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Environment configuration error: ' . $e->getMessage()]);
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
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db === null) {
        throw new Exception("Database connection is null");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection error: ' . $e->getMessage()]);
    exit();
}

// Handle API requests
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($method) {
    case 'GET':
        handleGetRequest($action, $db);
        break;
    case 'POST':
        handlePostRequest($action, $db);
        break;
    case 'PUT':
        handlePutRequest($action, $db);
        break;
    case 'DELETE':
        handleDeleteRequest($action, $db);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

function handleGetRequest($action, $db) {
    switch ($action) {
        case 'links':
            // Check if a specific link ID is requested
            $linkId = isset($_GET['id']) ? $_GET['id'] : null;
            
            if ($linkId) {
                // Get a specific link by ID
                $link = new Link($db);
                $stmt = $link->getById($linkId);
                $linkData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($linkData) {
                    $linkArray = [
                        'id' => $linkData['id'],
                        'name' => $linkData['name'],
                        'url' => $linkData['url'],
                        'icon' => $linkData['icon_url'],
                        'sort_order' => $linkData['sort_order']
                    ];
                    echo json_encode($linkArray);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Link not found']);
                }
            } else {
                // Get all links
                require_once __DIR__ . '/../controllers/DashboardController.php';
                $controller = new DashboardController($db);
                $links = $controller->getLinks();
                echo json_encode($links);
            }
            break;
            
        case 'shortlink_elements':
            try {
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
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Error fetching elements: ' . $e->getMessage()]);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}

function handlePostRequest($action, $db) {
    switch ($action) {
        case 'links':
            $link = new Link($db);
            
            // Get posted data
            $data = json_decode(file_get_contents("php://input"));
            
            // Validate required fields
            if (!isset($data->name) || !isset($data->url)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Name and URL are required']);
                return;
            }
            
            $link->name = $data->name;
            $link->url = $data->url;
            $link->icon_url = isset($data->icon_url) ? $data->icon_url : '';
            $link->sort_order = isset($data->sort_order) ? $data->sort_order : $link->getNextSortOrder();
            
            if ($link->create()) {
                // Return the created link with its ID
                $createdLink = [
                    'id' => $db->lastInsertId(),
                    'name' => $link->name,
                    'url' => $link->url,
                    'icon' => $link->icon_url,
                    'sort_order' => $link->sort_order
                ];
                echo json_encode(['success' => true, 'message' => 'Link created successfully', 'link' => $createdLink]);
            } else {
                http_response_code(503);
                echo json_encode(['success' => false, 'message' => 'Unable to create link']);
            }
            break;
            
        case 'shortlink_elements':
            try {
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
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error creating element: ' . $e->getMessage()]);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}

function handlePutRequest($action, $db) {
    switch ($action) {
        case 'links':
            $link = new Link($db);
            
            // Get posted data
            $data = json_decode(file_get_contents("php://input"));
            
            // Validate required fields
            if (!isset($data->id) || !isset($data->name) || !isset($data->url)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID, Name and URL are required']);
                return;
            }
            
            $link->id = $data->id;
            $link->name = $data->name;
            $link->url = $data->url;
            $link->icon_url = isset($data->icon_url) ? $data->icon_url : '';
            $link->sort_order = isset($data->sort_order) ? $data->sort_order : 0;
            
            if ($link->update()) {
                echo json_encode(['success' => true, 'message' => 'Link updated successfully']);
            } else {
                http_response_code(503);
                echo json_encode(['success' => false, 'message' => 'Unable to update link']);
            }
            break;
            
        case 'shortlink_elements':
            try {
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
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error updating element: ' . $e->getMessage()]);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}

function handleDeleteRequest($action, $db) {
    switch ($action) {
        case 'links':
            $link = new Link($db);
            
            // Get ID from request
            $id = isset($_GET['id']) ? $_GET['id'] : null;
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Link ID is required']);
                return;
            }
            
            $link->id = $id;
            
            if ($link->delete()) {
                echo json_encode(['success' => true, 'message' => 'Link deleted successfully']);
            } else {
                http_response_code(503);
                echo json_encode(['success' => false, 'message' => 'Unable to delete link']);
            }
            break;
            
        case 'shortlink_elements':
            try {
                $element = new ShortlinkElement($db);
                
                // Get ID from request
                $element->id = isset($_GET['id']) ? $_GET['id'] : die();
                
                if ($element->delete()) {
                    echo json_encode(['success' => true, 'message' => 'Element deleted successfully']);
                } else {
                    http_response_code(503);
                    echo json_encode(['success' => false, 'message' => 'Unable to delete element']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error deleting element: ' . $e->getMessage()]);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}