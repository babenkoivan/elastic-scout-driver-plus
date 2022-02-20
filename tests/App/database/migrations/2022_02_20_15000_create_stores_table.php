<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('stores', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->decimal('lat');
            $table->decimal('lon');
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
