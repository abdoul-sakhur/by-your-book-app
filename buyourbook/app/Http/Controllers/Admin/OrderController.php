<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Notifications\OrderStatusChangedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->when($request->search, fn ($q, $s) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->with(['user:id,name,email', 'relayPoint:id,name'])
            ->withCount('items')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statuses = OrderStatus::cases();

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function show(Order $order): View
    {
        $order->load([
            'user:id,name,email,phone',
            'relayPoint',
            'items.sellerBook.officialBook.subject',
            'items.sellerBook.seller:id,name',
        ]);

        $statuses = OrderStatus::cases();

        return view('admin.orders.show', compact('order', 'statuses'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $validated = $request->validated();

        $order->update(['status' => $validated['status']]);

        OrderEvent::create([
            'order_id' => $order->id,
            'status' => $validated['status'],
            'comment' => 'Statut mis à jour par l\'administration.',
        ]);

        $order->user->notify(new OrderStatusChangedNotification($order, $validated['status']));

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Statut mis à jour.');
    }
}
