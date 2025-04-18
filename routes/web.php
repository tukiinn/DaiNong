<?php
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OnlPaymentController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\NAController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\AdminVoucherController;
use App\Http\Controllers\Admin\AdminChatController;
use App\Http\Controllers\Admin\AdminSearchController;
use App\Http\Controllers\Admin\ProductCommentController;
use App\Http\Controllers\Admin\UserController;


Route::middleware(['role:admin|manager|web staff|accountant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard chung (có thể hiển thị khác nhau dựa trên role trong view)
        Route::get('/', [StatisticsController::class, 'dashboard'])->name('dashboard');

        // Quản lý bình luận, sản phẩm, người dùng, danh mục,...
        Route::resource('comments', ProductCommentController::class);
        Route::resource('products', AdminProductController::class);
        Route::resource('users', UserController::class);
        Route::resource('categories', AdminCategoryController::class);

        // Quản lý đơn hàng
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/confirm', [AdminOrderController::class, 'confirm'])->name('orders.confirm');
        Route::post('orders/{order}/ship', [AdminOrderController::class, 'ship'])->name('orders.ship');
        Route::post('orders/{order}/complete', [AdminOrderController::class, 'complete'])->name('orders.complete');
        Route::post('orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');

        Route::post('orders/bulk-confirm', [AdminOrderController::class, 'bulkConfirm'])->name('orders.bulkConfirm');
        Route::post('orders/bulk-ship', [AdminOrderController::class, 'bulkShip'])->name('orders.bulkShip');
        Route::post('orders/bulk-complete', [AdminOrderController::class, 'bulkComplete'])->name('orders.bulkComplete');
        Route::post('orders/bulk-cancel', [AdminOrderController::class, 'bulkCancel'])->name('orders.bulkCancel');

        // Quản lý chat
        Route::get('chat', [AdminChatController::class, 'index'])->name('chat.index');
        Route::post('send-reply', [AdminChatController::class, 'replyMessage'])->name('chat.reply');
        Route::get('chat/messages', [AdminChatController::class, 'getChatMessages'])->name('chat.messages');

        // Quản lý nhà cung cấp và đơn vị
        Route::resource('suppliers', App\Http\Controllers\Admin\AdminSupplierController::class);
        Route::resource('units', App\Http\Controllers\Admin\AdminUnitController::class);

        // Quản lý tin tức
        Route::resource('news', NewsController::class);
        Route::get('news/{slug}', [NewsController::class, 'show'])->name('news.show');

        // Quản lý voucher
        Route::resource('vouchers', AdminVoucherController::class)->except(['show']);

        Route::get('vouchers/clean', [AdminVoucherController::class, 'deleteExpiredOrUsedVouchers'])->name('vouchers.clean');
        Route::post('vouchers/bulk-action', [AdminVoucherController::class, 'bulkAction'])->name('vouchers.bulkAction');

        Route::get('product-histories', [AdminProductController::class, 'indexhs'])->name('product_histories.index');
        Route::get('histories/{id}', [AdminProductController::class, 'showhs'])->name('histories.show');



        // Tìm kiếm chung
        Route::get('search', [AdminSearchController::class, 'search'])->name('search');
    });

Route::get('/admin/revenue-data', [StatisticsController::class, 'getRevenueData']);
Route::get('/admin/category-revenue-data', [StatisticsController::class, 'getCategoryRevenueForChart']);
Route::get('/admin/payment-revenue-data', [StatisticsController::class, 'getPaymentMethodRevenueData']);
Route::get('/admin/dashboard/export-excel', [StatisticsController::class, 'exportExcel'])->name('admin.dashboard.exportExcel');
Route::get('/admin/export-revenue', [StatisticsController::class, 'exportRevenue'])->name('admin.export-revenue');


Route::post('/orders/{id}/cancel', [OrderController::class, 'cancelOrder'])->name('orders.cancel');


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('auth');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update')->middleware('auth');
Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.changePassword')->middleware('auth');
Route::post('/profile/upload-avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.uploadAvatar');
Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.deleteAvatar');
Route::post('/shipping-addresses', [ProfileController::class, 'store'])->name('shipping_addresses.store');
// Các route quản lý địa chỉ giao hàng
Route::get('/shipping-addresses/{id}/edit', [ProfileController::class, 'editAd'])->name('shipping-addresses.edit');
Route::put('/shipping-addresses/{id}', [ProfileController::class, 'updateAd'])->name('shipping-addresses.update');
Route::delete('/shipping-addresses/{id}', [ProfileController::class, 'destroyAd'])->name('shipping-addresses.destroy');

Route::post('/apply-voucher', [VoucherController::class, 'applyVoucher'])->name('voucher.apply');

Route::post('upload', [NAController::class, 'upload'])->name('upload');

