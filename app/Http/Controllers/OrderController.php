<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Partner;

class OrderController extends Controller
{
    public function index(Partner $partner)
    {
        $query = Order::query();
        if (\request()->has('from') && \request()->has('to')) {
            $from = \request()->input('from');
            $to = \request()->input('to');
            $query = $query->whereRaw("date(updated_at)>='{$from}' and date(updated_at)<='{$to}'");
        }
        if($partner->id) {
            $query = $query->where('partner_id', $partner->id);
            $query = $query->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('payment_type', 0)
                        ->where('status', '<>', 0);
                })
                    ->orWhere('payment_type', 1);
            });
        }
        if(\request()->has('partner_id')) {
            $query = $query->where('partner_id', \request()->input('partner_id'));
        }
        if(\request()->has('delivery_id')) {
            $query = $query->where('driver_id', \request()->input('delivery_id'));
        }
        if(\request()->has('customer_id')) {
            $query = $query->where('customer_id', \request()->input('customer_id'));
        }
        $orders = $query->orderBy('created_at')
            ->with('items')
            ->with('customer')
            ->with('items.product')
            ->with('partner')
            ->with('driver')
            ->paginate(\request('limit', 20));

        return $this->success([
            'orders' => $orders
        ]);
    }

    public function getOrdersByStatus(Request $request, $customer = null)
    {
        if ($customer != null) return $this->success([
            'orders' => Order::query()->with('customer')
                ->with('partner')
                ->where('customer_id', $customer)
                ->whereIn('status', [-1, 4, 2])
                ->orderByDesc('id')->get()
        ]);

        return $this->success([
            'orders' => Order::with('customer')
                ->where('status', $request->status)
                ->orderByDesc('id')->get()
        ]);
    }

    public function getDeliveryOrders($customer)
    {
        return $this->success([
            'orders' => Order::query()->where('customer_id', $customer)
                ->with('customer')
                ->with('partner')
                ->with('driver')
                ->orderBy('id')
                ->whereIn('status', [0, 1, 2, 3, 10, 31])->get()
        ]);
    }

    public function getOrderItems($order)
    {
        return $this->success([
            'items' => OrderItem::query()
                ->whereHas('product')
                ->with('product')
                ->with('order')
                ->with('order.customer')
                ->with('order.partner')
                ->where('order_id', $order)->get()
        ]);
    }

    public function store(OrderRequest $request)
    {
        $data = $request->validated();
        $data['total_price'] += $data['delivery_price'];
        $order = Order::query()->create($data);
        foreach ($data['order_items'] as $item) {
            if($item['quantity'] > 0 && $item['quantity'] <= Product::query()->where('id', $item['product_id'])->first()->quantity) {
                $inserted=DB::table('order_items')->insert([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
                if($inserted) {
                    $product = Product::query()->where('id', $item['product_id'])->first();
                    $product->quantity -= $item['quantity'];
                    if($product->quantity==0){
                        $product->status = 0;
                    }
                    $product->save();
                }
            }

        }
        return $this->success([
            'order' => $order,
            'payme' => $data['payment_type'] == 0 ? "https://checkout.paycom.uz/" . base64_encode("m=6708c357e64d929b0e41a59b;ac.order_id=" . $order->id . ";a=" . ($data['total_price'] * 100)) : ''
        ]);
    }

    public function show(Order $order)
    {
        return $this->success(['order' => $order]);
    }

    public function changeOrderStatus(Request $request, Order $order)
    {
        if ($request->status) {
            $history = new History();
            $history->order_id = $order->id;
            $history->status = $request->status;
            $history->save();
            $order->update(['status' => $request->status]);
            if($request->status=='-1'){
                $items=OrderItem::query()->where(['order_id'=>$order->id])->get()->toArray();
                foreach($items as $item){
                    $product=Product::query()->where('id',$item['product_id'])->first();
                    $product->quantity += $item['quantity'];
                    if($product->quantity>0){
                        $product->status = 1;
                    }
                    $product->save();
                }
            }
            return $this->success($order);
        }

        return $this->fail([], 'Invalid status');
    }

    public function changeItemStatus(Request $request, OrderItem $item)
    {
        if ($request->status) {
            $item->update(['status' => $request->status]);
            return $this->success($item);
        }

        return $this->fail([], 'Invalid status');
    }
    public function getPendingOrders(Partner $partner)
    {
        return $this->success([
            'orders' => Order::query()
                ->where('partner_id', $partner->id)
                ->whereNotIn('status', [-1, 4]) // Exclude cancelled (-1) and delivered (4) orders
                ->with(['customer', 'items.product', 'driver'])
                ->orderByDesc('created_at')
                ->get()
        ]);
    }

    public function cancelOrder(Order $order)
    {
        // Check if order can be cancelled (not delivered or already cancelled)
        if (in_array($order->status, [-1, 4])) {
            return $this->fail([], 'Order cannot be cancelled');
        }

        DB::transaction(function () use ($order) {
            // Restore product quantities
            foreach ($order->items as $item) {
                $product = Product::query()->where('id', $item->product_id)->first();
                $product->quantity += $item->quantity;
                if ($product->quantity > 0) {
                    $product->status = 1;
                }
                $product->save();
            }

            // Create history record
            History::create([
                'order_id' => $order->id,
                'status' => -1
            ]);

            // Update order status
            $order->update(['status' => -1]);
        });

        return $this->success(['order' => $order]);
    }
}