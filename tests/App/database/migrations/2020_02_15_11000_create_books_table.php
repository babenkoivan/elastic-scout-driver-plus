<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('books', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('author_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('price');
            $table->date('published');
            $table->json('tags')->nullable();
            $table->softDeletes();

            $table->foreign('author_id')->references('id')->on('authors');
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
