<?php declare(strict_types=1);

use Elastic\Adapter\Indices\Mapping;
use Elastic\Migrations\Facades\Index;
use Elastic\Migrations\MigrationInterface;

final class CreateAuthorsIndex implements MigrationInterface
{
    public function up(): void
    {
        Index::create('book-authors', static function (Mapping $mapping) {
            $mapping->integer('id');
            $mapping->text('name');
            $mapping->text('last_name');
            $mapping->keyword('phone_number');
            $mapping->text('email');
        });

        Index::putAlias('book-authors', 'authors');
    }

    public function down(): void
    {
        Index::dropIfExists('book-authors');
    }
}
