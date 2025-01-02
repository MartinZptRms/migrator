<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDatabaseRequest;
use App\Models\Connection;
use App\Models\Database;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class DatabaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Database::get();
        return View::make('pages.databases.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $connections = Connection::get();
        return View::make('pages.databases.create', compact('connections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDatabaseRequest $request)
    {
        $validated = $request->safe()->all();

        Database::create([
            'connection_id' => $validated['connection_id'],
            'name' => $validated['name'],
        ]);

        return Redirect::route('databases.index')->with('success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Database $database)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Database $database)
    {
        $connections = Connection::get();
        return View::make('pages.databases.edit', compact('database','connections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreDatabaseRequest $request, Database $database)
    {
        $validated = $request->safe()->all();

        $database->update([
            'connection_id' => $validated['connection_id'],
            'name' => $validated['name'],
        ]);

        return Redirect::route('databases.index')->with('success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Database $database)
    {
        $database->delete();
    }
}
