<?php
/**
 * Created by PhpStorm.
 * User: jeffmiller
 * Date: 2018-11-26
 * Time: 8:51 AM
 */

namespace Api\Test;


class TestPostCommand
{
    protected $json;

    public function __construct($json) {
        $this->repo = new TestRepository();
        $this->json = $json;
    }

    function execute(){
        //Validation

        $this->repo->saveTestPost($this->json);

        $result = true; //TODO some result object

        $result;
    }
}