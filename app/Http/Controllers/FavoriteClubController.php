<?php

namespace App\Http\Controllers;

use App\Models\CLUB\Equestrian_club;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteClubController extends Controller
{
    public function addClubToFavorites(Request $request)
    {
        $user_id = $request->user_id;
        $club_id = $request->club_id;

        $user = User::find($user_id);
        $club = Equestrian_club::find($club_id);

        if (!$user || !$club) {
            return response()->json(['message' => 'User or Club not found', 'status' => 'false']);
        }


        if ($user->favoriteClubs->contains($club_id)) {
            return response()->json(['message' => 'The club is already in your favorites', 'status' => 'false']);
        }

        $user->favoriteClubs()->attach($club_id);

        return response()->json(['message' => 'The club has been successfully added to your favorites', 'status' => 'true']);
    }


    public function removeClubFromFavorites(Request $request)
    {
        $user_id = $request->user_id;
        $club_id = $request->club_id;

        $user = User::find($user_id);
        $club = Equestrian_club::find($club_id);

        if (!$user || !$club) {
            return response()->json(['message' => 'User or Club not found','status' => 'false']);
        }

        $user->favoriteClubs()->detach($club_id);

        return response()->json(['message' => 'The club has been successfully removed from favorites','status' => 'true']);
    }

    public function getFavoriteClubs(Request $request)
    {
        $user_id = $request->user_id;

        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found','status' => 'false']);
        }
        $favoriteClubs = $user->favoriteClubs;
        foreach ($favoriteClubs as $favoriteClub){
            $favoriteClub->day=json_decode($favoriteClub->day);
            $favoriteClub->day = explode(',', $favoriteClub->day[0]);
        }

        return response()->json([
            'favorite_clubs' => $favoriteClubs,
            'status' => true
        ]);
    }

}
