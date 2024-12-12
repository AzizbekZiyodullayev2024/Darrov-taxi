<?php


namespace App\Http\Services;


use App\Models\Partner;
use Illuminate\Database\Eloquent\Model;

class PartnerService extends CRUDService
{
    public function __construct(Partner $model = null) {
        parent::__construct($model ?? new Partner());
    }

    public function all() {
        return $this->indexResponse(Partner::query()->paginate(100));
    }

    public function save($request) {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        if ($request->hasFile('img'))
            $data['img'] = $this->saveImage($request->file('img'), 'partners');

        return parent::store($data);
    }

    public function update($partner, $request) {
        $data = $request->validated();
        if ($request->hasFile('img')) {
            $data['img'] = $this->saveImage($request->file('img'), 'partners');
            $this->deleteFile($partner->img, 'partners');
        }
        dd($data,$request->toArray());
        return parent::update($partner, $data);
    }

    public function publicAll()
    {
        return $this->success(['partners' => Partner::all()]);
    }
}
