<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;


class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }
    public function brands()
    {
        $brands = Brand::orderBy('id', 'DESC')->paginate(10);
        return view('admin.brands', compact('brands'));
    }
    public function add_brand()
    {
        return view('admin.brand-add');
    }
    public function brand_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateBrandThumbailsImage($image, $file_name);
        $brand->image = $file_name;
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been added succesfully!');
    }

    public function brand_edit($id)
    {
        $brand = Brand::find($id);
        return view('admin.brand-edit', compact('brand'));
    }

    public function brand_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);
        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        if ($request->hasFile('iamge')) {
            if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
                File::delete(public_path('uploads/brands') . '/' . $brand->image);
            }

            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateBrandThumbailsImage($image, $file_name);
            $brand->image = $file_name;
        }
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been updated succesfully!');
    }

    public function GenerateBrandThumbailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }

    public function delete_brand($id)
    {
        $brand = Brand::find($id);
        if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
            File::delete(public_path('uploads/brands') . '/' . $brand->image);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status', 'Record has been deleted successfully !');
    }

    public function categories()
    {
        $categories = Category::orderBy('id','DESC')->paginate(10);
        return view('admin.categories',compact('categories'));
    }
    public function category_add()
    {
        return view('admin.category-add');
    }
    public function category_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);
        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateCategoryThumbailsImage($image, $file_name);
        $category->image = $file_name;
        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category has been added succesfully!');
    }
    public function GenerateCategoryThumbailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }
    public function category_edit($id)
    {
        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }

    public function category_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);
        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        if ($request->hasFile('iamge')) {
            if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
                File::delete(public_path('uploads/categories') . '/' . $category->image);
            }

            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateCategoryThumbailsImage($image, $file_name);
            $category->image = $file_name;
        }
        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category has been updated succesfully!');
    }
    public function category_delete($id)
    {
        $category = Category::find($id);
        if (File::exists(public_path('uploads/categories') . '/' . $category->image))
         {
            File::delete(public_path('uploads/categories') . '/' . $category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Record has been deleted successfully !');
    }


    public function products()
        {
        $products = Product::OrderBy('created_at','DESC')->paginate(10);
        return view("admin.products",compact('products'));
        }
    public function product_add()
        {
        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view("admin.product-add",compact('categories','brands'));
        }

        public function product_store(Request $request)
        {
            $request->validate([
                'name' => 'required',
                'slug' => 'required|unique:products,slug',
                'short_description' => 'required',
                'description' => 'required',
                'regular_price'=> 'required',
                'sale_price' => 'required',
                'SKU' => 'required',
                'stock_status' => 'required',
                'featured' => 'required',
                'quantity' => 'required',
                'image' => 'required|mimes:png,jpg,jepg|max:2048',
                'category_id' => 'required',
                'brand_id' => 'required'
                ]);
                $product = new Product();

                $product->name = $request->name;
                $product->slug = Str::slug($request->name);
                $product->short_description = $request->short_description;
                $product->description = $request->description;
                $product->regular_price = $request->regular_price;
                $product->sale_price = $request->sale_price;
                $product->SKU = $request->SKU;
                $product->stock_status = $request->stock_status;
                $product->featured = $request->featured;
                $product->quantity = $request->quantity;
                $product->category_id = $request->category_id;
                $product->brand_id = $request->brand_id;

                $current_timestamp = Carbon::now()->timestamp;

                if($request->hasFile('image'))
                {
                    $image =$request->File('image');
                    $imageName = $current_timestamp . '.'.$image->extension();
                    $this->  GenerateProductThumbailsImage($image,$imageName );
                    $product->image=$imageName;
                }

                $gallery_arr = array();
                $gallery_images = "";
                $counter = 1;

                if($request->hasFile('image'))
                {
                    $allowedfileExtion = ['png','jpg','jpeg'];
                    $files = $request->file('image');
                    foreach( $files as  $file)
                    {
                        $gextension =  $file->getClientOriginalExtension();
                        $gcheck = in_array($gextension , $allowedfileExtion);
                        if($gcheck)
                        {
                            $gfileName = $current_timestamp . "-" . $counter . "." . $gextension;
                            $this->GenerateProductThumbailsImage($file,$gfileName);
                            array_push($gallery_arr,$gfileName);
                            $counter = $counter + 1;
                        }
                    }
                    $gallery_images = implode(',',$gallery_arr);
                }
                $product->image = $gallery_images;
                $product->save();
                return redirect()->route('admin.products')-with('status','Product has been added successfully');

        }

        public function GenerateProductThumbailsImage($image, $imageName)
        {
            $destinationPathThumbail = public_path('uploads/products/thumbnails');
            $destinationPath = public_path('uploads/products');
            $img = Image::read($image->path());
            $img->cover(540, 689, "top");
            $img->resize(540, 689, function ($constraint)
            {
                $constraint->aspectRatio();
            })->save($destinationPath . '/' . $imageName);

            $img->resize(104, 104, function ($constraint)
            {
                $constraint->aspectRatio();
            })->save($destinationPathThumbail . '/' . $imageName);


        }
    }