Route::get('/lucky-wheel', [App\Http\Controllers\LuckyWheelController::class, 'index'])->name('luckywheel.index');

Route::post('/voucher', [VoucherController::class, 'store'])->name('voucher.store');
Route::post('/spin', [App\Http\Controllers\LuckyWheelController::class, 'storeSpin'])->name('spin.store');

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login'); 
Route::post('login', [AuthController::class, 'login']);  
Route::post('logout', [AuthController::class, 'logout'])->name('logout'); 

  // Password reset routes
  Route::get('password/reset', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
  Route::post('password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
  Route::get('password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
  Route::post('password/reset', [AuthController::class, 'reset'])->name('password.update');
  
  
Route::resource('products', ProductController::class);
Route::get('/recently-viewed', [ProductController::class, 'recentlyViewed'])->name('recently_viewed');


Route::resource('categories', CategoryController::class);


// Routes cho giỏ hàng
Route::get('/cart', [CartController::class, 'index'])->name('cart.index'); 
Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');


    Route::post('/cart/update/{id}', [CartController::class, 'updateCart'])->name('cart.update');
    Route::post('/update-quantity', [CartController::class, 'updateQuantity'])->name('updateQuantity');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/create', [OrderController::class, 'createOrder'])->name('orders.create');
    Route::post('/orders/{id}/payCOD', [OrderController::class, 'payCOD'])->name('order.payCOD');
 // Route cho VNPay thanh toán và trả về
Route::get('/payment', [OrderController::class, 'paymentPage'])->name('payment.index');
Route::get('/vnpay_return', [OnlPaymentController::class, 'vnpayReturn'])->name('vnpay.return');

Route::post('/vnpay', [OnlPaymentController::class, 'vnpayment'])->name('vnpay.vn');
Route::post('/momo', [OnlPaymentController::class, 'momopayment'])->name('momo.vn');
Route::post('/retry-vnpay', [OnlPaymentController::class, 'retryvnpayment'])->name('retryvnpay.vn');
Route::post('/retrymomo', [OnlPaymentController::class, 'retrymomo'])->name('retrymomo.vn');

Route::post('/paypal/create-order', [PaypalController::class, 'createPaypalOrder'])->name('paypal.createOrder');
Route::get('/paypal/success', [PaypalController::class, 'paypalSuccess'])->name('paypal.success');
Route::get('/paypal/cancel', [PaypalController::class, 'paypalCancel'])->name('paypal.cancel');
Route::post('/paypal/retry-order', [PaypalController::class, 'retryPaypalOrder'])->name('paypal.retryOrder');

Route::get('/cart/ajax', [CartController::class, 'ajaxCart'])->name('cart.ajax');

Route::get('/cart/count', [CartController::class, 'getCartItemCount'])->name('cart.count');
Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::get('/cart/vnpay/order-received', [OnlPaymentController::class, 'checkoutthank'])->name('thankyouvnpay');
Route::get('/cart/momo/order-received', [OnlPaymentController::class, 'thankmomo'])->name('thankyoumomo');


Route::controller(SocialController::class)->group(function(){
    Route::get('auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('auth/google/callback', 'handleGoogleCallback');
});
Route::get('login/facebook', [SocialController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('login/facebook/callback', [SocialController::class, 'handleFacebookCallback']);

Route::get('/news', [NAController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NAController::class, 'show'])->name('news.show');
Route::get('/address', [NAController::class, 'addressIndex'])->name('address.index');

Route::post('/contact-submit', [NAController::class, 'submit'])->name('contact.submit');

Route::get('/search', [NAController::class, 'search'])->name('products.search');

Route::get('/live-search', [NAController::class, 'liveSearch'])->name('live-search');

Route::post('/reviews/{category}', [ReviewController::class, 'store'])->name('reviews.store');
Route::post('/products/{productId}/comments', [ReviewController::class, 'storecmt'])->name('product.comments.store');
Route::post('product-comments/{comment}/reply', [ReviewController::class, 'reply'])->name('product.comments.reply');
Route::delete('product-comments/{id}', [ReviewController::class, 'destroy'])->name('product.comments.destroy');

Route::middleware('auth')->group(function () {
    // Giao diện chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    // Xử lý gửi tin nhắn
    Route::post('/send-message', [ChatController::class, 'sendMessage'])->name('chat.send');
});
Route::middleware('auth')->get('/chat/room', [ChatController::class, 'getRoomId']);
Route::middleware('auth')->get('/chat/messages', [ChatController::class, 'getMessages']);
Route::get('/chat/user/{id}', [ChatController::class, 'getUserName']);

Route::middleware(['cors'])->group(function () {
Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCartAjax'])->name('cart.remove.ajax');
});
