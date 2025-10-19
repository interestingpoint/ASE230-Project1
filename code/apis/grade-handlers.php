<?php
//framework taken from module1/code/6_RestAPI/api/handlers.php and PDO code modified from module1/code/2_Connect_PHP_with_MySQL/crud_pdo.php

require_once 'models/grade.php';

$servername = "localhost";
$username   = "root";
$password   = "freezerburn15"; 
$dbname     = "public_apis";

function get_all_grades()//lecture 6, part 2, page 31
{

    $grades = load_grades();
    echo json_encode([
        'success' => true,
        'data' => $grades,
        'count' => count($grades)
    ]);
}


function get_grade($id)//lecture 6, part 2, page 32
{
    $grades = load_grades();

    foreach ($grades as $grade) {
        if ($grade['id'] == $id) {
            echo json_encode([
                'success' => true,
                'data' => $grade
            ]);
            return;
        }
    }

    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'grade not found'
    ]);
}


function create_grade()//lecture 6, part 2, page 33
{

    $input = getRequestDatagrade();
    if (!$input) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid JSON data'
        ]);
    return;
    }


    $new_object = new grade();
    $new_object->setname($input['name'] ?? 'A');
    $new_object->setEnglish($input['English'] ?? 'B');
    $new_object->setMath($input['Math'] ?? 'C');
    $new_object->setSocial_Studies($input['Social_Studies'] ?? 'D');

    $gradesarr = $new_object->toArray();


    save_grades($gradesarr);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'grade created successfully',
        'data' => $gradesarr
    ]);
}


function grade_update($id)//lecture 6, part 2, page 36
{

    $input = getRequestDatagrade();

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
        $new_name = $input['name'] ?? 'A';
        $new_English = $input['English'] ?? 'B';
        $new_Math =  $input['Math'] ?? 'C';
        $new_Social_Studies =  $input['Social_Studies'] ?? 'D';
        $dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,                 
        ];
        $pdo = new PDO($dsn, $username, $password, $options); //lecture 2, part 4, page 11
        $sql = "UPDATE grades
                    SET name = :name, English = :English, Math = :Math, Social_Studies = :Social_Studies
                    WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $new_name,
            ':English' => $new_English,
            ':Math' => $new_Math,
            ':Social_Studies' => $new_Social_Studies,
            ':id' => $id,
        ]);

        if ($stmt->rowCount() > 0) {

        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'grade not found'
            ]);
            return;
        }


        echo json_encode([
            'success' => true,
            'message' => 'grade upgraded successfully',
        ]);  

        } catch (PDOException $e) {
        echo "Error (UPgrade): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }





}





function delete_grade($id)//lecture 6, part 2, page 36
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
    $sql = "DELETE FROM grades WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'grade deleted successfully',
            ]);  
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'grade not found'
            ]);
            return;
        }

        } catch (PDOException $e) {
        echo "Error (UPgrade): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }}



function load_grades()//lecture 6, part 2, page 27
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
        $sql    = "SELECT id, name, English, Math, Social_Studies FROM grades"; //lecture 2, part 4, page 8
        $rows   = $pdo->query($sql)->fetchAll();
        return $rows ?: [];
    }
    catch(PDOException $e){
        exit("Connection failed: " . htmlspecialchars($e->getMessage()));

    }

}


function save_grades($gradesarr)//lecture 6, part 2, page 28
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
    $sql  = "INSERT INTO grades (name, English, Math, Social_Studies) VALUES (:name, :English, :Math, :Social_Studies)"; //lecture 2, part 4, page 4
    $stmt = $pdo->prepare($sql);

 
    $stmt->execute([
        ':name' => $gradesarr['name'],
        ':English' => $gradesarr['English'],
        ':Math' => $gradesarr['Math'],
        ':Social_Studies' => $gradesarr['Social_Studies']
    ]);


    } catch (Error $e) {
        echo "Error (CREATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }
}


function getRequestDatagrade()//lecture 6, part 2, page 30
{
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}


