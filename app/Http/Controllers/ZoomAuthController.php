<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ZoomAuthController extends Controller
{
     public function redirectToZoom()
    {
//         $codeVerifier = bin2hex(random_bytes(32)); // Generate a random code verifier
// $codeChallenge = base64_encode(hash('sha256', $codeVerifier, true)); // Derive code challenge
// dd($codeVerifier,$codeChallenge);
        $zoomAuthUrl = 'https://zoom.us/oauth/authorize?' . http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.zoom.client_id'),
            'redirect_uri' => config('services.zoom.redirect_uri'),
            // 'code_challenge'=> 'RjCZ78MbNFDOPcdjyhVw9uzby5M9HGAbqruDOkkjzpI=',
            // 'code_challenge_method'=> 'S256'
        ]);

        return redirect($zoomAuthUrl);
    }

    public function handleZoomCallback(Request $request)
    {
        $code = $request->query('code');

        $response = Http::withQueryParameters([
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('services.zoom.redirect_uri'),
            'client_id' => config('services.zoom.client_id'),
            'client_secret' => config('services.zoom.client_secret'),
        ])->post('https://zoom.us/oauth/token');

        $data = $response->json();
        dd("Authorization Successfull!","Please save your access token now",$data);
        dd("Please save your access token now");
        dd($data);

        // Store the Zoom access token and refresh token in your user's record in the database
        // Typically, you would associate these tokens with the authenticated user.

        return redirect('/');
    }
}
