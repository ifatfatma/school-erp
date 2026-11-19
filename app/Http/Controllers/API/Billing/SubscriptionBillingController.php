<?php

namespace App\Http\Controllers\API\Billing;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\Models\User;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionBillingController extends Controller
{
    protected TenantManager $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Get all available subscription plans.
     */
    public function plans(): JsonResponse
    {
        $plans = Plan::where('status', true)->orderBy('price')->get();

        return response()->json([
            'success' => true,
            'data' => $plans,
        ]);
    }

    /**
     * Get list of tenants and their subscription status (Super Admin only).
     */
    public function tenants(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$this->tenantManager->isSuperAdmin($user)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $schools = School::withoutGlobalScopes()->get();
        $plansBySlug = Plan::all()->keyBy('slug');
        $basicPlan = $plansBySlug->get('basic') ?: Plan::first();

        $data = $schools->map(function ($school) use ($plansBySlug, $basicPlan) {
            // Find school admin via school_user pivot or direct school_id
            $admin = User::withoutGlobalScopes()
                ->whereHas('schools', fn($q) => $q->where('schools.id', $school->id)->where('school_user.role', 'school_admin'))
                ->first();

            if (!$admin) {
                $admin = User::withoutGlobalScopes()
                    ->where('school_id', $school->id)
                    ->whereIn('user_type', ['admin', 'school_admin'])
                    ->first();
            }

            // Resolve Plan
            $plan = $admin?->subscription?->plan;
            if (!$plan) {
                $rawSlug = strtolower($school->subscription_plan ?? 'basic');
                if ($rawSlug === 'starter') $rawSlug = 'basic';
                if ($rawSlug === 'growth') $rawSlug = 'professional';
                $plan = $plansBySlug->get($rawSlug) ?: $basicPlan;
            }

            $planSlug = $plan?->slug ?? 'basic';
            $planName = $plan?->name ?? 'Starter / Basic Plan';
            $planPrice = (float) ($plan?->price ?? 2499.00);
            $maxSchools = $admin ? $admin->maxAllowedSchools() : ($plan?->max_schools ?? 1);
            $ownedCount = $admin ? $admin->ownedSchools()->count() : 1;
            if ($ownedCount === 0) $ownedCount = 1;

            // Pending invoice
            $pendingInvoice = null;
            if ($admin) {
                $pendingInvoice = Invoice::where('user_id', $admin->id)
                    ->where('status', 'pending')
                    ->with(['currentPlan', 'targetPlan'])
                    ->latest()
                    ->first();
            }
            if (!$pendingInvoice) {
                $pendingInvoice = Invoice::where('school_id', $school->id)
                    ->where('status', 'pending')
                    ->with(['currentPlan', 'targetPlan'])
                    ->latest()
                    ->first();
            }

            return [
                'user_id' => $admin?->id,
                'name' => $admin?->name ?? 'Platform Managed',
                'email' => $admin?->email ?? ($school->email ?? 'N/A'),
                'phone' => $admin?->phone ?? $school->phone,
                'school_id' => $school->id,
                'school_name' => $school->name,
                'school_code' => $school->code,
                'school_logo' => $school->logo_url,
                'plan_name' => $planName,
                'plan_slug' => $planSlug,
                'plan_price' => $planPrice,
                'max_schools' => $maxSchools,
                'owned_schools_count' => $ownedCount,
                'can_add_more' => $admin ? $admin->canAddMoreSchools() : false,
                'pending_invoice' => $pendingInvoice,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Calculate prorated difference amount for an upgrade (Super Admin).
     */
    public function calculateUpgrade(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$this->tenantManager->isSuperAdmin($user)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'school_id' => 'nullable|exists:schools,id',
            'target_plan_id' => 'required|exists:plans,id',
        ]);

        $targetUser = null;
        if ($request->filled('user_id')) {
            $targetUser = User::withoutGlobalScopes()->with('subscription.plan', 'school')->find($request->user_id);
        }

        $school = null;
        if ($request->filled('school_id')) {
            $school = School::withoutGlobalScopes()->find($request->school_id);
        }

        if (!$targetUser && $school) {
            $targetUser = User::withoutGlobalScopes()
                ->whereHas('schools', fn($q) => $q->where('schools.id', $school->id)->where('school_user.role', 'school_admin'))
                ->first()
                ?: User::withoutGlobalScopes()->where('school_id', $school->id)->whereIn('user_type', ['admin', 'school_admin'])->first();
        }

        if (!$school && $targetUser) {
            $school = $targetUser->school;
        }

        $targetPlan = Plan::findOrFail($request->target_plan_id);

        $currentPlan = $targetUser?->subscription?->plan;
        if (!$currentPlan) {
            $planSlug = strtolower($school?->subscription_plan ?? 'basic');
            if ($planSlug === 'starter') $planSlug = 'basic';
            if ($planSlug === 'growth') $planSlug = 'professional';
            $currentPlan = Plan::where('slug', $planSlug)->first() ?: Plan::where('slug', 'basic')->first();
        }

        $currentPrice = (float) ($currentPlan->price ?? 2499.00);
        $targetPrice = (float) $targetPlan->price;

        if ($targetPlan->id === $currentPlan->id) {
            $creditAmount = 0.00;
            $netPayable = $targetPrice;
        } else {
            $creditAmount = $currentPrice;
            $netPayable = max(0, $targetPrice - $creditAmount);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $targetUser?->id,
                    'name' => $targetUser?->name ?? 'Platform Managed',
                    'email' => $targetUser?->email ?? ($school?->email ?? 'N/A'),
                    'school_name' => $school?->name ?? ($targetUser?->school?->name ?? 'N/A'),
                ],
                'current_plan' => [
                    'id' => $currentPlan->id,
                    'name' => $currentPlan->name,
                    'slug' => $currentPlan->slug,
                    'price' => $currentPrice,
                    'max_schools' => $currentPlan->max_schools,
                ],
                'target_plan' => [
                    'id' => $targetPlan->id,
                    'name' => $targetPlan->name,
                    'slug' => $targetPlan->slug,
                    'price' => $targetPrice,
                    'max_schools' => $targetPlan->max_schools,
                ],
                'credit_amount' => $creditAmount,
                'net_amount' => $netPayable,
            ],
        ]);
    }

    /**
     * Generate upgrade invoice for a School Admin (Super Admin).
     */
    public function generateUpgradeInvoice(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$this->tenantManager->isSuperAdmin($user)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'school_id' => 'nullable|exists:schools,id',
            'target_plan_id' => 'required|exists:plans,id',
            'custom_net_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $targetUser = null;
        if ($request->filled('user_id')) {
            $targetUser = User::withoutGlobalScopes()->with('subscription.plan', 'school')->find($request->user_id);
        }

        $school = null;
        if ($request->filled('school_id')) {
            $school = School::withoutGlobalScopes()->find($request->school_id);
        }

        if (!$targetUser && $school) {
            $targetUser = User::withoutGlobalScopes()
                ->whereHas('schools', fn($q) => $q->where('schools.id', $school->id)->where('school_user.role', 'school_admin'))
                ->first()
                ?: User::withoutGlobalScopes()->where('school_id', $school->id)->whereIn('user_type', ['admin', 'school_admin'])->first();
        }

        if (!$targetUser) {
            $targetUser = $user;
        }

        if (!$school) {
            $school = $targetUser->school;
        }

        $targetPlan = Plan::findOrFail($request->target_plan_id);

        $currentPlan = $targetUser->subscription?->plan;
        if (!$currentPlan) {
            $planSlug = strtolower($school?->subscription_plan ?? 'basic');
            if ($planSlug === 'starter') $planSlug = 'basic';
            if ($planSlug === 'growth') $planSlug = 'professional';
            $currentPlan = Plan::where('slug', $planSlug)->first() ?: Plan::where('slug', 'basic')->first();
        }

        $currentPrice = (float) ($currentPlan->price ?? 2499.00);
        $targetPrice = (float) $targetPlan->price;

        if ($targetPlan->id === $currentPlan->id) {
            $creditAmount = 0.00;
            $calculatedNet = $targetPrice;
            $invoiceType = 'plan_renewal';
        } else {
            $creditAmount = $currentPrice;
            $calculatedNet = max(0, $targetPrice - $creditAmount);
            $invoiceType = 'plan_upgrade';
        }

        $netAmount = $request->filled('custom_net_amount')
            ? (float) $request->custom_net_amount
            : $calculatedNet;

        // Cancel any existing pending invoice for this user / school to prevent duplicates
        Invoice::where(function ($q) use ($targetUser, $school) {
            $q->where('user_id', $targetUser->id);
            if ($school) {
                $q->orWhere('school_id', $school->id);
            }
        })
        ->where('status', 'pending')
        ->update(['status' => 'cancelled']);

        $invoiceNumber = ($invoiceType === 'plan_renewal' ? 'INV-REN-' : 'INV-UPG-') . date('Ymd') . '-' . strtoupper(Str::random(5));

        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'user_id' => $targetUser->id,
            'school_id' => $school?->id ?? $targetUser->school_id,
            'current_plan_id' => $currentPlan->id,
            'target_plan_id' => $targetPlan->id,
            'type' => $invoiceType,
            'current_plan_price' => $currentPrice,
            'target_plan_price' => $targetPrice,
            'credit_amount' => $creditAmount,
            'net_amount' => $netAmount,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Invoice {$invoiceNumber} generated successfully for " . ($school?->name ?? 'Institution') . " ({$targetUser->name}).",
            'data' => $invoice->load(['currentPlan', 'targetPlan', 'user', 'school']),
        ], 201);
    }

    /**
     * Get all invoices (Super Admin).
     */
    public function allInvoices(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$this->tenantManager->isSuperAdmin($user)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $query = Invoice::with(['user', 'school', 'currentPlan', 'targetPlan'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $invoices->items(),
            'meta' => [
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
                'total' => $invoices->total(),
            ],
        ]);
    }

    /**
     * Get my billing details and pending invoices (School Admin).
     */
    public function mySubscription(Request $request): JsonResponse
    {
        $user = $request->user();
        $isSuperAdmin = $this->tenantManager->isSuperAdmin($user);

        if ($isSuperAdmin) {
            return response()->json([
                'success' => true,
                'data' => [
                    'is_super_admin' => true,
                ],
            ]);
        }

        $plan = $user->subscription?->plan;
        if (!$plan) {
            $planSlug = strtolower($user->school?->subscription_plan ?? 'basic');
            $plan = Plan::where('slug', $planSlug)->first() ?: Plan::where('slug', 'basic')->first();
        }

        $ownedSchools = $user->ownedSchools()->where('status', true)->get(['schools.id', 'schools.name', 'schools.code', 'schools.logo']);
        $maxSchools = $user->maxAllowedSchools();
        $canAddMore = $user->canAddMoreSchools();

        $pendingInvoice = Invoice::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with(['currentPlan', 'targetPlan'])
            ->latest()
            ->first();

        $invoices = Invoice::where('user_id', $user->id)
            ->with(['currentPlan', 'targetPlan'])
            ->latest()
            ->get();

        $allPlans = Plan::where('status', true)->orderBy('price')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'plan' => $plan,
                'is_super_admin' => $isSuperAdmin,
                'max_schools' => $maxSchools,
                'owned_schools_count' => $ownedSchools->count(),
                'can_add_more_schools' => $canAddMore,
                'owned_schools' => $ownedSchools,
                'pending_invoice' => $pendingInvoice,
                'invoices' => $invoices,
                'available_plans' => $allPlans,
            ],
        ]);
    }

    /**
     * School Admin requests a plan upgrade (or self-generates an upgrade invoice).
     */
    public function requestUpgrade(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'target_plan_id' => 'required|exists:plans,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $targetPlan = Plan::findOrFail($request->target_plan_id);

        $currentPlan = $user->subscription?->plan;
        if (!$currentPlan) {
            $planSlug = strtolower($user->school?->subscription_plan ?? 'basic');
            if ($planSlug === 'starter') $planSlug = 'basic';
            if ($planSlug === 'growth') $planSlug = 'professional';
            $currentPlan = Plan::where('slug', $planSlug)->first() ?: Plan::where('slug', 'basic')->first();
        }

        $currentPrice = (float) ($currentPlan->price ?? 2499.00);
        $targetPrice = (float) $targetPlan->price;

        if ($targetPlan->id === $currentPlan->id) {
            $creditAmount = 0.00;
            $netAmount = $targetPrice;
            $invoiceType = 'plan_renewal';
        } else {
            $creditAmount = $currentPrice;
            $netAmount = max(0, $targetPrice - $creditAmount);
            $invoiceType = 'plan_upgrade';
        }

        // Cancel previous pending invoices for this user to prevent duplicates
        Invoice::where('user_id', $user->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        $invoiceNumber = ($invoiceType === 'plan_renewal' ? 'INV-REN-' : 'INV-UPG-') . date('Ymd') . '-' . strtoupper(Str::random(5));

        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'user_id' => $user->id,
            'school_id' => $user->school_id,
            'current_plan_id' => $currentPlan->id,
            'target_plan_id' => $targetPlan->id,
            'type' => $invoiceType,
            'current_plan_price' => $currentPrice,
            'target_plan_price' => $targetPrice,
            'credit_amount' => $creditAmount,
            'net_amount' => $netAmount,
            'status' => 'pending',
            'notes' => $request->notes ?? "Self-service upgrade request by School Admin ({$user->name})",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Upgrade invoice {$invoiceNumber} generated! You can now proceed to pay and activate your plan.",
            'data' => $invoice->load(['currentPlan', 'targetPlan', 'user', 'school']),
        ], 201);
    }

    /**
     * Mark invoice as paid (Super Admin or School Admin Pay Now).
     */
    public function payInvoice(Request $request, int $id): JsonResponse
    {
        $currentUser = $request->user();
        $invoice = Invoice::with(['user', 'school', 'targetPlan'])->findOrFail($id);

        $isSuperAdmin = $this->tenantManager->isSuperAdmin($currentUser);

        // Security check: only Super Admin OR the invoice owner can pay
        if (!$isSuperAdmin && $currentUser->id !== $invoice->user_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized to pay this invoice.'], 403);
        }

        if ($invoice->status === 'paid') {
            return response()->json(['success' => false, 'message' => 'This invoice has already been paid.'], 400);
        }

        $method = $request->input('payment_method') ?? ($isSuperAdmin ? 'manual_approval' : 'online');
        $txnRef = $request->input('transaction_ref') ?? $request->input('ref_no') ?? null;
        $upiId = $request->input('upi_id') ?? null;

        $notes = $invoice->notes;
        if ($txnRef) {
            $notes = ($notes ? $notes . ' | ' : '') . "Txn Ref: {$txnRef}";
        }
        if ($upiId) {
            $notes = ($notes ? $notes . ' | ' : '') . "UPI VPA: {$upiId}";
        }

        $this->activateUpgrade($invoice, $method, $notes);

        return response()->json([
            'success' => true,
            'message' => "Invoice {$invoice->invoice_number} paid successfully. Plan upgraded to {$invoice->targetPlan->name}.",
            'data' => $invoice->fresh(['currentPlan', 'targetPlan', 'user', 'school']),
        ]);
    }

    /**
     * Activate the upgrade after invoice payment.
     */
    protected function activateUpgrade(Invoice $invoice, string $method, ?string $notes = null): void
    {
        $user = $invoice->user;
        $targetPlan = $invoice->targetPlan;
        $school = $user->school ?? $invoice->school;

        // 1. Update/create user's active subscription
        Subscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'school_id' => $school?->id,
                'plan_id' => $targetPlan->id,
                'status' => 'active',
                'max_schools' => $targetPlan->max_schools,
                'amount_paid' => $invoice->net_amount,
                'started_at' => now(),
                'expires_at' => now()->addYear(),
            ]
        );

        // 2. Update all schools owned by this user to the target plan
        $user->ownedSchools()->update([
            'subscription_plan' => $targetPlan->slug,
            'subscription_status' => 'active',
        ]);

        if ($school) {
            $school->update([
                'subscription_plan' => $targetPlan->slug,
                'subscription_status' => 'active',
            ]);
        }

        // 3. Automatically grant/revoke Spatie permissions based on new plan
        $user->syncSchoolPermissionsBasedOnPlan();

        // 4. Mark invoice paid
        $invoice->update([
            'status' => 'paid',
            'payment_method' => $method,
            'notes' => $notes ?? $invoice->notes,
            'paid_at' => now(),
        ]);
    }
}
