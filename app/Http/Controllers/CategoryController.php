<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\CLUB\Category;
use App\Models\CLUB\ClubServise;
use App\Models\CLUB\Equestrian_club;
use App\Models\CLUB\Service;
use App\Models\User;
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

        $category = Category::where('id',$Cid)->first();

        if($category){
            $categoryServices = Service::where('category_id',$category->id)->get();

            if ($categoryServices) {
                return response()->json([
                    'services' => $categoryServices,
                    'status' => true
                ]);
            } else {
                return response()->json([
                    'message' => 'no services for this category',
                    'status' => false
                ]);
            }
        }
        else {
            return response()->json([
                'message' => 'no category for this id',
                'status' => false
            ]);
        }


    }

    public function serviceClubs ($Sid){

        $club_id =  Service::where('id',$Sid)->first()->club_id;
        $user_id = Equestrian_club::where('id',$club_id)->first()->user_id;
        $club = Equestrian_club::where('id',$club_id)->first();
        $user = User::where('id',$user_id)->first();



        if ($club) {
            return response()->json([
                'club' => $club,
                'user' => $user,
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
