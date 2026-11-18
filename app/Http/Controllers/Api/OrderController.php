<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Medicine;
use App\Models\Address;
use App\Models\FamilyMember;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * ORD-2, ORD-3, ORD-4
     * Create a new order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'address_id' => ['required', 'exists:addresses,id'],

            'family_member_id' => [
                'nullable',
                'exists:family_members,id'
            ],

            'prescription_id' => [
                'nullable',
                'exists:prescriptions,id'
            ],

            'items' => ['required', 'array', 'min:1'],

            'items.*.medicine_id' => [
                'required',
                'integer',
                'distinct',
                'exists:medicines,id'
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1'
            ],
        ]);

        $user = $request->user();

        // Check address ownership
        $address = Address::where('id', $validated['address_id'])
            ->where('account_id', $user->id)
            ->first();

        if (!$address) {
            throw ValidationException::withMessages([
                'address_id' => ['The selected address does not belong to you.']
            ]);
        }

        // Check family member ownership
        $familyMember = null;

        if (!empty($validated['family_member_id'])) {
            $familyMember = FamilyMember::where('id', $validated['family_member_id'])
                ->where('caregiver_id', $user->id)
                ->first();

            if (!$familyMember) {
                throw ValidationException::withMessages([
                    'family_member_id' => ['Invalid family member.']
                ]);
            }
        }

        $order = DB::transaction(function () use (
            $validated,
            $user,
            $familyMember
        ) {
            $items = [];
            $totalAmount = 0;
            $requiresPrescription = false;

            // Lock medicines and check stock
            foreach ($validated['items'] as $item) {

                $medicine = Medicine::where('id', $item['medicine_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$medicine) {
                    throw ValidationException::withMessages([
                        'medicine_id' => ['Medicine not found.']
                    ]);
                }

                if ($medicine->stock_quantity < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'quantity' => [
                            "Insufficient stock for {$medicine->name}."
                        ]
                    ]);
                }

                if ($medicine->requires_prescription) {
                    $requiresPrescription = true;
                }

                $unitPrice = $medicine->price;

                $subtotal = $unitPrice * $item['quantity'];

                $totalAmount += $subtotal;

                $items[] = [
                    'medicine' => $medicine,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                ];
            }

            // Check approved prescription
            if ($requiresPrescription) {

                if (empty($validated['prescription_id'])) {
                    throw ValidationException::withMessages([
                        'prescription_id' => [
                            'An approved prescription is required.'
                        ]
                    ]);
                }

                $prescription = Prescription::where(
                    'id',
                    $validated['prescription_id']
                )
                    ->where('customer_id', $user->id)
                    ->where('status', 'approved')
                    ->first();

                if (!$prescription) {
                    throw ValidationException::withMessages([
                        'prescription_id' => [
                            'The prescription must be approved and belong to you.'
                        ]
                    ]);
                }
            }

            // Create order
            $order = Order::create([
                'customer_id' => $user->id,
                'family_member_id' => $familyMember?->id,
                'address_id' => $validated['address_id'],
                'prescription_id' => $validated['prescription_id'] ?? null,
                'order_date' => now(),
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'recipient_name' => $familyMember
                    ? $familyMember->full_name
                    : $user->name,
            ]);

            // Create order details and deduct stock
            foreach ($items as $item) {

                OrderDetail::create([
                    'order_id' => $order->id,
                    'medicine_id' => $item['medicine']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);

                $item['medicine']->decrement(
                    'stock_quantity',
                    $item['quantity']
                );
            }

            return $order;
        });

        return response()->json([
            'message' => 'Order created successfully.',
            'data' => $order->load('orderDetails.medicine', 'address'),
        ], 201);
    }

    /**
     * ORD-5
     * Get all orders belonging to authenticated user
     */
    public function index(Request $request)
    {
        $orders = Order::where('customer_id', $request->user()->id)
            ->with('orderDetails.medicine', 'address')
            ->latest('id')
            ->paginate(10);

        return response()->json([
            'data' => $orders,
        ], 200);
    }

    /**
     * ORD-5
     * Show one order
     */
    public function show(Request $request, $id)
    {
        $order = Order::with(
            'orderDetails.medicine',
            'address',
            'familyMember'
        )->findOrFail($id);

        if ($order->customer_id !== $request->user()->id) {
            return response()->json([
                'message' => 'You are not allowed to view this order.'
            ], 403);
        }

        return response()->json([
            'data' => $order,
        ], 200);
    }

    /**
     * ORD-6
     * Cancel pending order and restore stock
     */
    public function cancel(Request $request, $id)
    {
        $user = $request->user();

        $order = DB::transaction(function () use ($id, $user) {

            $order = Order::where('id', $id)
                ->where('customer_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($order->status !== 'pending') {
                return null;
            }

            $details = OrderDetail::where('order_id', $order->id)
                ->get();

            foreach ($details as $detail) {

                $medicine = Medicine::where('id', $detail->medicine_id)
                    ->lockForUpdate()
                    ->first();

                if ($medicine) {
                    $medicine->increment(
                        'stock_quantity',
                        $detail->quantity
                    );
                }
            }

            $order->update([
                'status' => 'cancelled',
            ]);

            return $order;
        });

        if (!$order) {
            return response()->json([
                'message' => 'Only pending orders can be cancelled.'
            ], 409);
        }

        return response()->json([
            'message' => 'Order cancelled and stock restored successfully.',
            'data' => $order->load('orderDetails.medicine'),
        ], 200);
    }
}
