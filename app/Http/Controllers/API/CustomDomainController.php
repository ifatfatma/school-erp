<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Traits\ResolvesSchoolId;
use Illuminate\Http\Request;

class CustomDomainController extends Controller
{
    use ResolvesSchoolId;

    /**
     * Get Current School Custom Domain & DNS Status
     */
    public function show(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);
        $school = School::withoutGlobalScopes()->findOrFail($schoolId);

        $serverHost = parse_url(config('app.url'), PHP_URL_HOST) ?: $request->getHost();
        $serverIp = gethostbyname($serverHost);

        return response()->json([
            'success' => true,
            'data' => [
                'school_id' => $school->id,
                'school_name' => $school->name,
                'school_code' => $school->code,
                'default_url' => url('/s/' . $school->code),
                'custom_domain' => $school->custom_domain,
                'domain_status' => $school->domain_status ?? 'none',
                'domain_verified_at' => $school->domain_verified_at,
                'dns_cname_target' => $school->dns_cname_target ?: $serverHost,
                'server_ip' => $serverIp,
                'website_url' => $school->website_url,
                'has_custom_domain' => (bool) $school->has_custom_domain,
            ],
        ]);
    }

    /**
     * Configure / Update School Personal Domain
     */
    public function update(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);
        $school = School::withoutGlobalScopes()->findOrFail($schoolId);

        $request->validate([
            'domain' => 'required|string|max:191',
        ]);

        $rawDomain = trim($request->input('domain'));
        // Strip protocols and trailing slashes
        $cleanDomain = preg_replace('#^https?://#i', '', $rawDomain);
        $cleanDomain = preg_replace('#/.*$#', '', $cleanDomain);
        $cleanDomain = strtolower(trim($cleanDomain));

        // Validate domain format
        if (!preg_match('/^([a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/i', $cleanDomain)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid domain or subdomain name (e.g. pajschool.com or portal.stgeorge.edu)',
            ], 422);
        }

        // Prevent setting main platform domain as custom domain
        $serverHost = parse_url(config('app.url'), PHP_URL_HOST) ?: $request->getHost();
        if (in_array($cleanDomain, ['localhost', '127.0.0.1', strtolower($serverHost)])) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot use the primary platform domain as a custom school domain.',
            ], 422);
        }

        // Check uniqueness across other schools
        $exists = School::withoutGlobalScopes()
            ->where('id', '!=', $school->id)
            ->where('custom_domain', $cleanDomain)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => "The domain '{$cleanDomain}' is already assigned to another school institution.",
            ], 422);
        }

        $targetCname = parse_url(config('app.url'), PHP_URL_HOST) ?: $request->getHost();

        $school->update([
            'custom_domain' => $cleanDomain,
            'domain_status' => 'pending',
            'domain_verified_at' => null,
            'dns_cname_target' => $targetCname,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Personal domain '{$cleanDomain}' configured! Please configure your DNS CNAME record and click Verify.",
            'data' => [
                'custom_domain' => $school->custom_domain,
                'domain_status' => $school->domain_status,
                'dns_cname_target' => $school->dns_cname_target,
                'website_url' => $school->website_url,
            ],
        ]);
    }

    /**
     * Verify DNS Propagation for Custom Domain
     */
    public function verify(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);
        $school = School::withoutGlobalScopes()->findOrFail($schoolId);

        if (empty($school->custom_domain)) {
            return response()->json([
                'success' => false,
                'message' => 'No custom domain configured for this school.',
            ], 422);
        }

        $domain = $school->custom_domain;
        $targetHost = $school->dns_cname_target ?: (parse_url(config('app.url'), PHP_URL_HOST) ?: $request->getHost());

        // Perform DNS check
        $isResolved = false;
        $resolvedIp = null;
        $cnameMatches = false;

        // In local development environment, allow 1-click instant verification
        $isLocal = app()->environment('local') || str_contains($targetHost, 'localhost') || str_contains($targetHost, '127.0.0.1');

        if (!$isLocal) {
            // Check CNAME records
            $cnameRecords = @dns_get_record($domain, DNS_CNAME);
            if (!empty($cnameRecords)) {
                foreach ($cnameRecords as $rec) {
                    if (isset($rec['target']) && (strcasecmp($rec['target'], $targetHost) === 0 || str_contains($rec['target'], $targetHost))) {
                        $cnameMatches = true;
                        $isResolved = true;
                        break;
                    }
                }
            }

            // Check A records
            if (!$isResolved) {
                $aRecords = @dns_get_record($domain, DNS_A);
                if (!empty($aRecords)) {
                    $serverIp = gethostbyname($targetHost);
                    foreach ($aRecords as $rec) {
                        if (isset($rec['ip']) && $rec['ip'] === $serverIp) {
                            $isResolved = true;
                            break;
                        }
                    }
                }
            }
        } else {
            // Local mode: simulate instant verification
            $isResolved = true;
        }

        if ($isResolved) {
            $school->update([
                'domain_status' => 'active',
                'domain_verified_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'verified' => true,
                'message' => "Congratulations! Personal domain '{$domain}' is active and verified. Your school is now accessible directly at https://{$domain}",
                'data' => [
                    'custom_domain' => $school->custom_domain,
                    'domain_status' => 'active',
                    'domain_verified_at' => $school->domain_verified_at,
                    'website_url' => $school->website_url,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'verified' => false,
            'message' => "DNS records for '{$domain}' have not propagated yet. Please ensure your CNAME points to '{$targetHost}' and allow a few minutes for global DNS propagation.",
            'data' => [
                'custom_domain' => $school->custom_domain,
                'domain_status' => 'pending',
                'dns_cname_target' => $targetHost,
            ],
        ], 422);
    }

    /**
     * Remove / Revert Custom Domain back to standard /s/code URL
     */
    public function destroy(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);
        $school = School::withoutGlobalScopes()->findOrFail($schoolId);

        $oldDomain = $school->custom_domain;

        $school->update([
            'custom_domain' => null,
            'domain_status' => 'none',
            'domain_verified_at' => null,
            'dns_cname_target' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Personal domain removed. School has reverted to the default URL: " . url('/s/' . $school->code),
            'data' => [
                'default_url' => url('/s/' . $school->code),
                'custom_domain' => null,
                'domain_status' => 'none',
            ],
        ]);
    }
}
