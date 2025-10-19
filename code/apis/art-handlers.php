<?php
//framework taken from module1/code/6_RestAPI/api/handlers.php and PDO code modified from module1/code/2_Connect_PHP_with_MySQL/crud_pdo.php


require_once 'models/art.php';

$servername = "localhost";
$username   = "root";
$password   = "freezerburn15"; 
$dbname     = "public_apis";

function get_all_arts()//lecture 6, part 2, page 31
{
    $arts = load_arts();
    echo json_encode([
        'success' => true,
        'data' => $arts,
        'count' => count($arts)
    ]);
}


function get_art($id)//lecture 6, part 2, page 32
{
    $arts = load_arts();

    foreach ($arts as $art) {
        if ($art['id'] == $id) {
            echo json_encode([
                'success' => true,
                'data' => $art
            ]);
            return;
        }
    }

    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'art not found'
    ]);
}


function create_art()//lecture 6, part 2, page 33
{
    $input = getRequestDataart();
    if (!$input) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid JSON data'
        ]);
    ;
    }


    $new_object = new art();
    $new_object->setname($input['name'] ?? 'An IoT art');
    $new_object->setsize($input['size'] ?? 'An IoT art');
    $new_object->setartist($input['artist'] ?? 'An IoT art');

    $artsarr = $new_object->toArray();

    save_arts($artsarr);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'art created successfully',
        'data' => $artsarr
    ]);
}


function art_update($id)//lecture 6, part 2, page 36
{
    $input = getRequestDataart();

    if (!$input) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid JSON data'
        ]);
        ;
    }
    $servername = "localhost";//lecture 2, part 4, page 3
    $username = "root";
    $password = "freezerburn15";
    $dbname = "public_apis";
    
    try {
        $new_name = $input['name'] ?? '';
        $new_size = $input['size'] ?? '';
        $new_artist =  $input['artist'] ?? '';
        $dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,                  
        ];
        $pdo = new PDO($dsn, $username, $password, $options); //lecture 2, part 4, page 11
        $sql = "UPDATE arts
                    SET name = :name, size = :size, artist = :artist
                    WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $new_name,
            ':size' => $new_size,
            ':artist' => $new_artist,
            ':id' => $id,
        ]);

        if ($stmt->rowCount() > 0) {

        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'art not found'
            ]);
            return;
        }


        echo json_encode([
            'success' => true,
            'message' => 'art updated successfully',
        ]);  

        } catch (PDOException $e) {
        echo "Error (UPDATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }




}






function delete_art($id)//lecture 6, part 2, page 36
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
    $sql = "DELETE FROM arts WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'art deleted successfully',
            ]);  
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'art not found'
            ]);
            return;
        }

        } catch (PDOException $e) {
        echo "Error (UPDATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }}



function load_arts()//lecture 6, part 2, page 27
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
        $sql    = "SELECT id, name, size, artist FROM arts"; //lecture 2, part 4, page 8
        $rows   = $pdo->query($sql)->fetchAll();
        return $rows ?: [];
    }
    catch(PDOException $e){
        exit("Connection failed: " . htmlspecialchars($e->getMessage()));

    }

}


function save_arts($artsarr)//lecture 6, part 2, page 28
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
    $sql  = "INSERT INTO arts (name, size, artist) VALUES (:name, :size, :artist)"; //lecture 2, part 4, page 4
    $stmt = $pdo->prepare($sql);


    $stmt->execute([
        ':name' => $artsarr['name'],
        ':size' => $artsarr['size'],
        ':artist' => $artsarr['artist']
    ]);



    } catch (Error $e) {
        echo "Error (CREATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }
}


function getRequestDataart()//lecture 6, part 2, page 30
{
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}


