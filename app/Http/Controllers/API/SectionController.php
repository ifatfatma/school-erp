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
        $sections = $this->service->paginate(
            $request->only([
                'search',
                'class_id'
            ]),
            $request->integer('per_page', 10)
        );

        return SectionResource::collection($sections);
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
}