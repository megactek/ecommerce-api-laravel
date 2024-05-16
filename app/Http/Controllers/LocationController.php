<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    //
    public function store()
    {
        $validate = Validator::make(request()->all(), ['street' => 'required', 'building' => 'required', 'area' => 'required',]);
        Locations::create(array_merge($validate->validated(), ['user_id' => \Auth::user()->id]));
        return response()->json(['message' => 'address added'], 201);
    }
    public function update(Request $request, $id)
    {
        $validate = Validator::make(request()->all(), ['building' => 'required', 'area' => 'required', 'street' => 'required']);
        Locations::where('id', $id)->update($validate->validated());
        return response()->json(['message' => 'address updated'], 200);
    }
    public function destroy($id)
    {
        try {
            Locations::where('id', $id)->delete();
            return response()->json(['message' => 'address updated'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'location not found'], 404);
        }
    }
}
