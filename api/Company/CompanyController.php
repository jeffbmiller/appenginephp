<?php

namespace Api;

use Psr\Container\ContainerInterface;

class CompanyController
{
    protected $container;
    protected $repo;
    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
        $this->container = $container;

        $this->repo = new CompanyRepository();
    }

    public function get($request, $response, $args)
    {
        $companies = $this->repo->getAllCompanies();
        return $response->withJson($companies);
    }

}
