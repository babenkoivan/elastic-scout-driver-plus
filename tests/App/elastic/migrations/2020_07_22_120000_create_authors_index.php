<?php declare(strict_types=1);

use Elastic\Adapter\Indices\Mapping;
use Elastic\Elasticsearch\Client;
use Elastic\Migrations\Facades\Index;
use Elastic\Migrations\MigrationInterface;

final class CreateAuthorsIndex implements MigrationInterface
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function up(): void
    {
        Index::create('book-authors', static function (Mapping $mapping) {
            $mapping->integer('id');
            $mapping->text('name');
            $mapping->text('last_name');
            $mapping->keyword('phone_number');
            $mapping->text('email');
        });

        // todo update when elastic-migrations support aliases
        $this->client->indices()->putAlias([
            'name' => 'authors',
            'index' => 'book-authors',
        ]);
    }

    public function down(): void
    {
        Index::dropIfExists('book-authors');
    }
}
