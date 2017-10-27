<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\UserRegistrationRequest;
use App\Models\Team;
use App\Models\User;
use Artesaos\Defender\Facades\Defender;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Request;
use Stripe\Card;
use Stripe\Stripe;


class PaymentController extends Controller
{
    private $key;
    private $role;


    public function __construct()
    {
        $this->middleware('needsRole:admin');
        // Set your secret key: remember to change this to your live secret key in production
        // See your keys here: https://dashboard.stripe.com/account/apikeys
        $this->key = "sk_test_vKQEgHfPSO1a5eJl2W0ZqUzW";
        $this->role = Defender::findRole('user');
    }

    public function index()
    {
        //return view('payment.register');
        return view('payment.index');
    }

    public function termsAccept()
    {
        $planId = bcrypt(request()->post('id'));
        return $planId;
    }

    public function getCard() {
        Stripe::setApiKey($this->key);
        $customer = \Stripe\Customer::retrieve(auth()->user()->stripe_id);
        $card = $customer->sources->retrieve($customer->default_source);
        return dd($card->jsonSerialize());
    }

    public function registerUser($plan)
    {
        return view('payment.user-form',compact('plan'));
    }

    public function store(UserRegistrationRequest $request)
    {

        Stripe::setApiKey($this->key);

        $team = new  Team();
        $team->name = $request->input('team');
        $team->slugname = str_slug($team->name,'-');
        $team->save();

        $user = new  User();

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->remember_token = str_random(10);
        $user->team_id = $team->id;
        $user->trial_ends_at = Carbon::now()->addDays(14);

        $user->save();


        if ($user->save()) {
            $plan = $request->input('plan');
            $email = $request->input('email');
            $owner = $request->input('owner');

            $user->newSubscription('main', $plan)
                ->trialDays(14)
                ->create(request('stripeToken'), [
                    'email' => $email,
                ]);

            $customer = \Stripe\Customer::retrieve($user->stripe_id);
            $card = $customer->sources->retrieve($customer->default_source);
            $card->name = $owner;
            $card->save();

            //save card expiration users table
            $user->card_exp_month = $card->exp_month;
            $user->card_exp_year = $card->exp_year;

            $user->save();

        }


        return view('user.index',compact('user'));

    }

    public function cardExp(Request $request){
        return $request::all();
    }
}
