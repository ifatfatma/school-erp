<?php

namespace App\Services;

use App\Models\School;

class SubscriptionPlanService
{
    /**
     * Feature mapping per subscription package
     */
    public const PLAN_FEATURES = [
        'starter' => [
            'students',
            'attendance',
            'teachers',
            'academics',
            'fees',
            'examinations',
            'portal',
            'online_classes',
        ],
        'basic' => [
            'students',
            'attendance',
            'teachers',
            'academics',
            'fees',
            'examinations',
            'portal',
            'online_classes',
        ],
        'growth' => [
            'students',
            'attendance',
            'teachers',
            'academics',
            'fees',
            'examinations',
            'portal',
            'online_classes',
            'website_cms',
            'online_admissions',
            'library',
            'transport',
            'hostel',
            'inventory',
            'payment_gateway',
        ],
        'professional' => [
            'students',
            'attendance',
            'teachers',
            'academics',
            'fees',
            'examinations',
            'portal',
            'online_classes',
            'website_cms',
            'online_admissions',
            'library',
            'transport',
            'hostel',
            'inventory',
            'payment_gateway',
        ],
        'enterprise' => [
            'students',
            'attendance',
            'teachers',
            'academics',
            'fees',
            'examinations',
            'portal',
            'online_classes',
            'website_cms',
            'online_admissions',
            'library',
            'transport',
            'hostel',
            'inventory',
            'payment_gateway',
            'custom_domain',
            'multi_branch',
            'api_access',
            'advanced_reports',
        ],
    ];

    /**
     * Route prefix to required feature key mapping for backend middleware protection
     */
    public const ROUTE_FEATURE_MAP = [
        'api/hostel' => 'hostel',
        'api/library' => 'library',
        'api/books' => 'library',
        'api/book-categories' => 'library',
        'api/book-copies' => 'library',
        'api/book-issues' => 'library',
        'api/book-returns' => 'library',
        'api/book-renewals' => 'library',
        'api/library-members' => 'library',
        'api/library-reports' => 'library',
        'api/library-fines' => 'library',
        'api/lost-damaged-books' => 'library',
        'api/fine-collections' => 'library',
        'api/publishers' => 'library',
        'api/authors' => 'library',
        'api/transport' => 'transport',
        'api/inventory' => 'inventory',
        'api/online-classes' => 'online_classes',
        'api/admin/website' => 'website_cms',
        'api/admin/admissions' => 'online_admissions',
    ];

    /**
     * Check if a school has access to a specific feature
     */
    public static function canAccessFeature(?School $school, string $feature): bool
    {
        if (!$school) {
            return false;
        }

        // Custom override features list on school
        if (is_array($school->subscription_features) && in_array($feature, $school->subscription_features, true)) {
            return true;
        }

        $plan = strtolower($school->subscription_plan ?? 'starter');
        $allowed = self::PLAN_FEATURES[$plan] ?? self::PLAN_FEATURES['starter'];

        return in_array($feature, $allowed, true);
    }

    /**
     * Get all active features for a school
     */
    public static function getSchoolFeatures(?School $school): array
    {
        if (!$school) {
            return [];
        }

        $plan = strtolower($school->subscription_plan ?? 'starter');
        $base = self::PLAN_FEATURES[$plan] ?? self::PLAN_FEATURES['starter'];

        if (is_array($school->subscription_features)) {
            return array_values(array_unique(array_merge($base, $school->subscription_features)));
        }

        return $base;
    }

    /**
     * Human-friendly plan name
     */
    public static function getPlanLabel(string $plan): string
    {
        $plan = strtolower($plan);
        return match ($plan) {
            'growth', 'professional' => 'Professional / Growth',
            'enterprise' => 'Enterprise / Multi-Branch',
            default => 'Starter / Basic',
        };
    }
}
