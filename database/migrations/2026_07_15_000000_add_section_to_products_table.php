<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSectionToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('section')->default('common')->after('condition');
        });

        // Set existing records to common if they don't already have a section column.
        if (Schema::hasColumn('products', 'section')) {
            DB::table('products')->whereNull('section')->update(['section' => 'common']);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('section');
        });
    }
}
