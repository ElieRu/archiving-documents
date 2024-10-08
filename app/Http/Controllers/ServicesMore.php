<?php

namespace App\Http\Controllers;

use App\Models\Classeur;
use App\Models\Document;
use App\Models\Service;
use App\Models\Services_user;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Ramsey\Collection\Collection as CollectionCollection;

use function Laravel\Prompts\table;

class ServicesMore extends Controller
{

    public function returnDatas($users, $add_users, $service, $classeurs, $documents)
    {
        return Inertia::render('ServicesMore', [
            'user' => Auth::user(),
            'users' => $users,
            'add_users' => $add_users,
            'service' => $service,
            'classeurs' => $classeurs,
            'documents' => $documents
        ]);
    }

    public function show(Request $request)
    {
        // dd($request->id);
        $service = Service::findOrFail($request->id);
        // dd($request->searchClasseur);
        if ($request->role) {
            $classeurs = Classeur::where('id', '=', $service->id)
                ->get();
        } else {
            $classeurs = Classeur::query()
                ->where('user_id', '=', Auth::user()->id)
                ->where('service_id', '=', $service->id)
                ->when($request->searchClasseur, function ($query, $searchClasseur) {
                    $query->where('classeurs.nom', 'like', "%{$searchClasseur}%");
                })
                ->paginate(24)
                ->withQueryString();

            $documents = Document::query()
                ->where('service_id', '=', $service->id)
                ->paginate(24)
                ->withQueryString();
        }

        $users = DB::table('users')
            ->join('services_users', 'services_users.user_id', '=', 'users.id')
            ->where('services_users.service_id', '=', $request->id)
            ->select('services_users.id as id', 'users.name', 'users.postname', 'services_users.service_id')
            ->get();

            // dd($users);

        if (!$users->isEmpty()) {
            $all_users = DB::table('users')
                ->join('services_users', 'services_users.user_id', '<>', 'users.id')
                ->where('services_users.service_id', '=', $request->id)
                ->where('users.role', '=', null)
                ->distinct()
                ->select('users.id as id', 'users.name', 'users.postname', 'services_users.service_id')
                ->get();
            $array_users = [];
            foreach ($all_users as $row) {
                if (!in_array($row->id, $users->pluck('id')->toArray())) {
                    $array_users[] = $row;
                } 
            }
            $add_users = collect($array_users);
            return $this->returnDatas($users, $add_users, $service, $classeurs, $documents);
        } else {
            $add_users = DB::table('users')
                ->where('users.role', '=', null)
                ->select('users.id as id', 'users.name', 'users.postname')
                ->get();
            return $this->returnDatas($users, $add_users, $service, $classeurs, $documents);
        }
    }

    public function addMermbers(Request $request)
    {
        for ($i = 0; $i < count($request->users_checked); $i++) {
            Services_user::create([
                'service_id' => $request->service_id,
                'user_id' => $request->users_checked[$i]
            ]);
        }
        return $this->show($request);
    }

    public function deleteMembers(Request $request)
    {
        for ($i = 0; $i < count($request->users_checked); $i++) {
            Services_user::findOrFail($request->users_checked[$i])->delete();
        }
        return $this->show($request);
    }
}
