<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SchoolResource extends JsonResource
{
    public function toArray($request)
    {
        $rawLogo = null;
        $logoVal = $this->resource->logo ?? $this->logo;
        if (is_string($logoVal)) {
            $rawLogo = trim($logoVal);
        } elseif (is_array($logoVal)) {
            $values = array_values($logoVal);
            $first = $values[0] ?? null;
            $rawLogo = is_string($first) ? trim($first) : null;
        }

        $logoUrl = null;

        if ($rawLogo && $rawLogo !== '' && $rawLogo !== '[]' && $rawLogo !== 'null' && $rawLogo !== '{}') {
            if (preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?/(storage/.*)$#', $rawLogo, $matches)) {
                $logoUrl = '/' . $matches[3];
            } elseif (str_starts_with($rawLogo, 'http://') || str_starts_with($rawLogo, 'https://')) {
                $logoUrl = $rawLogo;
            } else {
                $logoUrl = '/storage/' . ltrim($rawLogo, '/');
            }
        }

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'code'          => $this->code,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'address'       => $this->address,
            'city'          => $this->city,
            'state'         => $this->state,
            'country'       => $this->country,
            'postal_code'   => $this->postal_code,
            'logo'          => $logoUrl,
            'logo_path'     => $logoUrl ? $rawLogo : null,
            'login_background'      => $this->login_background_url,
            'login_background_path' => $this->login_background,
            'status'        => (bool) $this->status,
            'custom_domain'           => $this->custom_domain,
            'domain_status'           => $this->domain_status ?? 'none',
            'domain_verified_at'      => $this->domain_verified_at,
            'dns_cname_target'        => $this->dns_cname_target,
            'website_url'             => $this->website_url,
            'has_custom_domain'       => (bool) $this->has_custom_domain,
            'subscription_plan'       => $this->subscription_plan ?? 'starter',
            'subscription_plan_label' => \App\Services\SubscriptionPlanService::getPlanLabel($this->subscription_plan ?? 'starter'),
            'subscription_status'     => $this->subscription_status ?? 'active',
            'features'                => $this->getActiveFeatures(),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'deleted_at'    => $this->deleted_at,
        ];
    }
}
