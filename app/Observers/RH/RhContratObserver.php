<?php

namespace App\Observers\RH;

use App\Models\ActivityLog;
use App\Models\RhContrat;

class RhContratObserver
{
    /**
     * Handle the RhContrat "created" event.
     *
     * @param  \App\Models\RhContrat  $rhContrat
     * @return void
     */
    public function created(RhContrat $contrat)
    {
        ActivityLog::create([
            'table_name' => $contrat->getTable(),
            'record_id' => $contrat->id,
            'action' => 'created',
            'new_values' => $contrat->toArray(),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the RhContrat "updated" event.
     *
     * @param  \App\Models\RhContrat  $rhContrat
     * @return void
     */
    public function updated(RhContrat $contrat)
    {
        ActivityLog::create([
            'table_name' => $contrat->getTable(),
            'record_id' => $contrat->id,
            'action' => 'updated',
            'old_values' => $contrat->getOriginal(),
            'new_values' => $contrat->getChanges(),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the RhContrat "deleted" event.
     *
     * @param  \App\Models\RhContrat  $rhContrat
     * @return void
     */
    public function deleted(RhContrat $contrat)
    {
        ActivityLog::create([
            'table_name' => $contrat->getTable(),
            'record_id' => $contrat->id,
            'action' => 'deleted',
            'old_values' => $contrat->toArray(),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the RhContrat "restored" event.
     *
     * @param  \App\Models\RhContrat  $rhContrat
     * @return void
     */
    public function restored(RhContrat $contrat)
    {
        //
    }

    /**
     * Handle the RhContrat "force deleted" event.
     *
     * @param  \App\Models\RhContrat  $rhContrat
     * @return void
     */
    public function forceDeleted(RhContrat $contrat)
    {
        //
    }
}
