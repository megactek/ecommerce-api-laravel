<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    //
    public function index()
    {
        $products = Products::orderBy("id", "desc")->paginate(10);
        return response()->json($products);
    }
    public function show($id)
    {
        $product = Products::find($id);
        if ($product) {
            return response()->json(['status' => true, 'message' => 'product found', 'data' => $product]);
        } else {
            return response()->json(['status' => false, 'message' => 'product not found'], 404);
        }
    }
    public function store(Request $request)
    {
        $validate = \Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
            'brand_id' => 'required|numeric',
            'discount' => 'required|numeric',
            'amount' => 'required|numeric',
            'image' => 'required',
            'quantity' => 'required|numeric'
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validate->errors()
            ], 400);
        }
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $path = 'assets/uploads/products/' . $filename;
            if (File::exists($path)) {
                File::delete($path);
            }
            try {
                $file->move('assets/uploads/products', $filename);
            } catch (\Exception $e) {
                dd($e->getMessage());
            }
        }

        $product = Products::create(array_merge($validate->validated(), ['image' => $filename]));
        return response()->json(['status' => true, 'product' => $product, 'message' => 'product created successfully']);
    }
    public function update(Request $request, $id)
    {
        $validate = \Validator::make($request->all(), ['name' => 'required', 'price' => 'required|numeric', 'category_id' => 'required|numeric', 'brand_id' => 'required|numeric', 'discount' => 'required|numeric', 'amount' => 'required|numeric', 'image' => 'required']);
        if ($validate->fails()) {
            return response()->json(['status' => false, 'message' => $validate->errors()], 400);
        }
        $product = Products::find($id);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $path = 'assets/uploads/products/' . $product->image;
            if (File::exists($path)) {
                File::delete($path);
            }
            try {
                $file->move('assets/uploads/products', $filename);
            } catch (\Exception $e) {
                dd($e->getMessage());
            }
        }
        $product->update(array_merge($validate->validated(), ['image' => $filename]));
        return response()->json(['status' => true, 'product' => $product, 'message' => 'product updated']);

    }
    public function destroy($id)
    {
        try {
            Products::find($id)->delete();
            return response()->json(['status' => true, 'message' => 'product deleted']);

        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => 'product not found'], 404);
        }
    }
}
