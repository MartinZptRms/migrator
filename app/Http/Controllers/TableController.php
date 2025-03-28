<?php

namespace App\Http\Controllers;

use App\Models\Database;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use PDO;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Database $database)
    {
        $items = $database->tables()->get();
        return View::make('pages.databases.tables.index', compact('database','items'));
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
    public function show(Database $database)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Database $database)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Database $database)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Database $database)
    {
        //
    }

    public function sync(Request $request, Database $database)
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
        $tables = array_column($db->select('SHOW TABLES'),"Tables_in_$database->name");
        foreach($tables as $t){
            $database->tables()->updateOrCreate([
                'name' => $t
            ]);
        }

        return Redirect::route('databases.tables.index', $database->id)->with('success');
    }

    public function syncColumns(Request $request, Database $database)
    {
        $validator = Validator::make($request->all(),[
            'table_id' => ['required','array']
        ]);

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

        foreach($request->table_id as $t){
            $table = Table::findOrFail($t);
            $columns = $db->select("select * from information_schema.columns
                                    where TABLE_SCHEMA = '$database->name'
                                    and TABLE_NAME = '$table->name'
                                    order by table_name, ordinal_position");
            foreach($columns as $c){
                $table->columns()->updateOrCreate([
                    'name' => $c->COLUMN_NAME,
                    'data_type' => $c->COLUMN_TYPE
                ]);
            }
        }

        return Response::make(['code' => 1], 200, ['Content-Type' => 'application/json']);
    }
}
