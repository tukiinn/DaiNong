<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierAndUnitToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Thêm trường supplier_id sau cột category_id
            $table->unsignedBigInteger('supplier_id')->nullable()->after('category_id');
            $table->foreign('supplier_id')
                  ->references('id')
                  ->on('suppliers')
                  ->onDelete('set null');

            // Thêm trường unit_id sau supplier_id
            $table->unsignedBigInteger('unit_id')->nullable()->after('supplier_id');
            $table->foreign('unit_id')
                  ->references('id')
                  ->on('units')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Hủy các khóa ngoại trước khi xóa cột
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['unit_id']);

            $table->dropColumn(['supplier_id', 'unit_id']);
        });
    }
}
