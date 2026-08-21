<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function __construct(
        protected SettingService $service
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | All Settings
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->all(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Group
    |--------------------------------------------------------------------------
    */

    public function group(string $group)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getGroup($group),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Group Models
    |--------------------------------------------------------------------------
    */

    public function groupDetails(string $group)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getGroupModels($group),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Single Setting
    |--------------------------------------------------------------------------
    */

    public function show(
        string $group,
        string $key
    ) {
        $value = $this->service->get(
            $group,
            $key
        );

        return response()->json([
            'success' => true,
            'data' => [
                'group' => $group,
                'key' => $key,
                'value' => $value,
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Set Single Setting
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $request->validate([

            'group' => [
                'required',
                'string',
                'max:100',
            ],

            'key' => [
                'required',
                'string',
                'max:255',
            ],

            'value' => [
                'nullable',
            ],

            'type' => [
                'required',
                Rule::in([
                    'string',
                    'text',
                    'integer',
                    'decimal',
                    'boolean',
                    'json',
                ]),
            ],

            'description' => [
                'nullable',
                'string',
            ],

        ]);


        $setting = $this->service->set(
            $data['group'],
            $data['key'],
            $data['value'] ?? null,
            $data['type'],
            $data['description'] ?? null
        );


        return response()->json([
            'success' => true,
            'message' => 'Setting saved successfully.',
            'data' => $setting,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Save Group
    |--------------------------------------------------------------------------
    */

    public function saveGroup(
        Request $request,
        string $group
    ) {
        $data = $request->validate([
            'settings' => [
                'required',
                'array',
            ],
        ]);


        $settings = $this->service->saveGroup(
            $group,
            $data['settings']
        );


        return response()->json([
            'success' => true,
            'message' => 'Settings saved successfully.',
            'data' => $settings,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Create Default Settings
    |--------------------------------------------------------------------------
    */

    public function createDefaults()
    {
        $this->service->createDefaults();

        return response()->json([
            'success' => true,
            'message' => 'Default settings created successfully.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(
        string $group,
        string $key
    ) {
        $deleted = $this->service->delete(
            $group,
            $key
        );


        if (!$deleted) {

            return response()->json([
                'success' => false,
                'message' => 'Setting not found.',
            ], 404);
        }


        return response()->json([
            'success' => true,
            'message' => 'Setting deleted successfully.',
        ]);
    }
}