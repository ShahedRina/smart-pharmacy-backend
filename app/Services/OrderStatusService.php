<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderStatusService
{
    public function change(
        Order $order,
        string $newStatus,
        int $changedBy,
        ?string $note = null
    ): Order {

        $allowedTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['preparing', 'cancelled'],
            'preparing' => ['out_for_delivery', 'cancelled'],
            'out_for_delivery' => ['delivered'],
            'delivered' => [],
            'cancelled' => [],
        ];

        return DB::transaction(function () use (
            $order,
            $newStatus,
            $changedBy,
            $note,
            $allowedTransitions
        ) {

            $order = Order::where('id', $order->id)
                ->lockForUpdate()
                ->firstOrFail();

            $currentStatus = $order->status;

            // Record the initial pending status
            if (
                $currentStatus === 'pending' &&
                $newStatus === 'pending' &&
                !$order->statusHistory()->exists()
            ) {
                $order->statusHistory()->create([
                    'changed_by' => $changedBy,
                    'status' => $newStatus,
                    'note' => $note,
                    'changed_at' => now(),
                ]);

                return $order;
            }

            // Validate status transition
            if (
                !isset($allowedTransitions[$currentStatus]) ||
                !in_array(
                    $newStatus,
                    $allowedTransitions[$currentStatus],
                    true
                )
            ) {
                abort(409, 'Invalid order status transition.');
            }

            // Update order status
            $order->update([
                'status' => $newStatus,
            ]);

            // Save status history
            $order->statusHistory()->create([
                'changed_by' => $changedBy,
                'status' => $newStatus,
                'note' => $note,
                'changed_at' => now(),
            ]);

            return $order->fresh();
        });
    }
}
