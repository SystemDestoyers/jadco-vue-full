<?php

namespace App\Models\Traits;

use Illuminate\Support\Carbon;

trait FixesInvalidDates
{
    /**
     * Boot the FixesInvalidDates trait for a model.
     *
     * @return void
     */
    public static function bootFixesInvalidDates()
    {
        static::creating(function ($model) {
            // Ensure timestamps are set to valid values (current year or past)
            $now = Carbon::now();
            if ($now->year > 2023) {
                $now->setYear(2023);
            }
            
            if (!$model->created_at) {
                $model->created_at = $now;
            } else if (Carbon::parse($model->created_at)->year > 2023) {
                $model->created_at = Carbon::parse($model->created_at)->setYear(2023);
            }
            
            if (!$model->updated_at) {
                $model->updated_at = $now;
            } else if (Carbon::parse($model->updated_at)->year > 2023) {
                $model->updated_at = Carbon::parse($model->updated_at)->setYear(2023);
            }
        });

        static::updating(function ($model) {
            // Ensure the updated_at timestamp is valid when updating
            $now = Carbon::now();
            if ($now->year > 2023) {
                $now->setYear(2023);
            }
            
            if (!$model->updated_at) {
                $model->updated_at = $now;
            } else if (Carbon::parse($model->updated_at)->year > 2023) {
                $model->updated_at = Carbon::parse($model->updated_at)->setYear(2023);
            }
        });
    }
} 