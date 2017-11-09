<?php

namespace App\Http\Controllers\Auth;

use App\Forms\CardForm;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Team;
use App\Models\User;
use App\Services\CardService;
use Artesaos\Defender\Facades\Defender;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stripe\Stripe;

class RegisterController extends Controller
{
    private $key;
    private $service;
    private $user;
    private $role;

    public function __construct(CardService $service)
    {
        $this->key = config('services.stripe.secret');
        Stripe::setApiKey($this->key);
        $this->service = $service;
        $this->role = Defender::findRole('user');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function registerPost(RegisterRequest $request)
    {
        $team = new  Team();
        $team->name = $request->input('company');
        $team->email = $request->input('email');
        $team->slug = str_slug($request->input('company'), '-');
        $team->save();
        $user = new  User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->remember_token = str_random(10);
        $user->team_id = $team->id;
        $user->trial_ends_at = Carbon::now()->addDays(14);
        $user->save();

        $team->owner_id = $user->id;
        $team->save();


        $user->attachRole($this->role);
        $plan = $request->input('plan');
        $email = $request->input('email');
        $owner = $request->input('owner');

        Stripe::setApiKey($this->key);

        $user->newSubscription('main', $plan)
            ->trialDays(14)
            ->create(request('card'), [
                'email' => $email,
            ]);

        $data = $this->service->store($user, $owner);

        $response = [
            "message" => $data,
            "data" => $data,
        ];

        return view('auth.login', compact('response'));
    }

    public function invitation($token)
    {
        $isValid = false;
        $user = User::query()->where('invitation_token', $token)->first();
        if ($user) {
            $isValid = true;
        }
        return view('auth.invitation', compact('isValid', 'token'));
    }

    public function registerInvitation(Request $request)
    {
        $user = User::where('invitation_token', $request->input('invitation_token'))->first();

        $user->name = $request->input('name');
        $user->invitation_token = null;
        $user->password = bcrypt($request->input('password'));
        $user->remember_token = str_random(10);
        $user->save();
        $role = Defender::findRole('user');
        $user->attachRole($role);

        return redirect()->route('home');
    }

    public function termsOfService()
    {
        return view('auth.terms-of-service');
    }

}
