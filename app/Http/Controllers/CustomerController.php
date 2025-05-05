<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Http\Services\CustomerService;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{

    /**
     * @var CustomerService
     */
    private CustomerService $service;

    public function __construct(CustomerService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service->all();
    }

    public function store(CustomerRequest $request)
    {
        return $this->service->save($request);
    }

    public function show(Customer $customer)
    {
        return $this->success($customer);
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        return $this->service->update($customer, $request->validated());
    }

    public function destroy(Customer $customer)
    {
        return $this->service->destroy($customer);
    }

    public function delete()
    {
        $model = Customer::query()->where('id', \auth('sanctum')->user()->id)->first();
        return $this->service->destroy($model);
    }
}
