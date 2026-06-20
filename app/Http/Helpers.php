<?php

use App\Models\Message;
use App\Models\Category;
use App\Models\PostTag;
use App\Models\PostCategory;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\Shipping;
use App\Models\Cart;
use Illuminate\Support\Str;

// use Auth;
class Helper
{
    public static function messageList()
    {
        return Message::whereNull('read_at')->orderBy('created_at', 'desc')->get();
    }
    public static function getAllCategory()
    {
        $category = new Category();
        $menu = $category->getAllParentWithChild();
        return $menu;
    }

    public static function getHeaderCategory()
    {
        $category = new Category();
        // dd($category);
        $menu = $category->getAllParentWithChild();

        if ($menu) {
?>

            <li>
                <a href="javascript:void(0);">Category<i class="ti-angle-down"></i></a>
                <ul class="dropdown border-0 shadow">
                    <?php
                    foreach ($menu as $cat_info) {
                        if ($cat_info->child_cat->count() > 0) {
                    ?>
                            <li><a href="<?php echo route('product-cat', $cat_info->slug); ?>"><?php echo $cat_info->title; ?></a>
                                <ul class="dropdown sub-dropdown border-0 shadow">
                                    <?php
                                    foreach ($cat_info->child_cat as $sub_menu) {
                                    ?>
                                        <li><a href="<?php echo route('product-sub-cat', [$cat_info->slug, $sub_menu->slug]); ?>"><?php echo $sub_menu->title; ?></a></li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </li>
                        <?php
                        } else {
                        ?>
                            <li><a href="<?php echo route('product-cat', $cat_info->slug); ?>"><?php echo $cat_info->title; ?></a></li>
                    <?php
                        }
                    }
                    ?>
                </ul>
            </li>
<?php
        }
    }

    public static function productCategoryList($option = 'all')
    {
        if ($option == 'all') {
            return Category::orderBy('id', 'DESC')->get();
        }
        return Category::has('products')->orderBy('id', 'DESC')->get();
    }

    public static function postTagList($option = 'all')
    {
        if ($option == 'all') {
            return PostTag::orderBy('id', 'desc')->get();
        }
        return PostTag::has('posts')->orderBy('id', 'desc')->get();
    }

    public static function postCategoryList($option = "all")
    {
        if ($option == 'all') {
            return PostCategory::orderBy('id', 'DESC')->get();
        }
        return PostCategory::has('posts')->orderBy('id', 'DESC')->get();
    }
    // Cart Count
    public static function cartCount($user_id = '')
    {

        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Cart::where('user_id', $user_id)->where('order_id', null)->sum('quantity');
        } else {
            return 0;
        }
    }
    // relationship cart with product
    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }

    public static function getAllProductFromCart($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Cart::with('product')->where('user_id', $user_id)->where('order_id', null)->get();
        } else {
            return 0;
        }
    }
    // Total amount cart
    public static function totalCartPrice($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Cart::where('user_id', $user_id)->where('order_id', null)->sum('amount');
        } else {
            return 0;
        }
    }
    // Wishlist Count
    public static function wishlistCount($user_id = '')
    {

        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Wishlist::where('user_id', $user_id)->where('cart_id', null)->sum('quantity');
        } else {
            return 0;
        }
    }
    public static function getAllProductFromWishlist($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Wishlist::with('product')->where('user_id', $user_id)->where('cart_id', null)->get();
        } else {
            return 0;
        }
    }
    public static function totalWishlistPrice($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Wishlist::where('user_id', $user_id)->where('cart_id', null)->sum('amount');
        } else {
            return 0;
        }
    }

    // Total price with shipping and coupon
    public static function grandPrice($id, $user_id)
    {
        $order = Order::find($id);
        if ($order) {
            $shipping_price = (float)$order->shipping->price;
            $order_price = self::orderPrice($id, $user_id);
            return number_format((float)($order_price + $shipping_price), 2, '.', '');
        } else {
            return 0;
        }
    }


    // Admin home
    public static function earningPerMonth()
    {
        $month_data = Order::where('status', 'delivered')->get();
        // return $month_data;
        $price = 0;
        foreach ($month_data as $data) {
            $price = $data->cart_info->sum('price');
        }
        return number_format((float)($price), 2, '.', '');
    }

    public static function shipping()
    {
        return Shipping::orderBy('id', 'DESC')->get();
    }
}



