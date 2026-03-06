<?php

namespace App\Http\Controllers\DMS;

use App\Models\DmsBudgetsAgr;
use Illuminate\Http\Request;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class BudgetAgrController extends VoyagerBaseController
{
    public function store(Request $request)
    {
        // Vérifier si un budget existe déjà pour cette activité et cette année
        $existingBudget = DmsBudgetsAgr::where('activity_id', $request->activity_id)
            ->where('year', $request->year)
            ->first();

        if ($existingBudget) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'duplicate' => 'Un budget existe déjà pour cette activité pour l\'année ' . $request->year
                ]);
        }

        return parent::store($request);
    }
}
