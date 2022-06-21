<?php declare(strict_types=1);

use Elastic\Adapter\Indices\Mapping;
use Elastic\Migrations\Facades\Index;
use Elastic\Migrations\MigrationInterface;

final class CreateStoresIndex implements MigrationInterface
{
    public function up(): void
    {
        Index::create('stores', static function (Mapping $mapping) {
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
