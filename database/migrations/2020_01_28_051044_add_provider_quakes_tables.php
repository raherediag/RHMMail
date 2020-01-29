<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProviderQuakesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quakes', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `quakes` CHANGE COLUMN `from` `provider` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci' AFTER `mag`;");
            $table->string('provider_id')->nullable()->after('provider');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quakes', function (Blueprint $table) {
            $table->dropColumn('provider_id');
            \DB::statement("ALTER TABLE `quakes` CHANGE COLUMN `provider` `from` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci' AFTER `mag`;");
        });
    }
}
