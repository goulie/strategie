<?php

namespace App\Observers;

use App\Models\RhContrat;
use App\Models\RhContratTerminaison;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RhContratTerminaisonObserver
{
    /**
     * Handle the RhContratTerminaison "created" event.
     *
     * @param  \App\Models\RhContratTerminaison  $rhContratTerminaison
     * @return void
     */
    public function created(RhContratTerminaison $rhContratTerminaison)
    {
        
        DB::transaction(function () use ($rhContratTerminaison) {

            $contrat = $rhContratTerminaison->contrat;

            if (!$contrat) {
                return;
            }

            // Sécurité : éviter double clôture
            if ($contrat->date_fin !== null) {
                $contrat->update([
                    'statut'   => RhContrat::STATUT_INACTIF,
                ]);
                return;
            }

            $contrat->update([
                'date_fin' => $rhContratTerminaison->date_termination,
                'statut'   => RhContrat::STATUT_INACTIF,
            ]);

            Log::alert('Contrat terminaison : ' . $contrat);
        });
    }

    /**
     * Handle the RhContratTerminaison "updated" event.
     *
     * @param  \App\Models\RhContratTerminaison  $rhContratTerminaison
     * @return void
     */
    public function updated(RhContratTerminaison $rhContratTerminaison)
    {
        //
    }

    /**
     * Handle the RhContratTerminaison "deleted" event.
     *
     * @param  \App\Models\RhContratTerminaison  $rhContratTerminaison
     * @return void
     */
    public function deleted(RhContratTerminaison $rhContratTerminaison)
    {
        //
    }

    /**
     * Handle the RhContratTerminaison "restored" event.
     *
     * @param  \App\Models\RhContratTerminaison  $rhContratTerminaison
     * @return void
     */
    public function restored(RhContratTerminaison $rhContratTerminaison)
    {
        //
    }

    /**
     * Handle the RhContratTerminaison "force deleted" event.
     *
     * @param  \App\Models\RhContratTerminaison  $rhContratTerminaison
     * @return void
     */
    public function forceDeleted(RhContratTerminaison $rhContratTerminaison)
    {
        //
    }
}