if (!function_exists('generateUniqueSlug')) {
    /**
     * Generate a unique slug for a given title and model.
     *
     * @param string $title
     * @param string $modelClass
     * @return string
     */
    function generateUniqueSlug($title, $modelClass)
    {
        $slug = Str::slug($title);
        $count = $modelClass::where('slug', $slug)->count();

        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }

        return $slug;
    }
}

if (!function_exists('compressImage')) {

    /*
    * Compress Image
    */
    function compressImage($path, $keepOriginal = true)
    {
        if ($keepOriginal) {
            $origFilePath = public_path('/') . Str::replaceLast('.', '_original.', $path);
            rename(public_path('/') . $path, $origFilePath);
        } else {
            $origFilePath = $path;
        }
        $img = \Image::make($origFilePath);
        $img->orientate();
        $imgSize = $img->filesize() / 1024;
        if ($imgSize <= 100) {
            $quality = 80;
        } elseif ($imgSize <= 200) {
            $quality = 75;
        } elseif ($imgSize <= 400) {
            $quality = 65;
        } elseif ($imgSize <= 800) {
            $quality = 55;
        } elseif ($imgSize <= 1024) {
            $quality = 45;
        } elseif ($imgSize <= 2048) {
            $quality = 35;
        } elseif ($imgSize <= 4096) {
            $quality = 30;
        } else {
            $quality = 20;
        }

        if ($keepOriginal) {
            return $img->save($path, $quality, 'jpg');
        } else {
            return $img->save(null, $quality, 'jpg');
        }
    }
}

if (!function_exists('thumbnail')) {

    /*
    * Compress Image for thumbnail
    */
    function thumbnail($path)
    {
        $thumbnailPath = Str::replaceLast('.', '_thumbnail.', $path);
        $img = \Image::make(public_path('/') . $path);
        $img->orientate();
        $imgSize = $img->filesize() / 1024;
        if ($imgSize <= 100) {
            $quality = 10; //60
        } elseif ($imgSize <= 200) {
            $quality = 10; //55
        } elseif ($imgSize <= 400) {
            $quality = 10; //45
        } elseif ($imgSize <= 800) {
            $quality = 10; //17
        } elseif ($imgSize <= 1024) {
            $quality = 10; //14
        } elseif ($imgSize <= 2048) {
            $quality = 8;
        } elseif ($imgSize <= 4096) {
            $quality = 6;
        } else {
            $quality = 4;
        }
        return $img->save($thumbnailPath, $quality, 'jpg');
    }
}

if (!function_exists('getThumbnailAndOriginalImage')) {

    /*
    * Compress Image for thumbnail
    */
    function getThumbnailAndOriginalImage($image, $column_name = 'path')
    {

        $image['original_image'] = null;
        $image['thumb_image'] = null;
        if (!empty($image[$column_name]) && !is_null($image[$column_name])) {
            $fullUrl = explode('.', $image[$column_name]);

            $image['original_image'] = $fullUrl[0] . '_original' . '.' . $fullUrl[1];
            $image['thumb_image'] = $fullUrl[0] . '_thumbnail' . '.' . $fullUrl[1];
        } else {
            $image['original_image'] = null;
            $image['thumb_image'] = null;
        }

        return $image;
    }
}


if (!function_exists('limitPerPage')) {

    /*
    * Get limit of items to return per page
    */
    function limitPerPage($request)
    {
        return $request->has('limit') && $request->limit > 0 ? $request->limit : config('constant.pagination_count');
    }
}

if (!function_exists('contactLimitPerPage')) {
    /*
    * Get limit of items for contact
    */
    function contactLimitPerPage($request)
    {
        return $request->has('limit') && $request->limit > 0 ? $request->limit : config('constant.contact_pagination_count');
    }
}

if (!function_exists('generateKey')) {
    function generateKey($minlength = 20, $maxlength = 20, $uselower = true, $useupper = true, $usenumbers = true, $usespecial = false)
    {
        $charset = '';
        if ($uselower) {
            $charset .= "abcdefghijklmnopqrstuvwxyz";
        }
        if ($useupper) {
            $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
        if ($usenumbers) {
            $charset .= "123456789";
        }
        if ($usespecial) {
            $charset .= "~@#$%^*()_+-={}|][";
        }
        if ($minlength > $maxlength) {
            $length = mt_rand($maxlength, $minlength);
        } else {
            $length = mt_rand($minlength, $maxlength);
        }
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $charset[(mt_rand(0, strlen($charset) - 1))];
        }
        return $key;
    }
}


?>