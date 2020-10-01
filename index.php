<?php 
session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Models\User;

$app = new Slim();

$app->config('debug', true);

//chama a rota qdo get
$app->get('/', function() {
    
	$page = new Page();
	$page->setTpl("index");
});

$app->get('/admin', function() {

	User::verifyLogin();
    
	$page = new PageAdmin();
	$page->setTpl("index");
});

$app->get('/login', function() {
    
	$page = new PageAdmin([
		"header" => false,
		"footer" => false
	]);
	$page->setTpl("login");
});

$app->post('/admin/login', function() {
	
	User::login($_POST["login"], $_POST["password"]);
	header("Location: /admin");
	exit;
});

$app->get('/logout', function() {
	
	User::logout();
	header("Location: /login");
	exit;
});

$app->run();

 ?>