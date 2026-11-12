<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use App\Services\SectionService;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    protected SectionService $service;

    public function __construct(SectionService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing.
     */
    public function index(Request $request)
    {
        $authUser = $request->user();
        $isSuperAdmin = $authUser && (
            $authUser->user_type === 'super_admin' || 
            (method_exists($authUser, 'hasRole') && ($authUser->hasRole('Super Admin') || $authUser->hasRole('super_admin'))) ||
            ($authUser->roles && $authUser->roles->contains('name', 'Super Admin'))
        );

        $schoolId = $request->input('school_id');
        if ($isSuperAdmin && ($schoolId === null || $schoolId === '' || $schoolId === 'all')) {
            $schoolId = 'all';
        }

        $filters = $request->only([
            'search',
            'class_id',
            'status',
        ]);
        $filters['school_id'] = $schoolId;

        $sections = $this->service->paginate(
            $filters,
            $request->integer('per_page', 10)
        );

        $resource = SectionResource::collection($sections);

        if ($isSuperAdmin) {
            $resource->additional([
                'all_schools' => \App\Models\School::where('status', true)->orderBy('name')->get(['id', 'name', 'code']),
            ]);
        }

        return $resource;
    }

    /**
     * Store a newly created resource.
     */
    public function store(StoreSectionRequest $request)
    {
        $section = $this->service->store(
            $request->validated()
        );

        return new SectionResource($section->load('class'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Section $section)
    {
        return new SectionResource(
            $section->load('class')
        );
    }

    /**
     * Update the specified resource.
     */
    public function update(
        UpdateSectionRequest $request,
        Section $section
    ) {
        $section = $this->service->update(
            $section,
            $request->validated()
        );

        return new SectionResource($section);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(Section $section)
    {
        $this->service->delete($section);

        return response()->json([
            'success' => true,
            'message' => 'Section deleted successfully.'
        ]);
    }

    public function active()
{
    return response()->json([
        'success' => true,
        'data' => Section::where('status', 1)
            ->orderBy('name')
            ->get()
    ]);
}
}