<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartnerUpdateRequest;
use App\Http\Services\PartnerService;
use App\Http\Requests\PartnerRequest;
use App\Models\History;
use App\Models\Partner;
use Illuminate\Support\Facades\DB;

class PartnerController extends Controller
{

    /**
     * @var PartnerService
     */
    private PartnerService $service;

    public function __construct(PartnerService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service->all();
    }

    public function indexPublic()
    {
        return $this->service->publicAll();
    }

    public function store(PartnerRequest $request)
    {
        return $this->service->save($request);
    }

    public function show(Partner $partner)
    {
        return $this->success(['partner' => $partner]);
    }

    public function update(PartnerUpdateRequest $request, Partner $partner)
    {
        return $this->service->update($partner, $request);
    }

    public function updateImage(PartnerUpdateRequest $request, Partner $partner)
    {
        return $this->service->updateImage($partner, $request);
    }

    public function destroy(Partner $partner)
    {
        return $this->service->destroy($partner);
    }

    public function statPartner()
    {

        $today = \request()->has('date')?\request()->get('date'):now()->format('Y-m-d');
        $query=History::query()
            ->join('orders', 'histories.order_id', '=', 'orders.id')
            ->join('partners', 'orders.partner_id', '=', 'partners.id')
            ->select(
                'orders.partner_id',
                'partners.name as partner_name',
                DB::raw("COUNT(CASE WHEN DATE(histories.created_at) = '" . $today . "' THEN 1 END) AS daily_count"),
                DB::raw('COUNT(CASE WHEN YEAR(histories.created_at) = YEAR(CURDATE()) AND MONTH(histories.created_at) = MONTH(CURDATE()) THEN 1 END) AS monthly_count'),
                DB::raw('COUNT(CASE WHEN YEAR(histories.created_at) = YEAR(CURDATE()) THEN 1 END) AS yearly_count'),
                DB::raw("SUM(CASE WHEN DATE(histories.created_at) = '" . $today . "' THEN (orders.total_price - orders.delivery_price) ELSE 0 END) AS daily_sum"),
                DB::raw('SUM(CASE WHEN YEAR(histories.created_at) = YEAR(CURDATE()) AND MONTH(histories.created_at) = MONTH(CURDATE()) THEN (orders.total_price - orders.delivery_price) ELSE 0 END) AS monthly_sum'),
                DB::raw('SUM(CASE WHEN YEAR(histories.created_at) = YEAR(CURDATE()) THEN (orders.total_price - orders.delivery_price) ELSE 0 END) AS yearly_sum')
            )
            ->where('histories.status', 2);
            
        return $this->indexResponse($query
            ->groupBy('orders.partner_id', 'partners.name')
            ->paginate(\request()->get('limit', 20))
            ->toArray());
    }

    public function statPartnerOverall()
    {
        $today = now()->format('Y-m-d');

        return $this->indexResponse(History::query()
            ->join('orders', 'histories.order_id', '=', 'orders.id')
            ->join('partners', 'orders.partner_id', '=', 'partners.id')
            ->select(
                DB::raw('COUNT(CASE WHEN DATE(histories.created_at) = DATE(CURDATE()) THEN 1 END) AS daily_count'),
                DB::raw('COUNT(CASE WHEN YEAR(histories.created_at) = YEAR(CURDATE()) AND MONTH(histories.created_at) = MONTH(CURDATE()) THEN 1 END) AS monthly_count'),
                DB::raw('COUNT(CASE WHEN YEAR(histories.created_at) = YEAR(CURDATE()) THEN 1 END) AS yearly_count'),
                DB::raw('SUM(CASE WHEN DATE(histories.created_at) = DATE(CURDATE()) THEN (orders.total_price - orders.delivery_price) ELSE 0 END) AS daily_sum'),
                DB::raw('SUM(CASE WHEN YEAR(histories.created_at) = YEAR(CURDATE()) AND MONTH(histories.created_at) = MONTH(CURDATE()) THEN (orders.total_price - orders.delivery_price) ELSE 0 END) AS monthly_sum'),
                DB::raw('SUM(CASE WHEN YEAR(histories.created_at) = YEAR(CURDATE()) THEN (orders.total_price - orders.delivery_price) ELSE 0 END) AS yearly_sum')
            )
            ->where('histories.status', 2)
            ->get()
            ->toArray());
    }

