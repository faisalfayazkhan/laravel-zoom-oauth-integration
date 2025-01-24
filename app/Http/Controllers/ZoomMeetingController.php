<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ZoomMeetingController extends Controller
{
    public $accessToken = 'eyJzdiI6IjAwMDAwMSIsImFsZyI6IkhTNTEyIiwidiI6IjIuMCIsImtpZCI6IjVjNGYxZGVlLWE3NjItNDQ5Ny1hMDRhLTNkZDlkMDBhYjEyZSJ9.eyJ2ZXIiOjksImF1aWQiOiI1YTE2Zjc5NTcyNDVjZDIxZDUzZTVkZDFhYzMxNmM2MCIsImNvZGUiOiJoVHZzd1Awb1BOaGNvUktOemxoUkdtS2UzMlE0bXNCMlEiLCJpc3MiOiJ6bTpjaWQ6Y19oaXlyMXVTMGVSaTA5RmdvamZVQSIsImdubyI6MCwidHlwZSI6MCwidGlkIjowLCJhdWQiOiJodHRwczovL29hdXRoLnpvb20udXMiLCJ1aWQiOiJQTVFibWJuMVQxbWVXaTJKOHVaakhnIiwibmJmIjoxNjkyOTkxNTEzLCJleHAiOjE2OTI5OTUxMTMsImlhdCI6MTY5Mjk5MTUxMywiYWlkIjoiY1BscF9VWWFTVWFlTExJbm0yVml2USJ9.-rM61JB945-9UJimjzrff8cClcyA3Q81wl8HdVXbqwUIXyUI3k-LJ_vAyzgJM7rFYHt2_6raxKv88VpOlcJ1Dg';
    public $refreshToken = 'eyJzdiI6IjAwMDAwMSIsImFsZyI6IkhTNTEyIiwidiI6IjIuMCIsImtpZCI6Ijg3M2VkMDE2LTEyNWYtNDM0Zi1iZDFhLTE5Nzc3MWE0NmIzMyJ9.eyJ2ZXIiOjksImF1aWQiOiI1YTE2Zjc5NTcyNDVjZDIxZDUzZTVkZDFhYzMxNmM2MCIsImNvZGUiOiJoVHZzd1Awb1BOaGNvUktOemxoUkdtS2UzMlE0bXNCMlEiLCJpc3MiOiJ6bTpjaWQ6Y19oaXlyMXVTMGVSaTA5RmdvamZVQSIsImdubyI6MCwidHlwZSI6MSwidGlkIjoxLCJhdWQiOiJodHRwczovL29hdXRoLnpvb20udXMiLCJ1aWQiOiJQTVFibWJuMVQxbWVXaTJKOHVaakhnIiwibmJmIjoxNjkyOTkyMTk1LCJleHAiOjE3MDA3NjgxOTUsImlhdCI6MTY5Mjk5MjE5NSwiYWlkIjoiY1BscF9VWWFTVWFlTExJbm0yVml2USJ9.frOwGvbV6lH52IZN3pt0SzeQY2wq6fGuQJigI43HLEYI0xY2oRyNndewCcok1OADL-kNOwghFb_MMITUEiT_BA';
        public function createMeeting(Request $request)
    {
        // Retrieve the authenticated user's Zoom access token from the database
        // $user = auth()->user();

        // Check if the token is expired or needs refreshing
        // if ($this->isTokenExpired($user->token_expiry)) {
            // $user = $this->refreshToken($user);
        // }

        // Use the refreshed or existing access token to create a Zoom meeting
          $this->refreshToken();
          dd("DSf");
        $response = Http::withToken($user->zoom_access_token)
            ->post('https://api.zoom.us/v2/users/me/meetings', [
                'topic' => 'My Meeting',
                'type' => 2, // 2 for scheduled meeting
                // Add more meeting parameters as needed
            ]);

        if ($response->successful()) {
            $meetingData = $response->json();
            // Handle the meeting data as needed
            return view('meeting.created', compact('meetingData'));
        } else {
            // Handle meeting creation failure
            return redirect()->back()->withErrors(['message' => 'Meeting creation failed']);
        }
    }

    public function updateMeeting(Request $request, $meetingId)
    {
        // Retrieve the authenticated user's Zoom access token from the database
        $user = auth()->user();

        // Check if the token is expired or needs refreshing
        if ($this->isTokenExpired($user->token_expiry)) {
            $user = $this->refreshToken($user);
        }

        // Update the Zoom meeting by making a PUT request to the Zoom API
        $response = Http::withToken($user->zoom_access_token)
            ->put("https://api.zoom.us/v2/meetings/{$meetingId}", [
                // Add parameters to update the meeting as needed
                'topic' => 'Updated Meeting Topic',
                // Add more meeting parameters to update
            ]);

        if ($response->successful()) {
            $updatedMeetingData = $response->json();
            // Handle the updated meeting data as needed
            return view('meeting.updated', compact('updatedMeetingData'));
        } else {
            // Handle meeting update failure
            return redirect()->back()->withErrors(['message' => 'Meeting update failed']);
        }
    }

    public function deleteMeeting($meetingId)
    {
        // Retrieve the authenticated user's Zoom access token from the database
        $user = auth()->user();

        // Check if the token is expired or needs refreshing
        if ($this->isTokenExpired($user->token_expiry)) {
            $user = $this->refreshToken($user);
        }

        // Delete the Zoom meeting by making a DELETE request to the Zoom API
        $response = Http::withToken($user->zoom_access_token)
            ->delete("https://api.zoom.us/v2/meetings/{$meetingId}");

        if ($response->successful()) {
            // Handle the successful deletion as needed
            return redirect('/meetings')->with('success', 'Meeting deleted successfully');
        } else {
            // Handle meeting deletion failure
            return redirect()->back()->withErrors(['message' => 'Meeting deletion failed']);
        }
    }

    private function isTokenExpired($tokenExpiry)
    {
        // Compare the token's expiration timestamp with the current time
        return now() >= $tokenExpiry;
    }

    private function refreshToken()
    {
        $response = Http::withQueryParameters([
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->refreshToken,
            'client_id' => config('services.zoom.client_id'),
            'client_secret' => config('services.zoom.client_secret'),
        ])->post('https://zoom.us/oauth/token');
        // $repo = Http::post('https://zoom.us/oauth/token?grant_type=refresh_token&refresh_token=eyJzdiI6IjAwMDAwMSIsImFsZyI6IkhTNTEyIiwidiI6IjIuMCIsImtpZCI6Ijg3M2VkMDE2LTEyNWYtNDM0Zi1iZDFhLTE5Nzc3MWE0NmIzMyJ9.eyJ2ZXIiOjksImF1aWQiOiI1YTE2Zjc5NTcyNDVjZDIxZDUzZTVkZDFhYzMxNmM2MCIsImNvZGUiOiJoVHZzd1Awb1BOaGNvUktOemxoUkdtS2UzMlE0bXNCMlEiLCJpc3MiOiJ6bTpjaWQ6Y19oaXlyMXVTMGVSaTA5RmdvamZVQSIsImdubyI6MCwidHlwZSI6MSwidGlkIjoxLCJhdWQiOiJodHRwczovL29hdXRoLnpvb20udXMiLCJ1aWQiOiJQTVFibWJuMVQxbWVXaTJKOHVaakhnIiwibmJmIjoxNjkyOTkyMTk1LCJleHAiOjE3MDA3NjgxOTUsImlhdCI6MTY5Mjk5MjE5NSwiYWlkIjoiY1BscF9VWWFTVWFlTExJbm0yVml2USJ9.frOwGvbV6lH52IZN3pt0SzeQY2wq6fGuQJigI43HLEYI0xY2oRyNndewCcok1OADL-kNOwghFb_MMITUEiT_BA&client_id=c_hiyr1uS0eRi09FgojfUA&client_secret=OcT9LnQciUgFQuNZ022NfLy4AmbwdQvK');
dd($response,$response->json());
        if ($response->successful()) {
            $data = $response->json();
            dd($data);

            // Update the user's access token and token expiry in the database
            $user->zoom_access_token = $data['access_token'];
            $user->token_expiry = now()->addSeconds($data['expires_in']);
            $user->save();

            return $user;
        } else {
            dd("error");
            // Handle token refresh failure
            return redirect()->back()->withErrors(['message' => 'Token refresh failed']);
        }
    }
}
