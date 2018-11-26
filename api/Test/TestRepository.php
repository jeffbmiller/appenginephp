<?php

namespace Api\Test;
use Api\BaseRepository;

class TestRepository extends BaseRepository
{
    function getAllLogs(){
        return $this->getAll('TestPost');
    }

    function saveTestPost($json){
        return $this->save('TestPost',$json);
    }
}