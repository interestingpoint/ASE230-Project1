<?php
//framework taken from module1/code/6_RestAPI/api/handlers.php and PDO code modified from module1/code/2_Connect_PHP_with_MySQL/crud_pdo.php


require_once 'models/homework.php';

$servername = "localhost";
$username   = "root";
$password   = "freezerburn15"; 
$dbname     = "private_apis";

function get_all_homeworks()//lecture 6, part 2, page 31
{
    $homeworks = load_homeworks();
    echo json_encode([
        'success' => true,
        'data' => $homeworks,
        'count' => count($homeworks)
    ]);
}


function get_homework($id)//lecture 6, part 2, page 32
{
    $homeworks = load_homeworks();

    foreach ($homeworks as $homework) {
        if ($homework['id'] == $id) {
            echo json_encode([
                'success' => true,
                'data' => $homework
            ]);
            return;
        }
    }

    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'homework not found'
    ]);
}


function create_homework()//lecture 6, part 2, page 33
{

    $input = getRequestDatahomework();
    if (!$input) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid JSON data'
        ]);
    ;
    }


    $new_object = new homework();
    $new_object->setname($input['name'] ?? '');
    $new_object->setquestions($input['questions'] ?? '');
    $new_object->setclass($input['class'] ?? '');


    $homeworksarr = $new_object->toArray();


    save_homeworks($homeworksarr);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'homework created successfully',
        'data' => $homeworksarr
    ]);
}


function homework_update($id)//lecture 6, part 2, page 36
{

    $input = getRequestDatahomework();

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
    $dbname = "private_apis";
    
    try {
        $new_name = $input['name'] ?? '';
        $new_questions = $input['questions'] ?? '';
        $new_class =  $input['class'] ?? '';
        $dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,                  
        ];
        $pdo = new PDO($dsn, $username, $password, $options);//lecture 2, part 4, page 11
        $sql = "UPDATE homeworks
                    SET name = :name, questions = :questions, class = :class
                    WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $new_name,
            ':questions' => $new_questions,
            ':class' => $new_class,
            ':id' => $id,
        ]);

        if ($stmt->rowCount() > 0) {

        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'homework not found'
            ]);
            return;
        }


        echo json_encode([
            'success' => true,
            'message' => 'homework updated successfully',
        ]);  

        } catch (PDOException $e) {
        echo "Error (UPDATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }





}






function delete_homework($id)//lecture 6, part 2, page 36
{
    $servername = "localhost"; //lecture 2, part 4, page 3
    $username = "root";
    $password = "freezerburn15";
    $dbname = "private_apis";
    $dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,                  
    ];
    try{
    $pdo = new PDO($dsn, $username, $password, $options); //lecture 2, part 4, page 13
    $sql = "DELETE FROM homeworks WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'homework deleted successfully',
            ]);  
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'homework not found'
            ]);
            return;
        }

        } catch (PDOException $e) {
        echo "Error (UPDATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }}



function load_homeworks()//lecture 6, part 2, page 27
{
    $servername = "localhost"; //lecture 2, part 4, page 3
    $username   = "root";
    $password   = "freezerburn15"; 
    $dbname     = "private_apis";
    try {
        $dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,                 
        ];
        $pdo = new PDO($dsn, $username, $password, $options);
        $sql    = "SELECT id, name, questions, class FROM homeworks"; //lecture 2, part 4, page 8
        $rows   = $pdo->query($sql)->fetchAll();
        return $rows ?: [];
    }
    catch(PDOException $e){
        exit("Connection failed: " . htmlspecialchars($e->getMessage()));

    }

}


function save_homeworks($homeworksarr)//lecture 6, part 2, page 28
{
    $servername = "localhost"; //lecture 2, part 4, page 3
    $username   = "root";
    $password   = "freezerburn15"; 
    $dbname     = "private_apis";
    try {
    $dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,                  
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
    $sql  = "INSERT INTO homeworks (name, questions, class) VALUES (:name, :questions, :class)"; //lecture 2, part 4, page 4
    $stmt = $pdo->prepare($sql);


    $stmt->execute([
        ':name' => $homeworksarr['name'],
        ':questions' => $homeworksarr['questions'],
        ':class' => $homeworksarr['class']
    ]);



    } catch (Error $e) {
        echo "Error (CREATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }
}


function getRequestDatahomework()//lecture 6, part 2, page 30
{
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}


