<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentPromotionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'school_id' => $this->school_id,

            /*
            |--------------------------------------------------------------------------
            | Student
            |--------------------------------------------------------------------------
            */

            'student' => [

                'id' => $this->student?->id,

                'admission_no' => $this->student?->admission_no,

                'roll_no' => $this->student?->roll_no,

                'first_name' => $this->student?->first_name,

                'last_name' => $this->student?->last_name,

                'full_name' => trim(
                    ($this->student?->first_name ?? '') . ' ' .
                    ($this->student?->last_name ?? '')
                ),

            ],

            /*
            |--------------------------------------------------------------------------
            | From
            |--------------------------------------------------------------------------
            */

            'from_session' => [

                'id' => $this->fromSession?->id,

                'name' => $this->fromSession?->name,

            ],

            'from_class' => [

                'id' => $this->fromClass?->id,

                'name' => $this->fromClass?->name,

            ],

            'from_section' => [

                'id' => $this->fromSection?->id,

                'name' => $this->fromSection?->name,

            ],

            /*
            |--------------------------------------------------------------------------
            | To
            |--------------------------------------------------------------------------
            */

            'to_session' => [

                'id' => $this->toSession?->id,

                'name' => $this->toSession?->name,

            ],

            'to_class' => [

                'id' => $this->toClass?->id,

                'name' => $this->toClass?->name,

            ],

            'to_section' => [

                'id' => $this->toSection?->id,

                'name' => $this->toSection?->name,

            ],

            /*
            |--------------------------------------------------------------------------
            | Promotion
            |--------------------------------------------------------------------------
            */

            'promotion_date' => optional($this->promotion_date)
                ->format('Y-m-d'),

            'promotion_type' => $this->promotion_type,

            'promotion_badge' => $this->promotion_badge,

            'remarks' => $this->remarks,

            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            'created_by' => $this->creator?->name,

            'updated_by' => $this->updater?->name,

            'created_at' => optional($this->created_at)
                ->format('Y-m-d H:i:s'),

            'updated_at' => optional($this->updated_at)
                ->format('Y-m-d H:i:s'),

        ];
    }
}