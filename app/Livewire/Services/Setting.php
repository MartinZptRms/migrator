<?php

namespace App\Livewire\Services;

use App\Models\Column;
use App\Models\Database;
use App\Models\Service;
use App\Models\ServiceDatabase;
use App\Models\ServiceDatabaseTable;
use App\Models\ServiceDatabaseTableColumn;
use App\Models\Table;
use Illuminate\Support\Collection;
use Livewire\Component;

class Setting extends Component
{
    public Service $service;
    public ServiceDatabase $serviceDatabases;
    public ServiceDatabase $serviceSourceDatabase;
    public ServiceDatabase $serviceTargetDatabase;
    public Collection $serviceSourceTables;
    public Collection $serviceTargetTables;

    public ServiceDatabaseTable|null $selectedServiceSourceTable;
    public ServiceDatabaseTable|null $selectedServiceTargetTable;

    public int $step = 1;
    public Collection $databases;
    public array $inputDatabases = [];

    public Collection $tables;
    public bool $allTables;
    public array $inputTables;

    public Collection $serviceSourceColumns;
    public Collection $serviceTargetColumns;

    public Column|null $selectedSourceColumn;
    public ServiceDatabaseTableColumn|null $selectedServiceTargetColumn;

    public function mount()
    {
        $this->databases = Database::get();
        $this->tables = Collection::make();
        $this->serviceSourceTables = Collection::make();
        $this->serviceTargetTables = Collection::make();
        $this->serviceSourceColumns = Collection::make();
        $this->serviceTargetColumns = Collection::make();
        $this->serviceSourceClauses = Collection::make();

        if($this->service->databases()->count() > 1){
            $currentServiceDatabases = $this->service->databases()->get();
            $this->inputDatabases[0] = $currentServiceDatabases->where('type', 0)->first()->database_id;
            $this->inputDatabases[1] = $currentServiceDatabases->where('type', 1)->first()->database_id;
        }
    }

    public function render()
    {
        return view('livewire.services.setting');
    }

    public function stepNext()
    {
        if($this->step == 1){
            $this->stepTables();
        }elseif($this->step == 2){
            $this->stepIntegration();
        }
        $this->step++;
    }

    public function stepTables(){
        $this->tables = $this->databases->find($this->inputDatabases[0])->tables()->get();
        $this->serviceSourceDatabase = $this->service->databases()->firstOrCreate(['database_id' => $this->inputDatabases[0],'type' => 0 ]);
        $this->serviceTargetDatabase = $this->service->databases()->firstOrCreate(['database_id' => $this->inputDatabases[1],'type' => 1 ]);
    }

    public function updatedAllTables()
    {
        if($this->allTables){
            $this->inputTables = $this->tables->pluck('id')->toArray();
        }else{
            $this->inputTables = [];
        }
    }

    public function updatedInputTables()
    {
        if(count($this->inputTables) < $this->databases->find($this->inputDatabases[0])->tables()->count()){
            $this->allTables = false;
        }else{
            $this->allTables = true;
        }
    }

    public function stepIntegration()
    {
        $selectedTables = $this->databases->find($this->inputDatabases[0])->tables()->whereIn('id', $this->inputTables)->get();
        foreach($selectedTables as $st){
            $this->serviceSourceDatabase->tables()->firstOrCreate([
                'table_id' =>  $st->id
            ]);
        }

        $this->serviceSourceTables = $this->serviceSourceDatabase->tables()->get();
        $this->selectedServiceSourceTable = $this->serviceSourceTables[0];
        $this->serviceSourceColumns = $this->selectedServiceSourceTable->table->columns()->get();

        $this->serviceTargetTables = $this->serviceTargetDatabase->tables()->get();
        $this->serviceSourceClauses = $this->selectedServiceSourceTable->clauses()->get();
    }

    #Integration
    public function setSourceTarget($id){ //source identity for selected table
        ServiceDatabaseTable::whereIn('id',$this->serviceSourceTables->pluck('id'))->update([ 'source' => 0 ]);
        ServiceDatabaseTable::where('id', $id)->update([ 'source' => 1 ]);
    }
    public function changeSourceTable($id)
    {
        $this->selectedServiceSourceTable = $this->serviceSourceTables->where('id', $id)->first();
        $this->serviceSourceColumns = $this->selectedServiceSourceTable->table->columns()->get();
        $this->selectedSourceColumn = null;

        $this->serviceSourceClauses = $this->selectedServiceSourceTable->clauses()->get();
    }
    public function changeSourceColumn($id)
    {
        $this->selectedSourceColumn = $this->serviceSourceColumns->where('id', $id)->first();

    }

