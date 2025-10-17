<?php

namespace App\Services;

use Couchbase\Cluster;
use Couchbase\ClusterOptions;
use Couchbase\Collection;
use Couchbase\DefaultSerializer;
use Couchbase\QueryOptions;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Facades\Log;

class CouchbaseService
{
    protected Cluster $cluster;

    protected Collection $collection;

    public function __construct(Config $config)
    {
        $options = new ClusterOptions();
        $options->credentials(
            $config->get('database.connections.couchbase.username'),
            $config->get('database.connections.couchbase.password')
        );
        $options->setSerializer(new DefaultSerializer());

        $host = $config->get('database.connections.couchbase.host');

        $this->cluster = new Cluster(sprintf('couchbase://%s', $host), $options);

        $bucket = $this->cluster->bucket($config->get('database.connections.couchbase.bucket'));
        $scope = $config->get('database.connections.couchbase.scope');
        $collection = $config->get('database.connections.couchbase.collection');

        $this->collection = $bucket->scope($scope)->collection($collection);
    }

    public function upsert(string $key, array $document): void
    {
        $this->collection->upsert($key, $document);
    }

    public function get(string $key): ?array
    {
        try {
            $result = $this->collection->get($key);

            return $result->content();
        } catch (\Exception $e) {
            Log::warning('Couchbase get failed', ['key' => $key, 'message' => $e->getMessage()]);

            return null;
        }
    }

    public function query(string $statement, array $options = []): array
    {
        $queryOptions = new QueryOptions();

        foreach ($options as $key => $value) {
            $queryOptions->{$key}($value);
        }

        $result = $this->cluster->query($statement, $queryOptions);

        return $result->rows();
    }
}

