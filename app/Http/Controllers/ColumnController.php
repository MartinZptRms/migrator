<?php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\Database;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use PDO;

class ColumnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Database $database, Table $table)
    {
        $items = $table->columns()->get();
        return View::make('pages.databases.tables.columns.index', compact('database','table','items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Column $column)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Column $column)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Column $column)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Column $column)
    {
        //
    }

    public function sync(Request $request, Database $database, Table $table)
    {
        $db = DB::connectUsing($database->name,[
            'driver' => 'mariadb',
            'url' => "",
            'host' => $database->connection->host,
            'port' => $database->connection->port,
            'database' => $database->name,
            'username' => $database->connection->username,
            'password' => $database->connection->password,
            // 'password' => "1p86dbF7jg4G9dou",
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([ PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'), ]) : [],
        ]);

        $columns = $db->select("select * from information_schema.columns where TABLE_SCHEMA = '$database->name' and TABLE_NAME = '$table->name' order by table_name, ordinal_position");

        foreach($columns as $c){
            $table->columns()->updateOrCreate([
                'name' => $c->COLUMN_NAME,
                'data_type' => $c->COLUMN_TYPE
            ]);
        }

        return Redirect::route('databases.tables.columns.index', [$database->id, $table->id])->with('success');
    }
}
