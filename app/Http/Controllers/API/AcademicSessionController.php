<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicSession\StoreAcademicSessionRequest;
use App\Http\Requests\AcademicSession\UpdateAcademicSessionRequest;
use App\Http\Resources\AcademicSessionResource;
use App\Models\AcademicSession;
use App\Services\AcademicSessionService;
use Illuminate\Http\Request;

class AcademicSessionController extends Controller
{
    public function __construct(
        protected AcademicSessionService $service
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $isSuperAdmin = $user && (
            $user->user_type === 'super_admin' || 
            (method_exists($user, 'hasRole') && ($user->hasRole('Super Admin') || $user->hasRole('super_admin'))) ||
            ($user->roles && $user->roles->contains('name', 'Super Admin'))
        );

        $resource = AcademicSessionResource::collection(
            $this->service->list($request)
        );

        if ($isSuperAdmin) {
            $resource->additional([
                'all_schools' => \App\Models\School::where('status', true)->orderBy('name')->get(['id', 'name', 'code']),
            ]);
        }

        return $resource;
    }

    public function store(StoreAcademicSessionRequest $request)
    {
        $session = $this->service->store(
            $request->validated()
        );

        return new AcademicSessionResource($session);
    }

    public function show(AcademicSession $academicSession)
    {
        return new AcademicSessionResource($academicSession);
    }

    public function update(
        UpdateAcademicSessionRequest $request,
        AcademicSession $academicSession
    ) {
        $session = $this->service->update(
            $academicSession,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Academic session updated successfully.',
            'data' => new AcademicSessionResource($session)
        ]);
    }

    public function destroy(AcademicSession $academicSession)
    {
        $this->service->delete($academicSession);

        return response()->json([
            'success' => true,
            'message' => 'Academic session deleted successfully.'
        ]);
    }

        public function active()
{
    return response()->json([
        'success' => true,
        'data' => AcademicSession::where('status', 1)
            ->orderBy('name')
            ->get()
    ]);
}
}
