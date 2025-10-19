<?php

//framework taken from module1/code/6_RestAPI/api/handlers.php and PDO code modified from module1/code/2_Connect_PHP_with_MySQL/crud_pdo.php
require_once 'models/announcement.php';

$servername = "localhost";
$username   = "root";
$password   = "freezerburn15"; 
$dbname     = "public_apis";

function get_all_announcements()//lecture 6, part 2, page 31
{
    $announcements = load_announcements();
    echo json_encode([
        'success' => true,
        'data' => $announcements,
        'count' => count($announcements)
    ]);
}


function get_announcement($id)//lecture 6, part 2, page 32
{
    $announcements = load_announcements();

    foreach ($announcements as $announcement) {
        if ($announcement['id'] == $id) {
            echo json_encode([
                'success' => true,
                'data' => $announcement
            ]);
            return;
        }
    }

    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'announcement not found'
    ]);
}


function create_announcement()//lecture 6, part 2, page 33
{
    $input = getRequestDataAnn();
    if (!$input) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid JSON data'
        ]);
    return;
    }


    $new_object = new announcement();
    $new_object->setannouncement($input['announcement'] ?? 'An IoT announcement');
    $new_object->setlocation($input['location'] ?? 'An IoT announcement');
    $new_object->setdate_announced();

    $announcementsarr = $new_object->toArray();

    save_announcements($announcementsarr);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'announcement created successfully',
        'data' => $announcementsarr
    ]);
}


function announcement_update($id)//lecture 6, part 2, page 36
{
    $input = getRequestDataAnn();

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
        $new_announcement = $input['announcement'] ?? '';
        $new_location = $input['location'] ?? '';
        $new_date = date('Y-m-d H:i:s');
        $dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,                  
        ];
        $pdo = new PDO($dsn, $username, $password, $options); //lecture 2, part 4, page 11
        $sql = "UPDATE announcements
                    SET announcement = :announcement, location = :location, date_announced = :date_announced
                    WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':announcement' => $new_announcement,
            ':location' => $new_location,
            ':date_announced' => $new_date,
            ':id' => $id,
        ]);

        if ($stmt->rowCount() > 0) {

        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'announcement not found'
            ]);
            return;
        }


        echo json_encode([
            'success' => true,
            'message' => 'announcement updated successfully',
        ]);  

        } catch (PDOException $e) {
        echo "Error (UPDATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }





}






function delete_announcement($id)//lecture 6, part 2, page 36
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
    $sql = "DELETE FROM announcements WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'announcement deleted successfully',
            ]);  
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'announcement not found'
            ]);
            return;
        }

        } catch (PDOException $e) {
        echo "Error (UPDATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }}



function load_announcements()//lecture 6, part 2, page 27
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
        $sql    = "SELECT id, announcement, location, date_announced FROM announcements"; //lecture 2, part 4, page 8
        $rows   = $pdo->query($sql)->fetchAll();
        return $rows ?: [];
    }
    catch(PDOException $e){
        exit("Connection failed: " . htmlspecialchars($e->getMessage()));

    }

}


function save_announcements($announcementsarr)//lecture 6, part 2, page 28
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
    $sql  = "INSERT INTO announcements (announcement, location, date_announced) VALUES (:announcement, :location, :date_announced)"; //lecture 2, part 4, page 4
    $stmt = $pdo->prepare($sql);


    $stmt->execute([
        ':announcement' => $announcementsarr['announcement'],
        ':location' => $announcementsarr['location'],
        ':date_announced' => $announcementsarr['date_announced']
    ]);



    } catch (Error $e) {
        echo "Error (CREATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }
}


function getRequestDataAnn()//lecture 6, part 2, page 30
{
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}


