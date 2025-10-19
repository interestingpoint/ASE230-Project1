<?php
//framework taken from module1/code/6_RestAPI/api/handlers.php and PDO code modified from module1/code/2_Connect_PHP_with_MySQL/crud_pdo.php


require_once 'models/date.php';

$servername = "localhost";
$username   = "root";
$password   = "freezerburn15"; 
$dbname     = "public_apis";

function get_all_dates()//lecture 6, part 2, page 31
{
    $dates = load_dates();
    echo json_encode([
        'success' => true,
        'data' => $dates,
        'count' => count($dates)
    ]);
}


function get_date($id)//lecture 6, part 2, page 32
{
    $dates = load_dates();

    foreach ($dates as $date) {
        if ($date['id'] == $id) {
            echo json_encode([
                'success' => true,
                'data' => $date
            ]);
            return;
        }
    }

    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'date not found'
    ]);
}


function create_date()//lecture 6, part 2, page 33
{

    $input = getRequestDatadate();
    if (!$input) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid JSON data'
        ]);
    ;
    }


    $new_object = new date();
    $new_object->setday($input['day'] ?? 'An IoT date');
    $new_object->settime($input['time'] ?? 'An IoT date');
    $new_object->setevent($input['event'] ?? 'An IoT date');


    $datesarr = $new_object->toArray();


    save_dates($datesarr);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'date created successfully',
        'data' => $datesarr
    ]);
}


function date_update($id)//lecture 6, part 2, page 36
{

    $input = getRequestDatadate();

    if (!$input) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid JSON data'
        ]);
        return;
    }
    $servername = "localhost"; //lecture 2, part 4, page 3
    $username = "root";
    $password = "freezerburn15";
    $dbname = "public_apis";
    
    try {
        $new_day = $input['day'] ?? '';
        $new_time = $input['time'] ?? '';
        $new_event =  $input['event'] ?? '';
        $dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,                  
        ];
        $pdo = new PDO($dsn, $username, $password, $options); //lecture 2, part 4, page 11
        $sql = "UPDATE dates
                    SET day = :day, time = :time, event = :event
                    WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':day' => $new_day,
            ':time' => $new_time,
            ':event' => $new_event,
            ':id' => $id,
        ]);

        if ($stmt->rowCount() > 0) {

        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'date not found'
            ]);
            return;
        }


        echo json_encode([
            'success' => true,
            'message' => 'date updated successfully',
        ]);  

        } catch (PDOException $e) {
        echo "Error (UPDATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }





}






function delete_date($id)//lecture 6, part 2, page 36
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
    $sql = "DELETE FROM dates WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'date deleted successfully',
            ]);  
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'date not found'
            ]);
            return;
        }

        } catch (PDOException $e) {
        echo "Error (UPDATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }}



function load_dates()//lecture 6, part 2, page 27
{
    $servername = "localhost";//lecture 2, part 4, page 3
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
        $sql    = "SELECT id, day, time, event FROM dates"; //lecture 2, part 4, page 8
        $rows   = $pdo->query($sql)->fetchAll();
        return $rows ?: [];
    }
    catch(PDOException $e){
        exit("Connection failed: " . htmlspecialchars($e->getMessage()));

    }

}


function save_dates($datesarr)//lecture 6, part 2, page 28
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
    $sql  = "INSERT INTO dates (day, time, event) VALUES (:day, :time, :event)"; //lecture 2, part 4, page 4
    $stmt = $pdo->prepare($sql);


    $stmt->execute([
        ':day' => $datesarr['day'],
        ':time' => $datesarr['time'],
        ':event' => $datesarr['event']
    ]);



    } catch (Error $e) {
        echo "Error (CREATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }
}


function getRequestDatadate()//lecture 6, part 2, page 30
{
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}


