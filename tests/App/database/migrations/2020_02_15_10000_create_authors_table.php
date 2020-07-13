<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorsTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('authors', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('email');
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authors');
    }
}
