<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentRequest;
use App\Models\Classeur;
use App\Models\Document;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

use function PHPSTORM_META\type;

class Documents extends Controller
{
    public function show()
    {
        $documents = Document::where('user_id', '=', Auth::user()->id)->get();
        // The user may send documents in the Services which he appears
        // 
        return Inertia::render('Documents', [
            'user' => Auth::user(),
            'documents' => $documents,
            'users' => User::all()
                ->where('role', '=', null)
                ->where('id', '!=', Auth::user()->id),
            'services' => Service::all(),
            'classeurs' => Classeur::where('user_id', '=', Auth::user()->id)->get()
        ]);
    }

    public function create(Request $request)
    {
        $documents = Document::where('user_id', '=', Auth::user()->id)->get();
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileSize = $file->getSize();
            $extension = '.' . $file->getClientOriginalExtension();
            $filename = substr($file->getClientOriginalName(), 0, strlen($file->getClientOriginalName()) - strlen($extension));
            $path = $file->store("documents", 'public');
            $path = "/" . $path;

            Document::create([
                'titre' => $filename,
                'chemin' => $path,
                'taille' => $fileSize,
                'extension' => $extension,
                'user_id' => Auth::id()
            ]);

            return Inertia::render('Documents', [
                'user' => Auth::user(),
                'documents' => $documents
            ]);
        }
    }

    public function update(DocumentRequest $request)
    {
        $documents = Document::where('user_id', '=', Auth::user()->id)->get();
        DB::table('documents')
            ->where('id', $request->id)
            ->update([
                'titre' => $request->titre,
                'type' => $request->type,
                'description' => $request->description
            ]);

        return Inertia::render('Documents', [
            'user' => Auth::user(),
            'users' => User::all()
                ->where('role', '=', null)
                ->where('id', '!=', Auth::user()->id),
            'services' => Service::all(),
            'documents' => $documents,
            'updated' => true
        ]);
    }

    public function delete(Request $request)
    {
        Document::findOrFail($request->id)->delete();
        $documents = Document::where('user_id', '=', Auth::user()->id)->get();
        return Inertia::render('Documents', [
            'user' => Auth::user(),
            'documents' => $documents,
            'users' => User::all()
                ->where('role', '=', null)
                ->where('id', '!=', Auth::user()->id),
            'services' => Service::all()
        ]);
    }

    public function share(Request $request)
    {
        $content = json_decode($request->getContent());
        $sharedDoc = Document::findOrFail($content->docId);

        if ($content->checkedUsers) {
            for ($i = 0; $i < count($content->checkedUsers); $i++) {
                Document::create([
                    'titre' => $sharedDoc->titre,
                    'chemin' => $sharedDoc->chemin,
                    'taille' => $sharedDoc->taille,
                    'extension' => $sharedDoc->extension,
                    'user_id' => $content->checkedUsers[$i]
                ]);
            }
        }
        if ($content->checkedServices) {
            for ($i = 0; $i < count($content->checkedServices); $i++) {
                Document::create([
                    'titre' => $sharedDoc->titre,
                    'chemin' => $sharedDoc->chemin,
                    'taille' => $sharedDoc->taille,
                    'extension' => $sharedDoc->extension,
                    'service_id' => $content->checkedServices[$i]
                ]);
            }
        }
        if ($content->checkedClasseurs) {
            for ($i = 0; $i < count($content->checkedClasseurs); $i++) {
                Document::create([
                    'titre' => $sharedDoc->titre,
                    'chemin' => $sharedDoc->chemin,
                    'taille' => $sharedDoc->taille,
                    'extension' => $sharedDoc->extension,
                    'classeur_id' => $content->checkedClasseurs[$i]
                ]);
            }
        }

        $documents = Document::where('user_id', '=', Auth::user()->id)->get();
        return Inertia::render('Documents', [
            'user' => Auth::user(),
            'users' => User::all()
                ->where('role', '=', null)
                ->where('id', '!=', Auth::user()->id),
            'services' => Service::all(),
            'documents' => $documents
        ]);
    }
}
