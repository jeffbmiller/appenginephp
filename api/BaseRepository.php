<?php

namespace Api;

# Imports the Google Cloud client library
use Google\Cloud\Datastore\DatastoreClient;

class BaseRepository
{
    private $datastore;

    public function __construct()
    {
        $_isDevEnv = (strpos(getenv('SERVER_SOFTWARE'), 'Development') === 0);
        if ($_isDevEnv)
            putenv('DATASTORE_EMULATOR_HOST=http://localhost:8081');


        # Your Google Cloud Platform project ID
        $projectId = 'tidy-muse-841';

        # Instantiates a client
        $this->datastore = new DatastoreClient([
            'projectId' => $projectId
        ]);
    }



    public function save($kind, $entity) {
        # The Cloud Datastore key for the new entity
        $key = $this->datastore->key($kind);
        # Prepares the new entity
        $task = $this->datastore->entity($key, $entity);
        # Saves the entity
        $this->datastore->upsert($task);

    }

    public function getAll($kind) {
        $query = $this->datastore->query()
            ->kind($kind);
        $results = $this->datastore->runQuery($query);

        $entities = array();
        foreach ($results as $index => $result){
            array_push($entities,$result->get());
        }

        return $entities;
    }
}