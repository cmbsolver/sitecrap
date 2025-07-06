<?php
// runewords.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';
require 'config.php';
require 'wordpattern.php';
require 'runedonkey.php';

use Slim\Factory\AppFactory;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$config = require 'config.php';

$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();

$container->set('db', function() use ($config) {
    try {
        // Use the default connection
        $dbConfig = $config['connections']['default'];
        $pdo = new PDO("mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}", 
                       $dbConfig['username'], 
                       $dbConfig['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
});

// To add a second database connection
$container->set('norvig', function() use ($config) {
    try {
        // Use the second connection
        $dbConfig = $config['connections']['norvig'];
        $pdo = new PDO("mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}", 
                       $dbConfig['username'], 
                       $dbConfig['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
});

$container->set('10k', function() use ($config) {
    try {
        // Use the second connection
        $dbConfig = $config['connections']['10k'];
        $pdo = new PDO("mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}",
            $dbConfig['username'],
            $dbConfig['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
});

$container->set('20k', function() use ($config) {
    try {
        // Use the second connection
        $dbConfig = $config['connections']['20k'];
        $pdo = new PDO("mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}",
            $dbConfig['username'],
            $dbConfig['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
});

function queryDatabase($field, $value, $db) {
    $stmt = $db->prepare("SELECT * FROM dictionary_words WHERE $field = :value");
    $stmt->execute(['value' => $value]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$app->post('/runewords.php/generate_excel', function(Request $request, Response $response) {
    $params = (array)$request->getParsedBody();
    $text = $params['text'] ?? '';
    $textType = $params['text_type'] ?? '';
    $action = $params['action'] ?? '';
    $dataset = $params['dataset'] ?? '';

    // Get the database connection
    $db = $this->get($dataset);

    // Create an instance of RuneDonkey
    $runeDonkey = new RuneDonkey();

    // Call the GenerateExcelFromValues method
    $base64String = $runeDonkey->GenerateExcelFromValues($text, $textType, $action, $db);

    // Return the base64 string as a JSON response
    $response->getBody()->write(json_encode(['base64' => $base64String], JSON_UNESCAPED_UNICODE));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/runewords.php/gem_sum/{database}/{value}', function(Request $request, Response $response, $args) {
    $db = $this->get($args['database']);
    $data = queryDatabase('gem_sum', $args['value'], $db);
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/runewords.php/gem_product/{database}/{value}', function(Request $request, Response $response, $args) {
    $db = $this->get($args['database']);
    $data = queryDatabase('gem_product', $args['value'], $db);
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/runewords.php/dict_word_length/{database}/{value}', function(Request $request, Response $response, $args) {
    $db = $this->get($args['database']);
    $data = queryDatabase('dict_word_length', $args['value'], $db);
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/runewords.php/dict_runeglish_length/{database}/{value}', function(Request $request, Response $response, $args) {
    $db = $this->get($args['database']);
    $data = queryDatabase('dict_runeglish_length', $args['value'], $db);
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/runewords.php/dict_rune_length/{database}/{value}', function(Request $request, Response $response, $args) {
    $db = $this->get($args['database']);
    $data = queryDatabase('dict_rune_length', $args['value'], $db);
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/runewords.php/rune_pattern/{database}/{value}', function(Request $request, Response $response, $args) {
    $db = $this->get($args['database']);
    $data = queryDatabase('rune_pattern', $args['value'], $db);
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/runewords.php/rune_pattern_no_doublet/{database}/{value}', function(Request $request, Response $response, $args) {
    $db = $this->get($args['database']);
    $data = queryDatabase('rune_pattern_no_doublet', $args['value'], $db);
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/runewords.php/wordpattern/{value}', function(Request $request, Response $response, $args) {
    $value = $args['value'] ?? '';
    $wordPattern = new WordPattern();
    $wordPattern->generatePattern($value);
    $response->getBody()->write(json_encode($wordPattern, JSON_UNESCAPED_UNICODE));
    return $response->withHeader('Content-Type', 'application/json');
});

// Add a custom not found handler
$app->setBasePath('/cmbsolver-api');
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$errorMiddleware->setErrorHandler(Slim\Exception\HttpNotFoundException::class, function (
    Request $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app) {
    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write('Route not found');
    return $response->withStatus(404);
});

$app->run();