<?php declare(strict_types=1);

use Elastic\Adapter\Indices\Mapping;
use Elastic\Adapter\Indices\Settings;
use Elastic\Migrations\Facades\Index;
use Elastic\Migrations\MigrationInterface;

final class CreateBooksIndex implements MigrationInterface
{
    public function up(): void
    {
        Index::create('books', static function (Mapping $mapping, Settings $settings) {
            $mapping->integer('id');
            $mapping->integer('author_id');
            $mapping->text('title');
            $mapping->completion('suggest');
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
