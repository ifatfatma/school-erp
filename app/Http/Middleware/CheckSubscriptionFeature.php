<?php

namespace App\Http\Middleware;

use App\Models\School;
use App\Services\SubscriptionPlanService;
use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionFeature
{
    protected TenantManager $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $requiredFeature = null): Response
    {
        $user = $request->user();

        // 1. If not authenticated or request is to public / root / logout endpoints, allow
        if (!$user) {
            return $next($request);
        }

        // 2. Super Admin has unrestricted access to all features across all schools
        if ($this->tenantManager->isSuperAdmin($user)) {
            return $next($request);
        }

        // 3. Determine required feature
        $feature = $requiredFeature;
        if (!$feature) {
            $path = ltrim($request->path(), '/');
            $cleanPath = str_starts_with($path, 'api/') ? substr($path, 4) : $path;

            foreach (SubscriptionPlanService::ROUTE_FEATURE_MAP as $routePrefix => $mappedFeature) {
                $cleanPrefix = str_starts_with($routePrefix, 'api/') ? substr($routePrefix, 4) : $routePrefix;
                if (
                    str_starts_with($path, $routePrefix) ||
                    str_starts_with($cleanPath, $cleanPrefix) ||
                    $request->is($routePrefix . '/*') ||
                    $request->is($cleanPrefix . '/*') ||
                    $request->is($routePrefix) ||
                    $request->is($cleanPrefix)
                ) {
                    $feature = $mappedFeature;
                    break;
                }
            }
        }

        // 4. If no restricted feature is matched, proceed
        if (!$feature) {
            return $next($request);
        }

        // 5. Resolve active school
        $schoolId = $this->tenantManager->getTenantId() ?? $user->school_id;
        $school = $schoolId ? School::find($schoolId) : null;

        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'No active school context found.',
            ], 403);
        }

        // 6. Check if school has permission for this feature
        if (!SubscriptionPlanService::canAccessFeature($school, $feature)) {
            $planLabel = SubscriptionPlanService::getPlanLabel($school->subscription_plan ?? 'starter');
            $featureLabel = ucwords(str_replace('_', ' ', $feature));

            return response()->json([
                'success' => false,
                'message' => "The [{$featureLabel}] feature is not included in your current [{$planLabel}] subscription package. Please upgrade your plan to unlock this module.",
                'upgrade_required' => true,
                'required_feature' => $feature,
                'current_plan' => $school->subscription_plan ?? 'starter',
            ], 403);
        }

        return $next($request);
    }
}
