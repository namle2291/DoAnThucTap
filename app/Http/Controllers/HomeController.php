<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Helper\CartHelper;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Customer;
use App\Models\OrderDetail;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use RealRashid\SweetAlert\Facades\Alert;

class HomeController extends Controller
{
    function index()
    {
        $products = Product::where('status', 1)->orderByDesc('id')->take(12)->get();
        return view('welcome', compact('products'));
    }
    // Cart
    function cart()
    {
        return view('cart');
    }
    function checkout()
    {
        return view('checkout');
    }
    function store_checkout(CartHelper $cart, Request $request)
    {
        $data = $request->all();
        unset($data['_token']);

        $request->validate(
            [
                'email' => 'required|email',
                'phone' => 'required',
            ],
            [],
            [
                'email' => 'Email',
                'phone' => 'Điện thoại',
            ]
        );

        $data['total'] = $cart->total_price;
        $data['customer_id'] = Auth::user()->id;
        $data['status_id'] = 1;

        $order = Order::create($data);
        if ($order) {
            foreach ($cart->items as $item) {
                $order_detail = new OrderDetail();
                $order_detail->product_id = $item['id'];
                $order_detail->order_id = $order->id;
                $order_detail->price = $item['price'];
                $order_detail->quantity = $item['quantity'];
                // Update product stock
                $product = Product::findOrFail($order_detail->product_id);
                $product->update(['stock' => $product->stock - $item['quantity']]);
                $product->save();
                $order_detail->save();
            }
            $cart->remove();
        }

        Mail::send(
            'email.checkout',
            [
                'order' => $order,
            ],
            function ($mail) use ($request) {
                $mail->to($request->email);
                $mail->from('lnam6507@gmail.com');
                $mail->subject('Đơn hàng tại Cây cảnh Nam Lê đã được đặt!');
            }
        );

        alert()->success('Đơn hàng đã được đặt thành công!');

        return redirect()->route('home');
    }
    function product_detail($id)
    {
        $product = Product::findOrFail($id);
        return view('product_detail', compact('product'));
    }
    function product_category(Request $request, $id)
    {
        $cats = Category::all();
        $category_find = Category::findOrFail($id);
        // Filter
        $orderBy = $request->input('orderBy');
        $products = Product::where([
            ['category_id', '=', $id],
            ['status', '=', 1]
        ]);
        if ($orderBy == 'default') {
            $products = $products->orderBy('price', 'ASC')->get();
        } else if ($orderBy == 'latest') {
            $products = $products->orderBy('id', 'DESC')->get();
        } else if ($orderBy == 'low_price') {
            $products = $products->orderBy('price', 'ASC')->get();
        } else if ($orderBy == 'high_price') {
            $products = $products->orderBy('price', 'DESC')->get();
        } else {
            $products = $products->get();
        }
        return view('product_category', compact('products', 'cats', 'category_find'));
    }
    // Tìm kiếm - POST
    function search(Request $request)
    {
        $data = $request->all();
        $key = $request->key;
        $cats = Category::all();
        $products = Product::where('name', 'like', '%' . $key . '%')->orderByDesc('id')->get();
        return view('search', compact('products', 'cats', 'key'));
    }
    // Trang giới thiệu
    function about()
    {
        return view('about');
    }
    // Trang liên hệ
    function contact()
    {
        $post_new = Post::orderByDesc('id')->take(5)->get();
        return view('contact', compact('post_new'));
    }
    function store_contact(Request $request)
    {
        Mail::send(
            'email.contact',
            [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'content' => $request->content
            ],
            function ($mail) use ($request) {
                $mail->to('lnam6507@gmail.com');
                $mail->from($request->email);
                $mail->subject('Có liên hệ mới!');
            }
        );
        return back();
    }
    // Trang bài viết
    function new()
    {
        $posts = Post::all();
        $post_new = Post::orderByDesc('id')->take(5)->get();
        return view('new', compact('posts', 'post_new'));
    }
    // Chi tiết bài viết
    function new_detail($id)
    {
        $new_detail = null;
        if ($id) {
            $new_detail = Post::find($id);
            $comments = Comment::where('post_id', $new_detail->id)->orderByDesc('id')->get();
            $new_detail->increment('views', 1);
            $new_detail->save();
        }
        $post_new = Post::orderByDesc('id')->get();
        return view('new_detail', compact(['new_detail', 'post_new', 'comments']));
    }
    // Customer
    function info()
    {
        $cus = Auth::user();
        return view('customer.info', compact('cus'));
    }
    function store_info(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);

        $file = $request->file('avatar');
        if ($file) {
            $filename = $file->hashName();
            $file->storeAs('/public/avatars', $filename);
            $data['avatar'] = $filename;
        }

        $cus = Auth::user();

        $password = $request->password;

        if ($password) {
            $request->validate([
                'password' => 'min:6',
                'confirm_password' => 'required|same:password'
            ], [], [
                    'password' => 'Mật khẩu',
                    'confirm_password' => 'Mật khẩu nhập lại'
                ]);
            $data['password'] = Hash::make($password);
        } else {
            $data['password'] = $cus->password;
        }

        unset($data['old_password']);
        unset($data['confirm_password']);

        User::whereId($cus->id)->update($data);
        return back();
    }
    // Đơn hàng
    function my_order($id = null)
    {
        $cus = Auth::user();

        $my_order = Order::where('customer_id', $cus->id)->orderBy('id', 'desc')->get();
        $order_detail = null;

        if ($id) {
            $order_detail = OrderDetail::where('order_id', $id)->get();
        }

        return view('customer.my_order', compact('my_order', 'order_detail'));
    }
}