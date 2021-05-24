<?php declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateBooksIndex implements MigrationInterface
{
    public function up(): void
    {
        Index::create('books', static function (Mapping $mapping, Settings $settings) {
            $mapping->integer('id');
            $mapping->integer('author_id');
            $mapping->text('title');
            $mapping->text('description');
            $mapping->integer('price');
            $mapping->date('published', ['format' => 'yyyy-MM-dd']);
            $mapping->keyword('tags');

            $mapping->nested('author', [
                'properties' => [
                    'name' => [
                        'type' => 'text',
                    ],
                    'phone_number' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);

            $settings->index([
                'number_of_shards' => 4,
            ]);
        });
    }

    public function down(): void
    {
        Index::dropIfExists('books');
    }
}
