<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Categories::paginate(10);
        return response()->json($categories);

    }
    public function show($id)
    {
        $category = Categories::find($id);
        if ($category) {
            return response()->json($category);
        } else {
            return response()->json(['status' => false, "message" => "category not found",], 404);
        }
    }
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), ['name' => 'required|unique:category,name', 'image' => 'required']);
        if ($validate->fails()) {
            return response()->json(['status' => false, 'message' => 'validation error', 'errors' => $validate->errors()], 400);
        }
        try {
            $category = new Categories();
            if ($request->hasFile('image')) {
                $path = 'assets/uploads/category/' . $category->image;
                if (File::exists($path)) {
                    File::delete($path);
                }
                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $filename = time() . '.' . $ext;
                try {
                    $file->move('assets/uploads/category', $filename);
                } catch (\Throwable $th) {
                    dd($th);
                }
            }
            $category->name = $validate->validated()['name'];
            $category->image = $filename;
            $category->save();


            return response()->json(['status' => true, 'category' => $category, 'message' => 'category created successfully'], 201);
        } catch (\Exception $th) {
            dd($th);
            return response()->json(['status' => false, 'message' => 'error creating category'], 400);
        }
    }
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), ['name' => 'required|unique:category,name', 'image' => 'required',]);

        if ($validate->fails()) {
            return response()->json(['status' => false, 'errors' => $validate->errors()], 400);
        }
        $category = Categories::find($id);
        if ($request->hasFile('image')) {
            $path = 'assets/uploads/category/' . $category->image;
            if (File::exists($path)) {
                File::delete($path);
            }
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            try {
                $file->move('assets/uploads/category', $filename);
            } catch (\Throwable $th) {
                dd($th);
            }
        }
        if ($validate->fails()) {
            return response()->json(['status' => false, 'message' => 'validation error', 'errors' => $validate->errors()], 400);
        }
        $category = Categories::where('id', $id)->update(['name' => $validate->validated()['name'], 'image' => $filename]);
        return response()->json(['status' => true, 'category' => $category, 'message' => 'category updated successfully']);
    }
    public function destroy($id)
    {
        try {

            Categories::where('id', $id)->delete();
            return response()->json(['status' => true, 'message' => 'category deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'api exception', 'exception' => $e->getMessage()], 400);
        }
    }

}
