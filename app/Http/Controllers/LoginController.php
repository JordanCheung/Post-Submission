<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Brick\Math\BigInteger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Client\Provider\GenericProvider;
use \DateTime;

class LoginController extends Controller
{
    private $provider;

    public function __construct()
    {
        $this->provider = new GenericProvider([
            'clientId'                => env('OAUTH_APP_ID'),
            'clientSecret'            => env('OAUTH_APP_SECRET'),
            'redirectUri'             => env('OAUTH_REDIRECT_URI'),
            'urlAuthorize'            => env('OAUTH_AUTHORIZE_ENDPOINT'),
            'urlAccessToken'          => env('OAUTH_TOKEN_ENDPOINT'),
            'scopes'                  => env('OAUTH_SCOPES'),
            'urlResourceOwnerDetails' => '',
        ]);
    }

    public function login()
    {
        $this->provider->authorize();
    }

    public function logout()
    {
        Auth::guard('dashboard')->logout();
        return redirect('home');
    }

    public function callback(Request $request)
    {
        $request->validate(['code' => ['required', 'alpha_dash']]);
        $code  = $request->input('code');

        try
        {
            $token = $this->provider->getAccessToken('authorization_code', ['code' => $code]);

            $response  = $this->provider->getAuthenticatedRequest('GET', 'https://graph.facebook.com/v9.0/me?fields=name,picture', $token);
            $contents = $this->provider->getParsedResponse($response);

            $id = (int)$contents['id'];
            $name = $contents['name'];
            $picture = $contents['picture']['data']['url'];

            $profile = new Profile();
            $profile->setAttribute('id', $id);
            $profile->setAttribute('name', $name);
            $profile->setAttribute('picture', $picture);

            $exist = Profile::find($id);

            if ($exist != null)
            {
                Auth::guard('dashboard')->loginUsingId($id, true);
            }
            else
            {
                $now = new DateTime("now");
                $profile->setAttribute('created_at', $now);
                $profile->setAttribute('updated_at', $now);
                Auth::guard('dashboard')->login($profile, true);
            }

            return redirect()->route('dashboard');

        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());
            return view('error');
        }
    }
}
