<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Http\Request;

class FavoriteAuctionController extends Controller
{
    public function addAuctionToFavorites(Request $request)
    {
        $user_id = $request->user_id;
        $auction_id = $request->auction_id;

        $user = User::find($user_id);
        $auction = Auction::find($auction_id);

        if (!$user || !$auction) {
            return response()->json(['message' => 'User or Auction not found', 'status' => 'false']);
        }

        if ($user->favoriteAuctions->contains($auction_id)) {
            return response()->json(['message' => 'The auction is already in your favorites', 'status' => 'false']);
        }

        $user->favoriteAuctions()->attach($auction_id);
        $message='add Auction to favorite';
        Broadcast(new \App\Events\FavoriteAuction($message));
        return response()->json(['message' => 'The auction has been successfully added to your favorites', 'status' => 'true']);
    }

    public function removeAuctionFromFavorites(Request $request)
    {
        $user_id = $request->user_id;
        $auction_id = $request->auction_id;

        $user = User::find($user_id);
        $auction = Auction::find($auction_id);

        if (!$user || !$auction) {
            return response()->json(['message' => 'User or Auction not found','status' => 'false']);
        }

        $user->favoriteAuctions()->detach($auction_id);
        $message="delete auction fro favorite";
        Broadcast(new \App\Events\FavoriteAuction($message));

        return response()->json(['message' => 'The auction has been successfully removed from favorites','status' => 'true']);
    }

    public function getFavoriteAuctions(Request $request)
    {
        $user_id = $request->user_id;

        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found','status' => 'false']);
        }

        $favoriteAuctions = $user->favoriteAuctions;


        foreach ($favoriteAuctions as $auction) {
            $auction->horses = $auction->horses;
        }

        return response()->json([
            'favorite_auctions' => $favoriteAuctions,
            'status' => true
        ]);
    }


}
