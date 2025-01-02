<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConnectionRequest;
use App\Models\Connection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class ConnectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Connection::get();
        return View::make('pages.connections.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return View::make('pages.connections.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConnectionRequest $request)
    {
        $validated = $request->safe()->all();

        Connection::create([
            'name' => $validated['name'],
            'host' => $validated['host'],
            'port' => $validated['port'],
            'username' => $validated['username'],
            'password' => $validated['password'],
        ]);

        return Redirect::route('connections.index')->with('success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Connection $connection)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Connection $connection)
    {
        return View::make('pages.connections.edit', compact('connection'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreConnectionRequest $request, Connection $connection)
    {
        $validated = $request->safe()->all();

        $connection->update([
            'name' => $validated['name'],
            'host' => $validated['host'],
            'port' => $validated['port'],
            'username' => $validated['username'],
            'password' => $validated['password'],
        ]);

        return Redirect::route('connections.index')->with('success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Connection $connection)
    {
        $connection->delete();
    }
}
