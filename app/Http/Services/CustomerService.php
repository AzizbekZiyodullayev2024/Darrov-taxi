<?php


namespace App\Http\Services;

use App\Models\Customer;

class CustomerService extends CRUDService
{
    public function __construct(Customer $model = null)
    {
        parent::__construct($model ?? new Customer());
    }

    public function all() {
        return $this->success(Customer::all());
    }

    public function save($request) {
        if ($request->hasFile('img'))
            $data['img'] = $this->saveImage($request->file('img'), 'customers');

        return parent::store($request->validated());
    }
}