    public function getMyStats()
    {
        $partnerId = auth('sanctum')->user()->id;
        
        return $this->success([
            'stats' => History::query()
                ->join('orders', 'histories.order_id', '=', 'orders.id')
                ->where('orders.partner_id', $partnerId)
                ->select(
                    DB::raw('COUNT(CASE WHEN DATE(histories.created_at) = CURDATE() THEN 1 END) as today_orders'),
                    DB::raw('COUNT(CASE WHEN MONTH(histories.created_at) = MONTH(CURDATE()) AND YEAR(histories.created_at) = YEAR(CURDATE()) THEN 1 END) as month_orders'),
                    DB::raw('COUNT(CASE WHEN YEAR(histories.created_at) = YEAR(CURDATE()) THEN 1 END) as year_orders'),
                    DB::raw('SUM(CASE WHEN DATE(histories.created_at) = CURDATE() THEN (orders.total_price - orders.delivery_price) ELSE 0 END) as today_earnings'),
                    DB::raw('SUM(CASE WHEN MONTH(histories.created_at) = MONTH(CURDATE()) AND YEAR(histories.created_at) = YEAR(CURDATE()) THEN (orders.total_price - orders.delivery_price) ELSE 0 END) as month_earnings'),
                    DB::raw('SUM(CASE WHEN YEAR(histories.created_at) = YEAR(CURDATE()) THEN (orders.total_price - orders.delivery_price) ELSE 0 END) as year_earnings'),
                    DB::raw('AVG(CASE WHEN DATE(histories.created_at) = CURDATE() THEN (orders.total_price - orders.delivery_price) END) as today_average_order'),
                    DB::raw('AVG(CASE WHEN MONTH(histories.created_at) = MONTH(CURDATE()) AND YEAR(histories.created_at) = YEAR(CURDATE()) THEN (orders.total_price - orders.delivery_price) END) as month_average_order')
                )
                ->where('histories.status', 2)
                ->first()
        ]);
    }

    public function getMyDailyStats()
    {
        $partnerId = auth('sanctum')->user()->id;
        $date = request()->get('date', now()->format('Y-m-d')); 
        
        if (request()->has('date')) {
            try {
                $date = \Carbon\Carbon::createFromFormat('d.m.Y', request()->get('date'))->format('Y-m-d');
            } catch (\Exception $e) {
                return $this->error('Invalid date format. Use dd.mm.yyyy');
            }
        }
        
        return $this->success([
            'daily_stats' => History::query()
                ->join('orders', 'histories.order_id', '=', 'orders.id')
                ->where('orders.partner_id', $partnerId)
                ->where('histories.status', 2)
                ->whereDate('histories.created_at', $date)
                ->select(
                    DB::raw('DATE(histories.created_at) as date'),
                    DB::raw('COUNT(*) as total_orders'),
                    DB::raw('SUM(orders.total_price - orders.delivery_price) as total_earnings'),
                    DB::raw('AVG(orders.total_price - orders.delivery_price) as average_order_value')
                )
                ->first()
        ]);
    }

    public function getMyMonthlyStats()
    {
        try {
            $partnerId = auth('sanctum')->user()->id;
            $months = min(abs(intval(request()->get('months', 12))), 24);
            
            $stats = History::query()
                ->join('orders', 'histories.order_id', '=', 'orders.id')
                ->where('orders.partner_id', $partnerId)
                ->where('histories.status', 2)
                ->where('histories.created_at', '>=', now()->subMonths($months))
                ->select(
                    DB::raw('DATE_FORMAT(histories.created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as total_orders'),
                    DB::raw('SUM(orders.total_price - orders.delivery_price) as total_earnings'),
                    DB::raw('AVG(orders.total_price - orders.delivery_price) as average_order_value')
                )
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get();
                
            return $this->success(['monthly_stats' => $stats]);
        } catch (\Exception $e) {
            \Log::error('Monthly stats error: ' . $e->getMessage());
            return $this->error('Failed to fetch monthly statistics');
        }
    }

    public function getMyYearlyStats()
    {
        $partnerId = auth('sanctum')->user()->id;
        $currentYear = now()->year;
        $year = intval(request()->get('year', $currentYear));
        
        if ($year < 2023 || $year > $currentYear) {
            return $this->error('Invalid year specified');
        }
        
        return $this->success([
            'yearly_stats' => History::query()
                ->join('orders', 'histories.order_id', '=', 'orders.id')
                ->where('orders.partner_id', $partnerId)
                ->whereYear('histories.created_at', $year)
                ->select(
                    DB::raw('YEAR(histories.created_at) as year'),
                    DB::raw('COUNT(*) as total_orders'),
                    DB::raw('SUM(orders.total_price - orders.delivery_price) as total_earnings'),
                    DB::raw('AVG(orders.total_price - orders.delivery_price) as average_order_value')
                )
                ->where('histories.status', 2)
                ->first()
        ]);
    }
}