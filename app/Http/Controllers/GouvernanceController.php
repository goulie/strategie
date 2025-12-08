<?php

namespace App\Http\Controllers;

use App\Imports\GouvernanceImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class GouvernanceController extends VoyagerBaseController
{
    
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new GouvernanceImport, $request->file('file'));

        return back()->with('success', 'Importation réussie !');
    }
}
