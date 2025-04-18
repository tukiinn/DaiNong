<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Category;  // Đảm bảo đã import model Category

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {

    
        // Lấy tất cả danh mục có subCategories và sắp xếp theo 'sort_order'
        $categories = Category::with('subCategories')->orderBy('sort_order', 'asc')->get();
        
        // Chia sẻ vào tất cả các view
        view()->share('categories', $categories);

        
    }
}
