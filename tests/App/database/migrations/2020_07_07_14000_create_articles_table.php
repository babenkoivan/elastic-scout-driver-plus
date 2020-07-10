<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('author_id');
            $table->string('title');
            $table->text('description');
            $table->integer('price');
            $table->date('published');
            $table->softDeletes();

            $table->foreign('author_id')->references('id')->on('authors');
        });
    }

    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
