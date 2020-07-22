<?php declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateAuthorsIndex implements MigrationInterface
{
    public function up(): void
    {
        Index::create('authors', static function (Mapping $mapping, Settings $settings) {
            $mapping->text('name');
            $mapping->text('last_name');
            $mapping->text('phone_number');
            $mapping->text('email');
        });
    }

    public function down(): void
    {
        Index::dropIfExists('authors');
    }
}