    public function changeTargetTable($id)
    {
        $this->selectedServiceTargetTable = $this->serviceTargetDatabase->tables()->find($id);
        $this->serviceTargetColumns = $this->selectedServiceTargetTable->columns()->get();
        $this->selectedServiceTargetColumn = null;
    }

    public function changeTargetColumn($id)
    {
        $this->selectedServiceTargetColumn = $this->serviceTargetColumns->find($id);
        $this->changeSourceTable($this->serviceSourceDatabase->tables()->where('table_id',$this->selectedServiceTargetColumn->column->table->id)->first()->id);

        $this->selectedSourceColumn = $this->serviceSourceColumns->where('id', $this->selectedServiceTargetColumn->column_id)->first();
        $this->selectedServiceSourceTable = $this->serviceSourceTables->where('table_id', $this->selectedSourceColumn->table_id)->first();
    }

    public function equalTo()
    {
        $this->selectedServiceTargetTable = $this->serviceTargetDatabase->tables()->firstOrCreate([
            'table_id' =>  $this->selectedServiceSourceTable->table_id
        ]);

        foreach ($this->selectedServiceSourceTable->table->columns()->get() as $c) {
            $this->selectedServiceTargetTable->columns()->firstOrCreate([
                'column_id' => $c->id,
            ]);
        }

        $this->serviceTargetTables = $this->serviceTargetDatabase->tables()->get();
        $this->serviceTargetColumns = $this->selectedServiceTargetTable->columns()->get();
    }

    public function equalToColumn()
    {
        $this->selectedServiceTargetTable->columns()->firstOrCreate([
            'column_id' => $this->selectedSourceColumn->id,
        ]);
    }

    public string $customTable;
    public function addCustomTable()
    {
        $table = $this->serviceTargetDatabase->database->tables()->firstOrCreate([
            'name' => $this->customTable,
            'type' => 1
        ]);

        $this->selectedServiceTargetTable = $this->serviceTargetDatabase->tables()->firstOrCreate([
            'table_id' =>  $table->id
        ]);

        $this->serviceTargetTables = $this->serviceTargetDatabase->tables()->get();
        $this->serviceTargetColumns = $this->selectedServiceTargetTable->columns()->get();

        $this->customTable = "";
    }

    public string $customColumn;
    public function addCustomColumn()
    {
        $column = $this->selectedServiceTargetTable->table->columns()->firstOrCreate([
            'name' =>  $this->customColumn,
            'data_type' => $this->selectedSourceColumn->data_type
        ]);

        $this->selectedServiceTargetTable->columns()->firstOrCreate([
            'column_id' => $this->selectedSourceColumn->id,
            'custom_column_id' => $column->id
        ]);

        $this->serviceTargetTables = $this->serviceTargetDatabase->tables()->get();
        $this->serviceTargetColumns = $this->selectedServiceTargetTable->columns()->get();

        $this->customColumn = "";
    }

    public Collection $serviceSourceClauses;
    public string $clause_type, $clause, $field, $operator, $value;
    public function addClause(){
        $this->selectedServiceSourceTable->clauses()->create([
            'type' => $this->clause_type,
            'clause' => $this->clause,
            'field' => $this->field,
            'operator' => $this->operator,
            'value' => $this->value,
        ]);

        $this->clause = "";
        $this->field = "";
        $this->operator = "";
        $this->value = "";

        $this->serviceSourceClauses = $this->selectedServiceSourceTable->clauses()->get();
    }
    public function removeClause($service_database_table_clause_id){
        $this->selectedServiceSourceTable->clauses()->where('id', $service_database_table_clause_id)->delete();
        $this->serviceSourceClauses = $this->selectedServiceSourceTable->clauses()->get();
    }

    public $join_from_table, $join_type, $join_to_table, $join_from_column, $join_to_column;
    public function addJoin()
    {

    }

}
