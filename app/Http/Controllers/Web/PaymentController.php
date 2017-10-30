<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\UserRegistrationRequest;
use App\Models\Team;
use App\Models\User;
use App\Services\CardService;
use Artesaos\Defender\Facades\Defender;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use function GuzzleHttp\Psr7\str;
use Request;
use Stripe\Card;
use Stripe\Stripe;


class PaymentController extends Controller
{
    private $key;
    private $role;
    private $service;


    public function __construct(CardService $service)
    {
        $this->key = "sk_test_vKQEgHfPSO1a5eJl2W0ZqUzW";
        $this->role = Defender::findRole('user');
        $this->service = $service;
    }

    public function index()
    {
        //return view('payment.register');
        //return view('payment.index');
        return view('payment.pay');
    }


}