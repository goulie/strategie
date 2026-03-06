<?php

namespace App\Traits\RH;

use App\Models\ActivityLog;

trait HasActivityLog
{
    
    protected array $activityLogIgnore = [
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    

    public static function bootHasActivityLog()
    {
        static::updated(function ($model) {

            $changes = [];

            foreach ($model->getChanges() as $field => $newValue) {

                if (in_array($field, $model->activityLogIgnore ?? [])) {
                    continue;
                }

                $oldValue = $model->getOriginal($field);

                if ($oldValue != $newValue) {
                    $changes[$field] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }

            // 👉 Aucun changement métier → on ne log rien
            if (empty($changes)) {
                return;
            }

            ActivityLog::create([
                'table_name' => $model->getTable(),
                'record_id' => $model->getKey(),
                'action' => 'updated',
                'old_values' => null,
                'new_values' => $changes,
                'user_id' => auth()->id(),
            ]);
        });

        static::created(function ($model) {
            ActivityLog::create([
                'table_name' => $model->getTable(),
                'record_id' => $model->getKey(),
                'action' => 'created',
                'new_values' => $model->toArray(),
                'user_id' => auth()->id(),
            ]);
        });

        static::deleted(function ($model) {
            ActivityLog::create([
                'table_name' => $model->getTable(),
                'record_id' => $model->getKey(),
                'action' => 'deleted',
                'old_values' => $model->toArray(),
                'user_id' => auth()->id(),
            ]);
        });
    }
}
