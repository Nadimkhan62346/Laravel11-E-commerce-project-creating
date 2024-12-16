<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('Admin.index');
    }
    public function brands()
    {
        $brands = Brand::orderBy('id','DESC')->paginate(10);
        return view('Admin.brands',compact('brands'));
    }
    public function add_brand()
    {
        return view('admin.add-brand');
    }
}
