<?php
// runewords.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';
require 'config.php';

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
        $pdo = new PDO("mysql:host={$config['host']};dbname={$config['dbname']}", $config['username'], $config['password']);
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

$app->get('/runewords.php/gem_sum/{value}', function(Request $request, Response $response, $args) {
    $db = $this->get('db');
    $data = queryDatabase('gem_sum', $args['value'], $db);
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/runewords.php/gem_product/{value}', function(Request $request, Response $response, $args) {
    $db = $this->get('db');
    $data = queryDatabase('gem_product', $args['value'], $db);
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/runewords.php/dict_word_length/{value}', function(Request $request, Response $response, $args) {
    $db = $this->get('db');
    $data = queryDatabase('dict_word_length', $args['value'], $db);
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/runewords.php/dict_runeglish_length/{value}', function(Request $request, Response $response, $args) {
    $db = $this->get('db');
    $data = queryDatabase('dict_runeglish_length', $args['value'], $db);
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/runewords.php/dict_rune_length/{value}', function(Request $request, Response $response, $args) {
    $db = $this->get('db');
    $data = queryDatabase('dict_rune_length', $args['value'], $db);
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/runewords.php/rune_pattern/{value}', function(Request $request, Response $response, $args) {
    $db = $this->get('db');
    $data = queryDatabase('rune_pattern', $args['value'], $db);
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/runewords.php/rune_pattern_no_doublet/{value}', function(Request $request, Response $response, $args) {
    $db = $this->get('db');
    $data = queryDatabase('rune_pattern_no_doublet', $args['value'], $db);
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
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