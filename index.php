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

// API CRUD

$app->get('/admin/users', function() {
	
	User::verifyLogin();

	$users = User::listAll();

	$page = new PageAdmin();
	$page->setTpl("users", array(
		"users" => $users
	));

});

$app->get('/admin/users/:iduser/delete', function($iduser) {

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

	$user->delete();

	header("Location: /admin/users");
	exit;
});

// Loading the form users-create
$app->get('/admin/users/create', function() {
	
	User::verifyLogin();

	$page = new PageAdmin();
	$page->setTpl("users-create");

});

$app->get('/admin/users/:iduser', function($iduser) {
	
	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();
	$page->setTpl("users-update", array(
		"user" => $user->getValues()
	));

});

// Insert user from tb_users
$app->post('/admin/users/create', function() {

	User::verifyLogin();

	$user = new User();
	$user->setData($_POST);

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;
	$user->save();

	header("Location: /admin/users");
	exit;
});

$app->post('/admin/users/:iduser', function($iduser) {

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;
	
	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;
});

$app->run();

 ?>