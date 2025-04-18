<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_comments', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('user_id');
           
           $table->foreign('parent_id')->references('id')->on('product_comments')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('product_comments', function (Blueprint $table) {
            $table->dropColumn('parent_id');
        });
    }
    
};
