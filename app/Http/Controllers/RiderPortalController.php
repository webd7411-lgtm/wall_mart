<?php

namespace App\Http\Controllers;

use App\Models\Rider;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RiderPortalController extends Controller
{
    /**
     * Check if current user is Super Admin or authorized restaurant management staff
     */
    protected function isSuperAdminOrStaff($user = null)
    {
        $user = $user ?: Auth::user();
        if (!$user) return false;

        if ($user->email === 'admin@admin.com') return true;

        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('Super Admin') || $user->hasRole('Admin') || $user->hasRole('Manager')) {
                return true;
            }
        }

        if (method_exists($user, 'getAssignedRoleNames')) {
            $roles = $user->getAssignedRoleNames();
            if (in_array('Super Admin', $roles) || in_array('Admin', $roles) || in_array('Manager', $roles)) {
                return true;
            }
        }

        if (method_exists($user, 'can')) {
            if ($user->can('restaurant.riders.view') || $user->can('restaurant.deliveries.view') || $user->can('sales.create')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Show Rider Dedicated Login Screen
     * If Super Admin is already logged in, redirect directly to portal without logging out!
     */
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Super Admin or admin staff goes straight to portal
            if ($this->isSuperAdminOrStaff($user)) {
                return redirect()->route('rider.portal');
            }

            $rider = Rider::withoutGlobalScope(\App\Scopes\BranchScope::class)
                ->where('user_id', $user->id)
                ->first();

            if (!$rider) {
                $cleanDigits = preg_replace('/[^0-9]/', '', $user->email ?: '');
                if (strlen($cleanDigits) >= 7) {
                    $rider = Rider::withoutGlobalScope(\App\Scopes\BranchScope::class)
                        ->where('phone', 'like', "%{$cleanDigits}%")
                        ->first();
                }
            }

            if ($rider) {
                return redirect()->route('rider.portal');
            }
        }

        return view('rider_portal.login');
    }

    /**
     * Handle Rider Login by Phone or Email + Password
     */
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $rawLogin = trim($request->input('login'));
        $password = $request->input('password');

        // Clean digits for phone matching
        $cleanDigits = preg_replace('/[^0-9]/', '', $rawLogin);

        // Find matching rider first
        $rider = Rider::withoutGlobalScope(\App\Scopes\BranchScope::class)
            ->where('is_active', true)
            ->where(function ($q) use ($rawLogin, $cleanDigits) {
                $q->where('phone', $rawLogin);
                if (strlen($cleanDigits) >= 7) {
                    $q->orWhere('phone', 'like', "%{$cleanDigits}%");
                }
            })->first();

        $user = null;
        if ($rider && $rider->user_id) {
            $user = User::find($rider->user_id);
        }

        if (!$user) {
            // Try by email or name directly
            $user = User::where('email', $rawLogin)
                ->orWhere('name', $rawLogin)
                ->first();
        }

        if (!$user && $rider) {
            // Check if rider has a user by rider phone
            $user = User::where('email', 'like', "%{$cleanDigits}%")->first();
        }

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $request->has('remember'));
            $request->session()->regenerate();

            // Link rider if not already linked
            if ($rider && empty($rider->user_id)) {
                $rider->user_id = $user->id;
                $rider->save();
            }

            return redirect()->route('rider.portal')->with('success', 'Khush Amdeed! Rider Portal me welcome.');
        }

        return redirect()->back()->withErrors([
            'login' => 'Phone number ya Password ghalat hai. Barah-e-karam dobara koshish karein.',
        ])->withInput();
    }

    /**
     * Get the authenticated rider instance safely
     */
    protected function getAuthenticatedRider()
    {
        $user = Auth::user();
        if (!$user) return null;

        $rider = Rider::withoutGlobalScope(\App\Scopes\BranchScope::class)
            ->where('user_id', $user->id)
            ->first();

        if (!$rider) {
            $cleanDigits = preg_replace('/[^0-9]/', '', $user->email ?: '');
            if (strlen($cleanDigits) >= 7) {
                $rider = Rider::withoutGlobalScope(\App\Scopes\BranchScope::class)
                    ->where('phone', 'like', "%{$cleanDigits}%")
                    ->first();
                if ($rider) {
                    $rider->user_id = $user->id;
                    $rider->save();
                }
            }
        }

        return $rider;
    }

    /**
     * Display Rider Mobile Portal Dashboard
     * Supports Super Admin view with multi-rider dropdown without requiring rider login!
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $isAdminView = $this->isSuperAdminOrStaff($user);
        $allRiders = collect();
        $rider = null;

        if ($isAdminView) {
            $activeBranchId = \App\Helpers\BranchHelper::getActiveBranchId() ?? session('branch_id');

            // Fetch all active riders for the dropdown
            $allRiders = Rider::withoutGlobalScope(\App\Scopes\BranchScope::class)
                ->where('is_active', true)
                ->when($activeBranchId, function($q) use ($activeBranchId) {
                    $q->where(function($sub) use ($activeBranchId) {
                        $sub->where('branch_id', $activeBranchId)
                            ->orWhereNull('branch_id');
                    });
                })
                ->orderBy('name')
                ->get();

            if ($allRiders->isEmpty()) {
                $allRiders = Rider::withoutGlobalScope(\App\Scopes\BranchScope::class)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get();
            }

            // Check if specific rider requested in URL query or session
            $requestedRiderId = $request->query('rider_id') ?? session('rider_portal_selected_rider_id');
            if ($requestedRiderId) {
                $rider = Rider::withoutGlobalScope(\App\Scopes\BranchScope::class)->find($requestedRiderId);
            }

            // Default to first rider if requested not found
            if (!$rider && $allRiders->isNotEmpty()) {
                $rider = $allRiders->first();
            }

            if ($rider) {
                session(['rider_portal_selected_rider_id' => $rider->id]);
            }
        } else {
            $rider = $this->getAuthenticatedRider();
        }

        if (!$rider) {
            if ($isAdminView) {
                return redirect()->route('restaurant.riders.index')
                    ->with('error', 'Koi active rider moujood nahi hai. Barah-e-karam pehle ek rider add karein.');
            }
            return redirect()->route('home')->with('error', 'Aapka account Rider profile se linked nahi hai.');
        }

        // Active Deliveries (Pending assignment or out on road)
        $activeOrders = Sale::withoutGlobalScope(\App\Scopes\BranchScope::class)
            ->with(['items.product', 'customer_relation'])
            ->where('rider_id', $rider->id)
            ->whereNotIn('sale_status', ['cancelled'])
            ->whereIn('delivery_status', ['pending', 'dispatched'])
            ->orderBy('id', 'desc')
            ->get();

        // Completed deliveries today
        $completedToday = Sale::withoutGlobalScope(\App\Scopes\BranchScope::class)
            ->with(['items.product', 'customer_relation'])
            ->where('rider_id', $rider->id)
            ->whereNotIn('sale_status', ['cancelled'])
            ->where('delivery_status', 'delivered')
            ->whereDate('updated_at', today())
            ->orderBy('updated_at', 'desc')
            ->get();

        // Cash in hand (Delivered orders whose COD cash is not yet settled with cashier)
        $cashInHand = (float) Sale::withoutGlobalScope(\App\Scopes\BranchScope::class)
            ->where('rider_id', $rider->id)
            ->whereNotIn('sale_status', ['cancelled'])
            ->where('delivery_status', 'delivered')
            ->where(function($q) {
                $q->whereNull('rider_cash_settled')->orWhere('rider_cash_settled', false);
            })
            ->sum(DB::raw('COALESCE(total_net, total_bill_amount)'));

        return view('rider_portal.dashboard', compact(
            'rider',
            'activeOrders',
            'completedToday',
            'cashInHand',
            'isAdminView',
            'allRiders'
        ));
    }

    /**
     * Live Polling API for Real-Time Order Updates on Rider Screen
     */
    public function liveOrdersApi(Request $request)
    {
        $user = Auth::user();
        $isAdminView = $this->isSuperAdminOrStaff($user);

        $rider = null;
        if ($isAdminView) {
            $requestedRiderId = $request->query('rider_id') ?? $request->input('rider_id') ?? session('rider_portal_selected_rider_id');
            if ($requestedRiderId) {
                $rider = Rider::withoutGlobalScope(\App\Scopes\BranchScope::class)->find($requestedRiderId);
            }
            if (!$rider) {
                $rider = Rider::withoutGlobalScope(\App\Scopes\BranchScope::class)->where('is_active', true)->first();
            }
        } else {
            $rider = $this->getAuthenticatedRider();
        }

        if (!$rider) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $activeOrders = Sale::withoutGlobalScope(\App\Scopes\BranchScope::class)
            ->with(['items.product', 'customer_relation'])
            ->where('rider_id', $rider->id)
            ->whereNotIn('sale_status', ['cancelled'])
            ->whereIn('delivery_status', ['pending', 'dispatched'])
            ->orderBy('id', 'desc')
            ->get();

        $formattedOrders = $activeOrders->map(function ($s) {
            $phone = $s->rider_phone;
            if (empty($phone) && $s->customer_relation) {
                $phone = $s->customer_relation->phone_number ?: ($s->customer_relation->mobile ?: '');
            }

            $address = $s->delivery_address;
            if (empty($address) && $s->customer_relation) {
                $address = $s->customer_relation->address ?? '';
            }

            $itemsSummary = $s->items->map(function ($it) {
                $qty = (int) ($it->total_pieces > 0 ? $it->total_pieces : ($it->qty > 0 ? $it->qty : 1));
                $name = $it->product_name ?? optional($it->product)->item_name ?? 'Item';
                return "{$qty}x {$name}";
            })->values()->all();

            $elapsedMins = $s->created_at ? round(now()->diffInMinutes($s->created_at)) : 0;

            return [
                'id'               => $s->id,
                'invoice_no'       => $s->invoice_no,
                'customer_name'    => optional($s->customer_relation)->name ?: ($s->walkin_name ?: 'Customer'),
                'customer_phone'   => $phone ?: '',
                'clean_phone'      => preg_replace('/[^0-9]/', '', $phone ?: ''),
                'delivery_address' => $address ?: 'No address specified',
                'delivery_charges' => (float) ($s->delivery_charges ?? 0),
                'total_bill'       => (float) ($s->total_net ?: $s->total_bill_amount),
                'order_notes'      => $s->order_notes ?: '',
                'delivery_status'  => $s->delivery_status ?: 'pending',
                'elapsed_mins'     => $elapsedMins,
                'items_summary'    => $itemsSummary,
                'created_time'     => $s->created_at ? $s->created_at->format('h:i A') : '',
            ];
        });

        $cashInHand = (float) Sale::withoutGlobalScope(\App\Scopes\BranchScope::class)
            ->where('rider_id', $rider->id)
            ->whereNotIn('sale_status', ['cancelled'])
            ->where('delivery_status', 'delivered')
            ->where(function($q) {
                $q->whereNull('rider_cash_settled')->orWhere('rider_cash_settled', false);
            })
            ->sum(DB::raw('COALESCE(total_net, total_bill_amount)'));

        $completedCount = Sale::withoutGlobalScope(\App\Scopes\BranchScope::class)
            ->where('rider_id', $rider->id)
            ->whereNotIn('sale_status', ['cancelled'])
            ->where('delivery_status', 'delivered')
            ->whereDate('updated_at', today())
            ->count();

        return response()->json([
            'success'          => true,
            'duty_status'      => $rider->duty_status,
            'active_count'     => $activeOrders->count(),
            'completed_count'  => $completedCount,
            'cash_in_hand'     => $cashInHand,
            'orders'           => $formattedOrders,
        ]);
    }

    /**
     * Rider updates order status to 'dispatched' (Picked Up / Out on Road)
     */
    public function markOutForDelivery($id, Request $request)
    {
        $user = Auth::user();
        $isAdminView = $this->isSuperAdminOrStaff($user);

        if ($isAdminView) {
            $sale = Sale::withoutGlobalScope(\App\Scopes\BranchScope::class)->where('id', $id)->first();
            if ($sale && $sale->rider_id) {
                $rider = Rider::withoutGlobalScope(\App\Scopes\BranchScope::class)->find($sale->rider_id);
            } else {
                $riderId = $request->input('rider_id') ?? session('rider_portal_selected_rider_id');
                $rider = Rider::withoutGlobalScope(\App\Scopes\BranchScope::class)->find($riderId);
            }
        } else {
            $rider = $this->getAuthenticatedRider();
            $sale = $rider ? Sale::withoutGlobalScope(\App\Scopes\BranchScope::class)->where('id', $id)->where('rider_id', $rider->id)->first() : null;
        }

        if (!$rider || !$sale) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $sale->delivery_status = 'dispatched';
        $sale->dispatched_at = now();
        $sale->save();

        $rider->duty_status = 'on_delivery';
        $rider->save();

        return response()->json([
            'success' => true,
            'message' => "Order #{$sale->invoice_no} marked as Out For Delivery!",
        ]);
    }

    /**
     * Rider marks order as Delivered and confirms Cash Collection
     */
    public function markDelivered($id, Request $request)
    {
        $user = Auth::user();
        $isAdminView = $this->isSuperAdminOrStaff($user);

        if ($isAdminView) {
            $sale = Sale::withoutGlobalScope(\App\Scopes\BranchScope::class)->where('id', $id)->first();
            if ($sale && $sale->rider_id) {
                $rider = Rider::withoutGlobalScope(\App\Scopes\BranchScope::class)->find($sale->rider_id);
            } else {
                $riderId = $request->input('rider_id') ?? session('rider_portal_selected_rider_id');
                $rider = Rider::withoutGlobalScope(\App\Scopes\BranchScope::class)->find($riderId);
            }
        } else {
            $rider = $this->getAuthenticatedRider();
            $sale = $rider ? Sale::withoutGlobalScope(\App\Scopes\BranchScope::class)->where('id', $id)->where('rider_id', $rider->id)->first() : null;
        }

        if (!$rider || !$sale) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $totalCollected = (float) ($sale->total_net ?: $sale->total_bill_amount);

        $sale->delivery_status = 'delivered';
        $sale->delivered_at = now();
        $sale->sale_status = 'posted';
        $sale->order_status = 'completed';
        $sale->is_booking = 0;
        if (empty($sale->cash) || $sale->cash <= 0) {
            $sale->cash = $totalCollected;
        }
        $sale->save();

        // Update linked Ecommerce Order if exists
        if (!empty($sale->ecommerce_order_id)) {
            DB::table('ecommerce_orders')
                ->where('id', $sale->ecommerce_order_id)
                ->update([
                    'order_status' => 'delivered',
                    'updated_at'   => now(),
                ]);
        }

        // Check if rider has any more active deliveries
        $remainingActive = Sale::withoutGlobalScope(\App\Scopes\BranchScope::class)
            ->where('rider_id', $rider->id)
            ->whereNotIn('sale_status', ['cancelled'])
            ->whereIn('delivery_status', ['pending', 'dispatched'])
            ->count();

        if ($remainingActive === 0) {
            $rider->duty_status = 'available';
            $rider->save();
        }

        return response()->json([
            'success' => true,
            'message' => "Order #{$sale->invoice_no} marked as Delivered successfully and Rs. " . number_format($totalCollected) . " recorded.",
        ]);
    }

    /**
     * Rider / Admin toggles Online / Offline Duty Status
     */
    public function updateDutyStatus(Request $request)
    {
        $user = Auth::user();
        $isAdminView = $this->isSuperAdminOrStaff($user);

        if ($isAdminView) {
            $riderId = $request->input('rider_id') ?? session('rider_portal_selected_rider_id');
            $rider = Rider::withoutGlobalScope(\App\Scopes\BranchScope::class)->find($riderId);
        } else {
            $rider = $this->getAuthenticatedRider();
        }

        if (!$rider) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $status = $request->input('duty_status');
        if (!in_array($status, ['available', 'off_duty'])) {
            return response()->json(['success' => false, 'message' => 'Invalid status'], 422);
        }

        // If rider has active deliveries, they cannot go off duty
        $hasActive = Sale::withoutGlobalScope(\App\Scopes\BranchScope::class)
            ->where('rider_id', $rider->id)
            ->whereNotIn('sale_status', ['cancelled'])
            ->whereIn('delivery_status', ['pending', 'dispatched'])
            ->exists();

        if ($hasActive && $status === 'off_duty') {
            return response()->json([
                'success' => false,
                'message' => 'Is rider ke pass active delivery orders pending hain. Pehle unhe deliver karein.',
            ], 400);
        }

        $rider->duty_status = $status;
        $rider->save();

        return response()->json([
            'success'     => true,
            'duty_status' => $rider->duty_status,
            'message'     => $status === 'available' ? 'Rider Online (Available) ho gaya hai.' : 'Rider Offline (Off Duty) ho gaya hai.',
        ]);
    }

    /**
     * Logout Rider
     * For Super Admin, does not destroy main session, redirects safely back to admin!
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($this->isSuperAdminOrStaff($user)) {
            return redirect()->route('restaurant.riders.index')->with('info', 'Rider Screen se exit kar lia gaya hai.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('rider.login')->with('success', 'Aap kamyabi se log out ho gaye.');
    }
}
