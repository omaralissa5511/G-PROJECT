<?php

namespace App\Http\Controllers\ADMIN;

use App\Http\Controllers\Controller;
use App\Models\CLUB\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function createCategory(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name',
            'description' => 'required',
            'image'=>'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }


        $file_extension = $request->image->getClientOriginalExtension();
        $filename = time() . '.' . $file_extension;
        $path = public_path('images/CLUB/CATEGORY');
        $request->image->move($path, $filename);

        $category = Category::create([
            'name' => $request->name,
            'description' =>$request->description,
            'image' =>$filename
        ]);

        return response()->json([
            'message' =>'Category is created successfully.',
            'category' => $category,
            'status' => true
            ]);
    }

    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'categories' => $categories,
            'status'=> true
            ]);
    }

    public function getCategory(Request $request)
    {
        $category = Category::find($request->id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found!',
                'status' => false
            ]);
        }

        return response()->json([
            'category' => $category,
            'status' => true
        ]);
    }

    public function updateCategory(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name',
            'description' => 'required',
            'image'=>'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
                'status' => false
            ]);
        }

        $category = Category::find($request->id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found!',
                'status' => false
            ]);
        }

        $file_extension = $request->image->getClientOriginalExtension();
        $filename = time() . '.' . $file_extension;
        $path = public_path('images/ADMIN/PROFILES');
        $request->image->move($path, $filename);


        $category->update([
            'name' => $request->name,
            'description' =>$request->description,
            'image'=>$filename
        ]);

        return response()->json([
            'message' =>'Category is updated successfully.',
            'category' => $category,
            'status' => true
        ]);
    }

    public function deleteCategory(Request $request)
    {
        $category = Category::find($request->id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found!',
                'status' => false
            ]);
        }

        $category->delete();

        return response()->json([
            'message' =>'Category is deleted successfully.',
            'status' => true
        ]);
    }

}

