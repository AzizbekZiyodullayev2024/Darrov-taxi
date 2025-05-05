<?php

namespace App\Http\Controllers;

use App\Http\Services\MessageService;
use App\Models\Client;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\DriverLoginRequest;
use App\Http\Requests\VerifyRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Services\AuthService;
use App\Jobs\SendMessageJob;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Partner;
use App\Models\User;


class AuthController extends Controller
{

    private AuthService $service;

    public function __construct()
    {
        $this->service = new AuthService();
    }

    /* Authenticate */
    function login(LoginRequest $request)
    {
        list($token, $model) = $this->service->login(new User(), $request->validated());
        if ($token) {
            return $this->success([
                'token' => $token,
                'model' => $model
            ]);
        }

        return $this->fail();
    }

    // Driver login
    public function driver(DriverLoginRequest $request)
    {
        $driver = Driver::query()->where('phone',
            $request->get('phone'))->first();


        if ($driver) {
            $driver->password = rand(1000, 9999);
            $driver->update();
            $driver->refresh();
            $sms=new MessageService();
            $sms->sendMessage($driver->phone,
                "<#> Darrov ilovasiga kirish uchun tasdiqlash kodi: $driver->password"
            );
            return $this->success([]);
        }

        return $this->fail([]);
    }
    //Delivery Login
    public function delivery(DriverLoginRequest $request){
        $delivery = Delivery::query()->where('phone',
            $request->get('phone'))->first();
        if($delivery){
            $delivery->password = rand(1000, 9999);
            $delivery->update();
            $delivery->refresh();
            $sms=new MessageService();
            $sms->sendMessage($delivery->phone,
                "<#> Darrov ilovasiga kirish uchun tasdiqlash kodi: $delivery->password"
            );
            return $this->success([
//                'code'=>$delivery->password
            ]);
        }else{
            return $this->fail([]);
        }
    }

    // Driver verify
    public function verify(VerifyRequest $request)
    {
        $data = $request->validated();
        $driver = Driver::query()->where('phone', $data['phone'])
            ->where('password', $data['code'])->firstOrFail();

        if ($driver) {
            $driver->password = '';
            $driver->update();
            return $this->success([
                'id' => $driver->id,
                'token' => $driver->createToken('auth_token')->plainTextToken
            ]);
        }

        return $this->fail([]);
    }

    public function verifyDelivery(VerifyRequest $request)
    {
        $data = $request->validated();
        $driver = Delivery::query()->where('phone', $data['phone'])
            ->where('password', $data['code'])->firstOrFail();

        if ($driver) {
            $driver->password = '';
            $driver->update();
            return $this->success([
                'id' => $driver->id,
                'token' => $driver->createToken('auth_token')->plainTextToken
            ]);
        }

        return $this->fail([]);
    }

    // Partner
    public function partner(LoginRequest $request)
    {
        list($token, $model) = $this->service->login(new Partner(), $request->validated());
        if ($token) {
            return $this->success([
                'token' => $token,
                'partner' => $model
            ]);
        }

        return $this->fail();
    }

    // Customer
    public function customer(DriverLoginRequest $request)
    {
        $phone = $request->get('phone');
        $customer = Customer::query()->where('phone', $phone)
            ->firstOrCreate(['phone' => $phone]);

        if ($phone == '994576678') {
            $customer->password = 7777;
        } else {
            $customer->password = rand(1000, 9999);
        }
        $customer->update();
        $customer->refresh();
        $sms=new MessageService();
        $sms->sendMessage($customer->phone,
          "<#> Darrov ilovasiga kirish uchun tasdiqlash kodi: $customer->password"
        );
        return $this->success([
//            'code' => $customer->password
        ]);
    }

    public function verifyCustomer(VerifyRequest $request)
    {
        $data = $request->validated();
        $customer = Customer::query()->where('phone', $data['phone'])
            ->where('password', $data['code'])->first();
        if ($customer) {
            $customer->password = '';
            $customer->update();
            return $this->success([
                'model' => $customer,
                'token' => $customer->createToken('auth_token')->plainTextToken
            ]);
        }

        return $this->fail([]);
    }

    public function client(DriverLoginRequest $request)
    {
        $phone = $request->get('phone');
        $client = Client::query()->where('phone', $phone)
            ->firstOrCreate(['phone' => $phone]);

        $client->password = rand(1000, 9999);
        $client->update();
        $client->refresh();
        $sms=new MessageService();
        $res=$sms->sendMessage($client->phone,
            "<#> Darrov ilovasiga kirish uchun tasdiqlash kodi: $client->password"
        );
        return $this->success([
//            'code' => $client->password
        ]);
    }

    public function verifyClient(VerifyRequest $request)
    {
        $data = $request->validated();
        $client = Client::query()->where('phone', $data['phone'])
            ->where('password', $data['code'])->first();
        if ($client) {
            $client->password = '';
            $client->update();
            return $this->success([
                'model' => $client,
                'token' => $client->createToken('auth_token')->plainTextToken
            ]);
        }

        return $this->fail([]);
    }


    public function receive(Request $request)
    {// EXPIRED
        if ($request->get('status') != "DELIVRD")
            Cache::put('token', $this->getToken());
    }
}
