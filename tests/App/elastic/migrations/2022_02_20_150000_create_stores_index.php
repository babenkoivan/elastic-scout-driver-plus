<?php declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateStoresIndex implements MigrationInterface
{
    public function up(): void
    {
        Index::create('stores', static function (Mapping $mapping, Settings $settings) {
            $mapping->integer('id');
            $mapping->text('name');
            $mapping->geoPoint('location');
        });
    }

    public function down(): void
    {
        Index::dropIfExists('stores');
    }
}
