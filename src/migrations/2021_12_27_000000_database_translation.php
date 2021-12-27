<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DatabaseTranslation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->increments('language_id');
            $table->string('language_code')->index();
            $table->string('name')->index();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
        Schema::create('language_sources', function (Blueprint $table) {
            $table->increments('language_source_id');
            $table->string('namespace')->default('*')->index();
            $table->string('group')->default('*')->index();
            $table->text('key');
            $table->timestamps();
        });
        Schema::create('language_translations', function (Blueprint $table) {
            $table->increments('language_translation_id');
            $table->unsignedInteger('language_id')->index();
            $table->unsignedInteger('language_source_id')->index();
            $table->text('translation');
            $table->timestamps();

            $table->foreign('language_id')->references('language_id')
                ->on('languages')->cascadeOnDelete();
            $table->foreign('language_source_id')->references('language_source_id')
                ->on('language_sources')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('language_translations');
        Schema::dropIfExists('language_sources');
        Schema::dropIfExists('languages');
    }
}
