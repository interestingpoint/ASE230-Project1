<?php
//framework taken from module1/code/6_RestAPI/api/handlers.php and PDO code modified from module1/code/2_Connect_PHP_with_MySQL/crud_pdo.php


require_once 'models/iot-device.php';

$servername = "localhost";
$username   = "root";
$password   = "freezerburn15"; 
$dbname     = "public_apis";

function get_all_devices()//lecture 6, part 2, page 31
{
    $devices = load_devices();
    echo json_encode([
        'success' => true,
        'data' => $devices,
        'count' => count($devices)
    ]);
}


function get_device($id)//lecture 6, part 2, page 32
{
    $devices = load_devices();

    foreach ($devices as $device) {
        if ($device['id'] == $id) {
            echo json_encode([
                'success' => true,
                'data' => $device
            ]);
            return;
        }
    }

    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'Device not found'
    ]);
}


function create_device()//lecture 6, part 2, page 33
{

    $input = getRequestData();
    if (!$input) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid JSON data'
        ]);
    ;
    }


    $new_object = new Iot_device();
    $new_object->setType($input['name'] ?? 'An IoT Device');
    $new_object->setDescription($input['age'] ?? 'An IoT Device');
    $new_object->setOnline($input['major'] ?? 0);


    $devicesarr = $new_object->toArray();


    save_devices($devicesarr);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Device created successfully',
        'data' => $devicesarr
    ]);
}


function device_update($id)//lecture 6, part 2, page 36
{

    $input = getRequestData();

    if (!$input) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid JSON data'
        ]);
        ;
    }
    $servername = "localhost"; //lecture 2, part 4, page 3
    $username = "root";
    $password = "freezerburn15";
    $dbname = "public_apis";
    
    try {
        $new_type = $input['type'] ?? '';
        $new_description = $input['description'] ?? '';
        $new_online = $input['is_online'] ?? 0;
        $dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,                 
        ];
        $pdo = new PDO($dsn, $username, $password, $options); //lecture 2, part 4, page 11
        $sql = "UPDATE iot_devices
                    SET device_type = :type, device_description = :description, is_online = :online
                    WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':type' => $new_type,
            ':description' => $new_description,
            ':online' => $new_online,
            ':id' => $id,
        ]);

        if ($stmt->rowCount() > 0) {

        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Device not found'
            ]);
            return;
        }


        echo json_encode([
            'success' => true,
            'message' => 'Device updated successfully',
        ]);  

        } catch (PDOException $e) {
        echo "Error (UPDATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }





}

function device_toggle($id){
    $servername = "localhost";
    $username = "root";
    $password = "freezerburn15";
    $dbname = "public_apis";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,                  
    ];
    $devices = load_devices();
    foreach ($devices as $device) {
        if ($device['id'] == $id) {
            $final_device = $device;
            break;
        }
    }
    if (!isset($final_device)){
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Device not found'
        ]);
        return;
    }
    if ($final_device["is_online"] == 0){
        $final_value = 1;
    }
    else{
        $final_value = 0;
    }
    $dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";
    try{
        $pdo = new PDO($dsn, $username, $password, $options);
        $sql = "UPDATE iot_devices
                    SET is_online = :is_online
                    WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':is_online' => $final_value,
            ':id' => $id
        ]);
        echo json_encode([
            'success' => true ]);
        } catch (PDOException $e) {
        echo "Error (UPDATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }

    }




function delete_device($id) //lecture 6, part 2, page 36
{
    $servername = "localhost"; //lecture 2, part 4, page 3
    $username = "root";
    $password = "freezerburn15";
    $dbname = "public_apis";
    $dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,                  
    ];
    try{
    $pdo = new PDO($dsn, $username, $password, $options); //lecture 2, part 4, page 13
    $sql = "DELETE FROM iot_devices WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Device deleted successfully',
            ]);  
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Device not found'
            ]);
            return;
        }

        } catch (PDOException $e) {
        echo "Error (UPDATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }}



function load_devices()//lecture 6, part 2, page 27
{
    $servername = "localhost"; //lecture 2, part 4, page 3
    $username   = "root";
    $password   = "freezerburn15"; 
    $dbname     = "public_apis";
    try {
        $dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,                  
        ];
        $pdo = new PDO($dsn, $username, $password, $options);
        $sql    = "SELECT id, device_type, device_description, is_online FROM iot_devices"; //lecture 2, part 4, page 8
        $rows   = $pdo->query($sql)->fetchAll();
        return $rows ?: [];
    }
    catch(PDOException $e){
        exit("Connection failed: " . htmlspecialchars($e->getMessage()));

    }

}


function save_devices($devicesarr)//lecture 6, part 2, page 28
{
    $servername = "localhost"; //lecture 2, part 4, page 3
    $username   = "root";
    $password   = "freezerburn15"; 
    $dbname     = "public_apis";
    try {
    $dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,                  
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
    $sql  = "INSERT INTO iot_devices (device_type, device_description, is_online) VALUES (:type, :description, :is_online)"; //lecture 2, part 4, page 4
    $stmt = $pdo->prepare($sql);

 
    $stmt->execute([
        ':type' => $devicesarr['type'],
        ':description' => $devicesarr['description'],
        ':is_online' => $devicesarr['online']
    ]);

  

    } catch (PDOException $e) {
        echo "Error (CREATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }
}


function getRequestData()//lecture 6, part 2, page 30
{
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}


