<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;
use App\Models\Review;
use App\Models\ProductComment;
use App\Models\Order;
use App\Models\ShippingAddress;

class ProfileController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Hãy đăng nhập để xem thông tin.');
        }
        $user = Auth::user();
    
        // Lấy các đánh giá của user
        $reviews = Review::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->with('product')
            ->get();
    
        // Lấy các bình luận sản phẩm của user
        $productComments = ProductComment::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->with('product')
            ->get();
    
        // Lấy các đơn hàng của user (nếu có)
        $orders = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    
        return view('profile.index', compact('user', 'reviews', 'productComments', 'orders'));
    }
    

    /**
     * Hiển thị form chỉnh sửa thông tin cá nhân.
     */
    public function edit()
    {
        $user = Auth::user();
        // Lấy tất cả địa chỉ của người dùng hiện tại
        $shippingAddresses = ShippingAddress::where('user_id', $user->id)->get();
        
        return view('profile.edit', compact('user', 'shippingAddresses'));
    }

    /**
     * Cập nhật thông tin cá nhân và avatar (nếu có).
     */
    public function update(Request $request)
    {   
        $manager = new ImageManager(new Driver());
        $user = Auth::user();
        
        $request->validate([
            'name'          => 'required|string|min:2|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'         => ['nullable', 'string', 'regex:/^(0|\+84)[0-9]{9,10}$/', 'max:10'],
            'address'       => 'nullable|string|max:255',
            'gender'        => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date|before:today',
            'croppedImage'  => 'nullable|string',
        ], [
            'name.required'    => 'Họ và tên là bắt buộc.',
            'name.string'      => 'Họ và tên phải là chuỗi ký tự.',
            'name.min'         => 'Họ và tên phải có ít nhất 2 ký tự.',
            'name.max'         => 'Họ và tên không được vượt quá 255 ký tự.',
        
            'email.required'   => 'Email là bắt buộc.',
            'email.email'      => 'Email không hợp lệ.',
            'email.max'        => 'Email không được vượt quá 255 ký tự.',
            'email.unique'     => 'Email này đã tồn tại.',
        
            'phone.string'     => 'Số điện thoại phải là chuỗi ký tự.',
            'phone.regex'      => 'Số điện thoại không hợp lệ. Vui lòng nhập số điện thoại hợp lệ (bắt đầu bằng 0 hoặc +84 và gồm 9-10 chữ số).',
            'phone.max'        => 'Số điện thoại không được vượt quá 20 ký tự.',
        
            'address.string'   => 'Địa chỉ phải là chuỗi ký tự.',
            'address.max'      => 'Địa chỉ không được vượt quá 255 ký tự.',
        
            'gender.in'        => 'Giới tính phải là "male", "female" hoặc "other".',
        
            'date_of_birth.date'   => 'Ngày sinh không hợp lệ.',
            'date_of_birth.before' => 'Ngày sinh phải là ngày trong quá khứ.',
        ]);
        

        $data = [
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'address'       => $request->address,
            'gender'        => $request->gender,
            'date_of_birth' => $request->date_of_birth,
        ];

        if ($request->filled('croppedImage')) {
            $croppedImage = $request->input('croppedImage');
            $filename = time() . '.png';

            // Giải mã dữ liệu base64 của ảnh đã cắt
            $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $croppedImage));

            // Tạo instance ảnh từ dữ liệu đã decode
            $image = $manager->read($decodedImage);

            // Lưu ảnh chính
            $destinationPath = public_path('images/avatars/');
            $image->save($destinationPath . $filename);

            // Tạo và lưu ảnh thumbnail (sử dụng instance mới để không ảnh hưởng đến ảnh gốc)
            $thumbnail = $manager->read($decodedImage)->resize(100, 100);
            $thumbnailPath = public_path('images/avatars/thumbnail/');
            $thumbnail->save($thumbnailPath . $filename);

            // Xóa avatar cũ nếu có
            if ($user->avatar) {
                File::delete(public_path($user->avatar));
                File::delete(public_path('images/avatars/thumbnail/' . basename($user->avatar)));
            }

            // Lưu đường dẫn đầy đủ của avatar vào database
            $data['avatar'] = 'images/avatars/' . $filename;
        }

        $user->update($data);

        return redirect()->route('profile.index')->with('success', 'Thông tin của bạn đã được cập nhật.');
    }

    /**
     * Thay đổi mật khẩu.
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.index')->with('success', 'Mật khẩu của bạn đã được thay đổi.');
    }

    /**
     * Upload avatar mới từ file upload.
     */
    public function uploadAvatar(Request $request)
    {
        $manager = new ImageManager(new Driver());
        $user = Auth::user();
        $request->validate([
            'avatar'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'avatar.image'     => 'Ảnh đại diện phải là một hình ảnh hợp lệ.',
            'avatar.mimes'     => 'Ảnh đại diện phải có định dạng: jpeg, png, jpg, gif, svg.',
            'avatar.max'       => 'Kích thước ảnh đại diện không được vượt quá 2MB.',
        ]);

     
        if ($request->filled('croppedImage')) {
            $croppedImage = $request->input('croppedImage');
            $filename = time() . '.png';

            // Giải mã dữ liệu base64 của ảnh đã cắt
            $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $croppedImage));

            // Tạo instance ảnh từ dữ liệu đã decode
            $image = $manager->read($decodedImage);

            // Lưu ảnh chính
            $destinationPath = public_path('images/avatars/');
            $image->save($destinationPath . $filename);

            // Tạo và lưu ảnh thumbnail (sử dụng instance mới để không ảnh hưởng đến ảnh gốc)
            $thumbnail = $manager->read($decodedImage)->resize(100, 100);
            $thumbnailPath = public_path('images/avatars/thumbnail/');
            $thumbnail->save($thumbnailPath . $filename);

            // Xóa avatar cũ nếu có
            if ($user->avatar) {
                File::delete(public_path($user->avatar));
                File::delete(public_path('images/avatars/thumbnail/' . basename($user->avatar)));
            }

            // Lưu đường dẫn đầy đủ của avatar vào database
            $data['avatar'] = 'images/avatars/' . $filename;
        }

        $user->update($data);
        return redirect()->route('profile.edit')->with('success', 'Avatar đã được cập nhật thành công');
    }

    public function deleteAvatar(Request $request)
{
    $user = Auth::user();

    if ($user->avatar) {
        // Xóa file avatar chính
        $avatarPath = public_path($user->avatar);
        if (file_exists($avatarPath)) {
            File::delete($avatarPath);
        }
        
        // Xóa file thumbnail (nếu tồn tại)
        $thumbnailPath = public_path('images/avatars/thumbnail/' . basename($user->avatar));
        if (file_exists($thumbnailPath)) {
            File::delete($thumbnailPath);
        }
        
        // Cập nhật avatar của user về null
        $user->avatar = null;
        $user->save();

        return redirect()->back()->with('success', 'Avatar đã được xóa thành công.');
    }

    return redirect()->back()->with('error', 'Không có avatar nào để xóa.');
}

    /**
     * Lưu địa chỉ giao hàng mới vào cơ sở dữ liệu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $data = $request->validate([
            'province'         => 'required|string',
            'district'         => 'required|string',
            'ward'             => 'required|string',
            'detailed_address' => 'required|string',
            'address_name' => 'required|string',
            'phone_address' => 'required|string',
            'name_address' => 'required|string',
            'full_address'          => 'required|string',
        ]);

        // Gán user_id nếu người dùng đã đăng nhập
        $data['user_id'] = Auth::id();

        // Tạo địa chỉ giao hàng mới
        ShippingAddress::create($data);

        // Redirect về trang trước kèm thông báo thành công
        return redirect()->back()->with('success', 'Địa chỉ giao hàng đã được lưu thành công.');
    }
    public function updateAd(Request $request, $id)
{
    // Tìm địa chỉ giao hàng theo ID
    $shippingAddress = ShippingAddress::findOrFail($id);

    // Kiểm tra quyền sở hữu (nếu cần)
    if ($shippingAddress->user_id !== Auth::id()) {
        return redirect()->back()->with('error', 'Bạn không có quyền cập nhật địa chỉ này.');
    }

    // Validate dữ liệu đầu vào
    $data = $request->validate([
        'province'         => 'required|string',
        'district'         => 'required|string',
        'ward'             => 'required|string',
        'detailed_address' => 'required|string',
        'address_name'     => 'required|string',
        'phone_address'    => 'required|string',
        'name_address'     => 'required|string',
        'full_address'     => 'required|string',
    ]);

    // Cập nhật địa chỉ giao hàng
    $shippingAddress->update($data);

    // Redirect về trang trước kèm thông báo thành công
    return redirect()->back()->with('success', 'Địa chỉ giao hàng đã được cập nhật thành công.');
}
public function destroyAd($id)
{
    $address = ShippingAddress::findOrFail($id);

    // Kiểm tra quyền sở hữu (chỉ cho phép xóa địa chỉ của chính mình)
    if ($address->user_id !== Auth::id()) {
        return response()->json(['error' => 'Bạn không có quyền xóa địa chỉ này!'], 403);
    }

    $address->delete();

    return redirect()->back()->with('success', 'Xóa địa chỉ thành công.');
}


}
