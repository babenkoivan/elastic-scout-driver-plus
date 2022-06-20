<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorsTable extends Migration
{
    public function up(): void
    {
        Schema::create('authors', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
}
