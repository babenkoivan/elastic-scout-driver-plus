<?php
declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateBooksIndex implements MigrationInterface
{
    public function up(): void
    {
        Index::create('books', function (Mapping $mapping, Settings $settings) {
            $mapping->integer('author_id');
            $mapping->text('title');
            $mapping->text('description');
            $mapping->integer('price');
            $mapping->date('published', ['format' => 'yyyy-MM-dd']);
        });
    }

    public function down(): void
    {
        Index::dropIfExists('books');
    }
}
