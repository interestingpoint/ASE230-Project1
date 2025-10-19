<?php
//framework taken from module1/code/6_RestAPI/api/index.php
require_once 'apis/iot-device-handlers.php';
require_once 'apis/announcement-handlers.php';
require_once 'apis/stock-ticker-handlers.php';
require_once 'apis/audio-handlers.php';
require_once 'apis/art-handlers.php';
require_once 'apis/candidate-handlers.php';
require_once 'apis/date-handlers.php';
require_once 'apis/grade-handlers.php';
require_once 'apis/employee-handlers.php';
require_once 'apis/homework-handlers.php';
require_once 'bearer_auth.php';

header('Content-Type: application/json'); //lecture 6, part 2, page 6
header('Access-Control-Allow-Origin: *'); //lecture 6, part 2, page 7
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); //lecture 6, part 2, page 7
header('Access-Control-Allow-Headers: Content-Type'); //lecture 6, part 2, page 7

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { //lecture 6, part 2, page 8
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD']; //lecture 6, part 2, page 13
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($path, '/');



$segments = explode('/', $path); //lecture 6, part 2, page 14
$resource = $segments[0] ?? '';
if(count($segments)<=2){
    $id = $segments[1] ?? null; } //this part is mine. I added this to parse two endpoints that were using PUT.
else{
    $mode = $segments[1];
    $id = $segments[2];
}




$method = $_SERVER['REQUEST_METHOD'];

if (empty($resource)) { //lecture 6, part 2, page 16

    echo json_encode([
        'message' => 'A collection of APIs',
        'api-1' => 'IoT device manager',
        'endpoints-1' => [
            'GET /iot-devices' => 'Get all devices',
            'GET /iot-devices/{id}' => 'Get device by ID',
            'POST /iot-devices' => 'Create new device',
            'PUT /iot-devices/update/{id}' => 'Update device',
            'PUT /iot-devices/toggle/{id}' => 'Toggle device activity',
            'DELETE /iot-devices/{id}' => 'Delete device'
        ],
        'api-2' => 'Announcement Manager',
        'endpoints-2' => [
            'GET /announcements' => 'Get all announcements',
            'GET /announcements/{id}' => 'Get announcement by ID',
            'POST /announcements' => 'Create new announcement',
            'PUT /announcements/{id}' => 'Update announcement',
            'DELETE /announcements/{id}' => 'Delete announcement'
        ],
        'api-3' => 'Stock Tickers',
        'endpoints-3' => [
            'GET /stock-tickers' => 'Get all tickers',
            'GET /stock-tickers/{symbol}' => 'Get ticker by symbol',
            'POST /stock-tickers' => 'Create new ticker',
            'PUT /stock-tickers/{symbol}' => 'Update ticker',
            'DELETE /stock-tickers/{symbol}' => 'Delete ticker'
        ],
        'api-4' => 'Audios',
        'endpoints-4' => [
            'GET /audios' => 'Get all audios',
            'GET /audios/{id}' => 'Get audio by ID',
            'POST /audios' => 'Create new audio',
            'PUT /audios/{id}' => 'Update audio',
            'DELETE /audio/{id}' => 'Delete audio'
        ],
        'api-5' => 'Arts',
        'endpoints-5' => [
            'GET /arts' => 'Get all arts',
            'GET /arts/{id}' => 'Get art by ID',
            'POST /arts' => 'Create new art',
            'PUT /arts/{id}' => 'Update art',
            'DELETE /arts/{id}' => 'Delete art'
        ],
        'api-6' => 'Candidates',
        'endpoints-6' => [
            'GET /candidates' => 'Get all candidates',
            'GET /candidates/{id}' => 'Get candidate by ID',
            'POST /candidates' => 'Create new candidate',
            'PUT /candidates/{id}' => 'Update candidate',
            'DELETE /candidates/{id}' => 'Delete candidate'
        ],
        'api-7' => 'Dates',
        'endpoints-7' => [
            'GET /dates' => 'Get all dates',
            'GET /dates/{id}' => 'Get date by ID',
            'POST /dates' => 'Create new date',
            'PUT /dates/{id}' => 'Update date',
            'DELETE /dates/{id}' => 'Delete date'
        ],
        'api-8' => 'Grades',
        'endpoints-8' => [
            'GET /grades' => 'Get all grades',
            'GET /grades/{id}' => 'Get grade by ID',
            'POST /grades' => 'Create new grade',
            'PUT /grades/{id}' => 'Update grade',
            'DELETE /grades/{id}' => 'Delete grade'
        ],
        'bearer-api-1' => 'Employees',
        'bearer-endpoints-1' => [
            'GET /employees' => 'Get all employees',
            'GET /employees/{id}' => 'Get employee by ID',
            'POST /employees' => 'Create new employee',
            'PUT /employees/{id}' => 'Update employee',
            'DELETE /employees/{id}' => 'Delete employee'
        ],
        'bearer-api-2' => 'Homeworks',
        'bearer-endpoints-2' => [
            'GET /homeworks' => 'Get all homeworks',
            'GET /homeworks/{id}' => 'Get homework by ID',
            'POST /homeworks' => 'Create new homework',
            'PUT /homeworks/{id}' => 'Update homework',
            'DELETE /homeworks/{id}' => 'Delete homework'
        ]
    ]);

    exit;
}


//the rest of this file is based on lecture 6, part 2, page 18-19
if ($resource === 'iot-devices') {
    
    $object_id = isset($id) ? (int)$id : null;
    
    switch ($method) {
        case 'GET':
            if ($object_id) {
                get_device($object_id);
            } else {
                get_all_devices();
            }
            break;
            
        case 'POST':
            create_device();
            break;
            
        case 'PUT':

            if ($object_id) {
                if ($mode == 'update'){
                    device_update($id);
                }
                else if ($mode == 'toggle') {
                    device_toggle($id);
                }
                else{
                    http_response_code(400);
                    echo json_encode(['error' => 'Mode required']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        case 'DELETE':
            if ($object_id) {
                delete_device($object_id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} 
else if ($resource === 'announcements') {
    $object_id = isset($id) ? (int)$id : null;
    
    switch ($method) {
        case 'GET':
            if ($object_id) {
                get_announcement($object_id);
            } else {
                get_all_announcements();
            }
            break;
            
        case 'POST':
            create_announcement();
            break;
            
        case 'PUT':
            if ($object_id){
                announcement_update($id);}
            else{
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        case 'DELETE':
            if ($object_id) {
                delete_announcement($object_id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} 
else if ($resource === 'stock-tickers') {
    switch ($method) {
        case 'GET':
            if ($id) {
                get_ticker($id);
            } else {
                get_all_tickers();
            }
            break;
            
        case 'POST':
            create_ticker();
            break;
            
        case 'PUT':
            if ($id){
                ticker_update($id);}
            else{
                http_response_code(400);
                echo json_encode(['error' => 'Stock Symbol required']);
            }
            break;
            
        case 'DELETE':
            if ($id) {
                delete_ticker($id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} else if ($resource === 'audios') {
    $object_id = isset($id) ? (int)$id : null;
    
    switch ($method) {
        case 'GET':
            if ($object_id) {
                get_audio($object_id);
            } else {
                get_all_audios();
            }
            break;
            
        case 'POST':
            create_audio();
            break;
            
        case 'PUT':
            if ($object_id){
                audio_update($id);}
            else{
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        case 'DELETE':
            if ($object_id) {
                delete_audio($object_id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} else if ($resource === 'arts') {
    $object_id = isset($id) ? (int)$id : null;
    
    switch ($method) {
        case 'GET':
            if ($object_id) {
                get_art($object_id);
            } else {
                get_all_arts();
            }
            break;
            
        case 'POST':
            create_art();
            break;
            
        case 'PUT':
            if ($object_id){
                art_update($id);}
            else{
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        case 'DELETE':
            if ($object_id) {
                delete_art($object_id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} else if ($resource === 'candidates') {
    $object_id = isset($id) ? (int)$id : null;
    
    switch ($method) {
        case 'GET':
            if ($object_id) {
                get_candidate($object_id);
            } else {
                get_all_candidates();
            }
            break;
            
        case 'POST':
            create_candidate();
            break;
            
        case 'PUT':
            if ($object_id){
                candidate_update($id);}
            else{
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        case 'DELETE':
            if ($object_id) {
                delete_candidate($object_id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} else if ($resource === 'dates') {
    $object_id = isset($id) ? (int)$id : null;
    
    switch ($method) {
        case 'GET':
            if ($object_id) {
                get_date($object_id);
            } else {
                get_all_dates();
            }
            break;
            
        case 'POST':
            create_date();
            break;
            
        case 'PUT':
            if ($object_id){
                date_update($id);}
            else{
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        case 'DELETE':
            if ($object_id) {
                delete_date($object_id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} else if ($resource === 'grades') {
    $object_id = isset($id) ? (int)$id : null;

    switch ($method) {
        case 'GET':
            if ($object_id) {
                get_grade($object_id);
            } else {
                get_all_grades();
            }
            break;
            
        case 'POST':
            create_grade();
            break;
            
        case 'PUT':
            if ($object_id){
                grade_update($id);}
            else{
                http_response_code(400);
                echo json_encode(value: ['error' => 'Device ID required']);
            }
            break;
            
        case 'DELETE':
            if ($object_id) {
                delete_grade($object_id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} else if ($resource === 'employees') {
    $object_id = isset($id) ? (int)$id : null;
    $user = requireAuth();
    switch ($method) {
        case 'GET':
            if ($object_id) {
                get_employee($object_id);
            } else {
                get_all_employees();
            }
            break;
            
        case 'POST':
            create_employee();
            break;
            
        case 'PUT':
            if ($object_id){
                employee_update($id);}
            else{
                http_response_code(400);
                echo json_encode(value: ['error' => 'Device ID required']);
            }
            break;
            
        case 'DELETE':
            if ($object_id) {
                delete_employee($object_id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} else if ($resource === 'homeworks') {
    $object_id = isset($id) ? (int)$id : null;
    $user = requireAuth();
    switch ($method) {
        case 'GET':
            if ($object_id) {
                get_homework($object_id);
            } else {
                get_all_homeworks();
            }
            break;
            
        case 'POST':
            create_homework();
            break;
            
        case 'PUT':
            if ($object_id){
                homework_update($id);}
            else{
                http_response_code(400);
                echo json_encode(value: ['error' => 'Device ID required']);
            }
            break;
            
        case 'DELETE':
            if ($object_id) {
                delete_homework($object_id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Device ID required']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Resource not found']);
}
