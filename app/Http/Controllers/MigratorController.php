<?php

namespace App\Http\Controllers;

use App\Models\Database;
use App\Models\Service;
use App\Models\Connection;
use App\Models\Table;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;
use Illuminate\Database\QueryException;

class MigratorController extends Controller
{
    protected ConnectionInterface $dbSource;
    protected ConnectionInterface $dbTarget;

    public function connection(Connection $connection, Database $database)
    {
        return DB::connectUsing($database->name,[
            'driver' => 'mariadb',
            'url' => "",
            'host' => $database->connection->host,
            'port' => $database->connection->port,
            'database' => $database->name,
            'username' => $database->connection->username,
            'password' => $database->connection->password,
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([ PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'), ]) : [],
        ]);
    }

    public function init(Service $service){
        $this->dbSource = $this->connection(new Connection, $service->source_database->database);
        $this->dbTarget = $this->connection(new Connection, $service->target_database->database);
    }

    public function start(Service $service)
    {
        // $service = Service::first();

        $this->init($service);

        $this->createTables($service);

        $this->migration($service);

        return "HECHO";
    }

    public function createTables(Service $service)
    {
        $targetDatabase = $service->target_database;
        $serviceTargetTables = $targetDatabase->tables()->get();

        try{
            foreach($serviceTargetTables as $stt){
                $table = $stt->table;
                $serviceTargetColumns = $stt->columns()->get();
                $columns = [];
                foreach($serviceTargetColumns as $stc){
                    $columns[] = $stc->custom_column ? $stc->custom_column->name." ".$stc->custom_column->data_type : $stc->column->name." ".$stc->column->data_type;
                }
                $statementColumns = implode(', ', $columns);
                $statementCreateTable = "CREATE TABLE $table->name ( $statementColumns ); ";

                $this->dbTarget->statement($statementCreateTable);
            }

        }catch(Exception $e){

        }
    }

    public function migration(Service $service)
    {
        set_time_limit(-1);
        // $this->dbSource->beginTransaction();
        // $this->dbTarget->beginTransaction();
        $sourceDatabase = $service->source_database;
        $database = $sourceDatabase->database;
        $sourceServiceTables = $sourceDatabase->tables()->get();

        $targetDatabase = $service->target_database;
        $targetServiceTables = $targetDatabase->tables()->get();

        #selected table base - icon star
        $sourceBaseTable = $sourceServiceTables->where('source', 1)->first();
        $table = $sourceBaseTable->table;

        $columns = $this->dbSource->select("select * from information_schema.columns where TABLE_SCHEMA = '$database->name' and TABLE_NAME = '$table->name' order by table_name, ordinal_position");
        foreach($targetServiceTables as $sst){
            $targetServiceColumns = $sst->columns()->with('column')->get();

            $stringColumns = $targetServiceColumns->pluck('alias')->toArray();

            $sourceTable = $this->dbSource->table($table->name)->select($stringColumns);
            // $sourceTable = $this->dbSource->table($table->name);

            $clauses = $sourceBaseTable->clauses()->get();
            foreach($clauses as $cl){
                if($cl->type == 1){
                    $sourceTable = $sourceTable->where($cl->field, $cl->operator, eval('return '.$cl->value.';'));
                }else if($cl->clause == 'where'){
                    $sourceTable = $sourceTable->where($cl->field, $cl->operator, $cl->value);
                }
            }
            $max_placeholders = 65000;
            $rows_per_block = floor($max_placeholders / $targetServiceColumns->pluck('alias')->count());
            $columnOrderBy = $clauses->where('type', '1')->first()?->field ?: $columns[0]->COLUMN_NAME;
            $sourceTable = $sourceTable->orderBy($columnOrderBy)->chunk($rows_per_block, function($data) use ($sst){
                $dataInsert = json_decode (json_encode ($data), TRUE);
                $this->dbTarget->table($sst->table->name)->insert( $dataInsert );
            });
        }
    }
}
