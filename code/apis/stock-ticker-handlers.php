<?php
//framework taken from module1/code/6_RestAPI/api/handlers.php and PDO code modified from module1/code/2_Connect_PHP_with_MySQL/crud_pdo.php


require_once 'models/stock-ticker.php';

$servername = "localhost";
$username   = "root";
$password   = "freezerburn15"; 
$dbname     = "public_apis";

function get_all_tickers()//lecture 6, part 2, page 31
{
    $tickers = load_tickers();
    echo json_encode([
        'success' => true,
        'data' => $tickers,
        'count' => count($tickers)
    ]);
}


function get_ticker($id)//lecture 6, part 2, page 32
{
    echo json_encode(['error' => 'Stock Symbol required']);
    $tickers = load_tickers();

    foreach ($tickers as $ticker) {
        if ($ticker['ticker_symbol'] == $id) {
            echo json_encode([
                'success' => true,
                'data' => $ticker
            ]);
            return;
        }
    }

    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'ticker not found'
    ]);
}


function create_ticker()//lecture 6, part 2, page 33
{

    $input = getRequestDataTick();
    if (!$input) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid JSON data'
        ]);
    ;
    }


    
    $new_ticker = new Stock_ticker();
    $new_ticker->setSymbol($input['symbol'] ?? 'ZZZ');
    $new_ticker->setFull_name($input['full_name'] ?? 'An IoT ticker');
    $new_ticker->setMarket_value($input['market_value'] ?? 2);
    $new_ticker->setIs_up($input['is_up'] ?? 0);
    

    $tickersarr = $new_ticker->toArray();
    // Save to file
    save_tickers($tickersarr);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'ticker created successfully',
        'data' => $tickersarr
    ]);
}


function ticker_update($id)//lecture 6, part 2, page 36
{

    $input = getRequestDataTick();

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
        $new_market_value = $input['market_value'] ?? 0;
        $old_market_value = 0;
        $is_up = 0;
        $tickers = load_tickers();

        foreach ($tickers as $ticker) {
            if ($ticker['ticker_symbol'] == $id) {
                $old_market_value = $ticker['market_value'];
            }
        }
        if ($new_market_value-$old_market_value > 0){
            $is_up = 1;
        }
        $dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,                 
        ];
        $pdo = new PDO($dsn, $username, $password, $options); //lecture 2, part 4, page 11
        $sql = "UPDATE stock_tickers
                    SET market_value = :market_value, is_up = :is_up
                    WHERE ticker_symbol = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':market_value' => $new_market_value,
            ':is_up' => $is_up,
            ':id' => $id
        ]);

        if ($stmt->rowCount() > 0) {

        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'ticker not found'
            ]);
            return;
        }


        echo json_encode([
            'success' => true,
            'message' => 'ticker updated successfully',
        ]);  

        } catch (Exception $e) {
        echo "Error (UPDATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }





}






function delete_ticker($id)//lecture 6, part 2, page 36
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
    $sql = "DELETE FROM stock_tickers WHERE ticker_symbol = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'ticker deleted successfully',
            ]);  
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'ticker not found'
            ]);
            return;
        }

        } catch (PDOException $e) {
        echo "Error (UPDATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }}



function load_tickers()//lecture 6, part 2, page 27
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
        $sql    = "SELECT ticker_symbol, full_name, market_value, is_up FROM stock_tickers"; //lecture 2, part 4, page 8
        $rows   = $pdo->query($sql)->fetchAll();
        return $rows ?: [];
    }
    catch(PDOException $e){
        exit("Connection failed: " . htmlspecialchars($e->getMessage()));

    }

}


function save_tickers($tickersarr)//lecture 6, part 2, page 28
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

    $sql  = "INSERT INTO stock_tickers (ticker_symbol, full_name, market_value, is_up) VALUES (:ticker_symbol, :full_name, :market_value, :is_up)"; //lecture 2, part 4, page 4
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ticker_symbol' => $tickersarr['symbol'],
        ':full_name' => $tickersarr['full_name'],
        ':market_value' => $tickersarr['market_value'],
        ':is_up' => $tickersarr['is_up']
    ]);



    } catch (PDOException $e) {
        echo "Error (CREATE): " . htmlspecialchars($e->getMessage()) . "<br><br>";
    }
}


function getRequestDataTick()//lecture 6, part 2, page 30
{
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}


