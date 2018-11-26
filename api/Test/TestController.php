<?php
namespace Api\Test;
use Psr\Container\ContainerInterface;
use Api\Test\TestRepository;
use Api\Test\TestPostCommand;

class TestController
{
    protected $container;
    protected $repo;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;

        $this->repo = new TestRepository();
    }

    public function getLog($request, $response, $args)
    {
        $logs = $this->repo->getAllLogs();
        return $response->withJson($logs);
    }

    public function testPost($request, $response, $args)
    {
        $json = $request->getParsedBody();

        $command = new TestPostCommand($json);

        $result = $command->execute();

        //TODO return $result object

    }
}