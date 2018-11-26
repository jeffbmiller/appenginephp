<?php
require './vendor/autoload.php';

$app = new \Slim\App;

$c = $app->getContainer();

$app->add(new \Api\BasicAuthMiddleware());

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->group('/test', function () use($app){
    $_class = '\Api\Test\TestController';

    //  GET 'url/test/log'
    $app->get('/log', $_class . ':getLog');

    //  POST 'url/test/testpost'
    $app->post('/testpost', $_class . ':testPost');
});


$app->group('/company', function () use($app){
    $_class = '\Api\CompanyController';

    //  GET 'url/company/getAll'
    $app->get('/getAll', $_class . ':get');

    $app->post('saveCompany', $_class . ':post');
});

$app->run();