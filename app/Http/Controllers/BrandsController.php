<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brands;
use Illuminate\Support\Facades\Validator;

class BrandsController extends Controller
{
    //
    public function index()
    {
        $brands = Brands::paginate(10);
        return response()->json($brands);

    }
    public function show($id)
    {
        $brand = Brands::find($id);
        if ($brand) {
            return response()->json($brand);
        } else {
            return response()->json(['status' => false, "message" => "brand not found",], 404);
        }
    }
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), ['name' => 'required|unique:brands,name',]);
        if ($validate->fails()) {
            return response()->json(['status' => false, 'message' => 'validation error', 'errors' => $validate->errors()], 400);
        }
        $brand = Brands::create($validate->validated());
        if ($brand) {
            return response()->json(['status' => true, 'brand' => $brand, 'message' => 'brand created successfully'], 201);
        } else {
            return response()->json(['status' => false, 'message' => 'error creating brand'], 400);
        }
    }
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), ['name' => 'required']);
        if ($validate->fails()) {
            return response()->json(['status' => false, 'message' => 'validation error', 'errors' => $validate->errors()], 400);
        }
        $brand = Brands::where('id', $id)->update(['name' => $validate->validated()['name']]);
        return response()->json(['status' => true, 'brand' => $brand, 'message' => 'brand updated successfully']);
    }
    public function destroy($id)
    {
        try {

            Brands::where('id', $id)->delete();
            return response()->json(['status' => true, 'message' => 'brand deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'api exception', 'exception' => $e->getMessage()], 400);
        }
    }
}
