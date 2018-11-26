<?php

namespace Api;

class CompanyRepository extends BaseRepository
{
    function getAllCompanies(){
        return $this->getAll('Company');
    }
}