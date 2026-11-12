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
            $logoUrl = str_starts_with($rawLogo, 'http')
                ? $rawLogo
                : asset('storage/' . ltrim($rawLogo, '/'));
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
            'status'        => (bool) $this->status,
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
