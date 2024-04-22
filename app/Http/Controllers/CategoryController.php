<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\CLUB\Category;
use App\Models\CLUB\Service;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getCategories()
    {
        $categories = Category::all();
        return response()->json([
            'categories' => $categories,
            'status' => true
        ]);
    }

    public function categoryServices($Cid)
    {

        $category = Category::find($Cid)->first();
        $categoryServices = $category->services();

        if ($categoryServices) {
            return response()->json([
                'categories' => $categoryServices,
                'status' => true
            ]);
        } else {
            return response()->json([
                'message' => 'no services for this category',
                'status' => false
            ]);
        }
    }

    public function serviceClubs ($Sid){

        $service = Service::find($Sid)->first();
        $serviceClubs = $service -> clubs();


        if ($serviceClubs) {
            return response()->json([
                'serviceClubs' => $serviceClubs,
                'status' => true
            ]);
        } else {
            return response()->json([
                'message' => 'no clubs for this service',
                'status' => false
            ]);
        }

    }
 }
