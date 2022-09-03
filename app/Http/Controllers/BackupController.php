<?php

namespace App\Http\Controllers;

use App\Backup;
use App\Http\Resources\BackupResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class BackupController extends Controller
{
    public function index(Request $request):Response
    {
        return inertia('Backups/Index', [
            'backups' => BackupResource::collection(Backup::all()),
            'isDirty' => Backup::isDirty(),
        ]);
    }

    public function create(Request $request):Response
    {
        Backup::create();

        return $this->index($request);
    }

    public function restore(Request $request): \Illuminate\Http\RedirectResponse
    {
        $filename = $request->input('filename');

        Backup::restore($filename);

        return redirect()->route('members')
            ->with('success', "Das Backup wurde wiederhergestellt.");
    }

}
