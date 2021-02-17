<?php

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

	require 'vendor/autoload.php';
	
	define("RE_CAPTCHA_SECRET_KEY" , "6LezljgUAAAAAJtzb1Wm8molXKtfT6TYovRx8fbu");

    // Autoload all clases
	
	spl_autoload_register(function ($className) {
		
	    $className = ltrim($className, '\\');
	    $fileName  = '';
	    $namespace = '';
	    
	    if ($lastNsPos = strrpos($className, '\\')) {
	    	
	        $namespace = substr($className, 0, $lastNsPos);
	        $className = substr($className, $lastNsPos + 1);
	        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
	    }
	    
	    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
	
	    if (file_exists($fileName)) {
	    	require $fileName;	    	
	    }
	    
	});

    // Configuration
	
	$config['displayErrorDetails'] = true;
	$config['addContentLengthHeader'] = false;	
	
	// Database Configuration
	
	$config['db']['host']   = "localhost";
	$config['db']['user']   = "daniel41_mr"; 	// "root"  - "daniel41_mr"
	$config['db']['pass']   = "roffe123db";  	// ""      - "roffe123db"
	$config['db']['dbname'] = "daniel41_mr";	// "roffe" - "daniel41_mr"
	
	// App
	
    $app = new \Slim\App(["settings" => $config]);
    
    // Container
	
    $container = $app->getContainer();
    
    // Logger - Monolog
	
	$container['logger'] = function($c) {
		
		$logger    = new \Monolog\Logger('LOGGER');
		$handler   = new \Monolog\Handler\RotatingFileHandler("logs/mr.log", 0, \Monolog\Logger::INFO);
		$formatter = new \Monolog\Formatter\LineFormatter(null, null, false, true);
		
		$handler->setFormatter($formatter);
		$logger->pushHandler($handler);
		
		return $logger;
    };
    
    // PDO
	
	$container['db'] = function ($c) {
		
		$db = $c['settings']['db'];
		
		$pdo = new \PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass'], [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
		
		$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
		
		$pdo->exec("SET time_zone='-3:00';");
		
		return $pdo;
    };

    // CORS
    
    $app->add(function ($req, $res, $next) {
        $response = $next($req, $res);
        return $response->withHeader('Access-Control-Allow-Origin', '*');
    });

    // Routes

    $routeFiles = (array) glob(__DIR__ . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . '*.php');
    
    foreach($routeFiles as $routeFile) {
      require_once $routeFile;
    }

    // Run
	
	$app->run();