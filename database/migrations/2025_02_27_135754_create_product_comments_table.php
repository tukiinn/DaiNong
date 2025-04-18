<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('product_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');   // liên kết đến sản phẩm
            $table->unsignedBigInteger('user_id');      // liên kết đến người dùng
            $table->tinyInteger('rating')->unsigned();  // đánh giá sao (ví dụ: từ 1 đến 5)
            $table->text('comment')->nullable();        // nội dung bình luận, có thể để null nếu chỉ đánh giá sao
            $table->timestamps();

          
          $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
          $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_comments');
    }
}
