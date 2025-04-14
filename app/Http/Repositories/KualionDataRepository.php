<?php

namespace App\Http\Repositories;

use App\Http\Helpers\CENACEInvoicesHelper;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class KualionDataRepository
{
    private $sourceConnection;
    private $targetConnection;
    private $teamId = 155;
    private $startDate;
    private $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->sourceConnection = DB::connection('mysql');
        $this->targetConnection = DB::connection('oracle');
        if (null == $startDate) {
            $this->startDate = Carbon::now()->subDay()->toDateString();
        } else {
            $this->startDate = $startDate;
        }

        if (null !== $endDate) {
            $this->endDate = $endDate;
        } else if (null !== $startDate){
            $this->endDate = $startDate;
        } else {
            $this->endDate = Carbon::now()->subDay()->toDateString();
        }
    }

    public function dispatchTables()
    {
        $methods = [
            'contrapartesTable',
            'diccionarioDeFoliosTable',
            'listadoDeContratosTable',
            'plantasDeGeneracionTable',
            'centrosDeCargaTable',
            'tipoCambioFixTable',
            'energiaAsignadaZonadeCargaTable',
            'energiaGeneradaporTipodeTeconologiaTable',
            'medicionesHorariasCCTable',
            'medicionesHorariasCETable',
            'ofertaDeCompraPorTipoTable',
            'preciosGasHBTable',
            'preciosPMLMTRTable',
            'preciosPNDMDATable',
            'preciosPNDMTRTable',
            'preciosSPOTERCOTTable',
            'proyeccionesDeEnergiaTable',
            'tipoDeCambioLiquidacionTable',
            'liquidacionesDiariasECDTable',
            'liquidacionesHorariasECDTable',
            'preciosPMLMDATable',
            'facturacionCENACETable',
            'facturacionUCTable',
            'ecdsCENACETable',
            'nodosPTable',
            'ofertasCompraEnergiaTable',
            'ofertasVentaEnergiaTable',
            'ofertasVentaPorTipoTable',
            'calculoDeContratosTable',
        ];

        foreach ($methods as $method) {
            try {
                $this->$method();
                error_log(
                    date("[Y-m-d H:i:s]") . $method . " done" . PHP_EOL . PHP_EOL,
                    3,
                    storage_path('logs/tables.log')
                );
            } catch (Exception $e) {
                error_log(
                    date("[Y-m-d H:i:s]") . $e . PHP_EOL . PHP_EOL,
                    3,
                    storage_path('logs/table_errors.log')
                );
            }
        }

        error_log(
            date("[Y-m-d H:i:s]") . " Tables done" . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function contrapartesTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where teamId = %s and created_at >= '%s'",
            "id, alias, name, rfc, email, municipio",
            "enegence_cloud.invoiceRecipients",
            $this->teamId,
            $this->startDate,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);

        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'ID'      => $item['id'],
                    'SHORTNAME'      => $item['alias'],
                    'NAME'           => $item['name'],
                    'RFC'            => $item['rfc'],
                    'EMAIL'          => $item['email'],
                    'MUNICIPIO'      => $item['municipio'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('CONTRAPARTES')->upsert(
                    $chunk,
                    [
                        'ID',
                        'NAME',
                        'RFC',
                    ],
                    [
                        'SHORTNAME',
                        'NAME',
                        'RFC',
                        'EMAIL',
                        'MUNICIPIO'
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
        
    }

    public function diccionarioDeFoliosTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s",
            "folio, concepto, mercado, clasificacion, descripcion",
            "enegence_dev.ecd_conceptos",
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);

        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'FOLIO'         => $item['folio'],
                    'CONCEPTO'      => $item['concepto'],
                    'MERCADO'       => $item['mercado'],
                    'CLASIFICACION' => $item['clasificacion'],
                    'DESCRIPCION'   => $item['descripcion'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('DICCIONARIOFOLIOSLIQUIDACION')->upsert(
                    $chunk,
                    [
                        'FOLIO',
                        'CONCEPTO',
                        'MERCADO',
                    ],
                    [
                        'CLASIFICACION',
                        'DESCRIPCION'
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function listadoDeContratosTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where teamId = %s and updated_at >= '%s'",
            "id, contractNumber, contractNumberId, name, clients, centrosDeCarga, centralesElectricas, created_at, updated_at, contract_template, vigenciaStartDate, vigenciaEndDate, currency, demandaContratada, energiaContratada, frecuenciaDeCalculo",
            "enegence_cloud.contracts",
            $this->teamId,
            $this->startDate,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'ID'      => $item['id'],
                    'CONTRACTNUMBER'   => $item['contractNumber'],
                    'CONTRACTNUMBERID' => $item['contractNumberId'],
                    'NAME'             => $item['name'],
                    'CLIENTE'           => $item['clients'],
                    'CENTRODECARGA'    => $item['centrosDeCarga'],
                    'CREATED_AT'       => $item['created_at'],
                    'UPDATED_AT'       => $item['updated_at'],
                    'INICIOVIGENCIA'   => $item['vigenciaStartDate'],
                    'FINVIGENCIA'      => $item['vigenciaEndDate'],
                    'MONEDA'           => $item['currency'],
                    'DEMAND'           => $item['demandaContratada'],
                    'CONTRACT_TEMPLATE' => $item['contract_template'],
                    'ENERGIACONTRATADA' => $item['energiaContratada'],
                    'CENTRALESELECTRICAS' => $item['centralesElectricas'],
                    'FRECUENCIADECALCULO' => $item['frecuenciaDeCalculo'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('LISTADOCONTRATOS')->upsert(
                    $chunk,
                    [
                        'ID',
                    ],
                    [
                        'NAME',
                        'CLIENTE',
                        'CENTRODECARGA',
                        'CREATED_AT',
                        'UPDATED_AT',
                        'INICIOVIGENCIA',
                        'FINVIGENCIA',
                        'MONEDA',
                        'DEMAND',
                        'CONTRACT_TEMPLATE',
                        'ENERGIACONTRATADA',
                        'CENTRALESELECTRICAS',
                        'FRECUENCIADECALCULO',
                        'CONTRACTNUMBER',
                        'CONTRACTNUMBERID',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function plantasDeGeneracionTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where teamId = %s and updated_at >= '%s'",
            "id, name, nivelTension, nivelTensionGroup, clvCentral, nodoP, zonaCarga, unidad, anexoElementoDelECDDeUnidad, anexoElementoDelECD, cuentaDeOrdenDelECD, contractNumberId, fechaInicioDeOperacion, sistema, tipoDeTecnologia, rmu, created_at",
            "enegence_dev.centralElectrica",
            $this->teamId,
            $this->startDate,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'ID'             => $item['id'],
                    'NAME'           => $item['name'],
                    'NIVELTENSION'   => $item['nivelTension'],
                    'SISTEMA'        => $item['sistema'],
                    'CREATED_AT'     => $item['created_at'],
                    'RMU'            => $item['rmu'],
                    'CLVCENTRAL'     => $item['clvCentral'],
                    'NODOP'          => $item['nodoP'],
                    'ZONACARGA'      => $item['zonaCarga'],
                    'UNIDAD'         => $item['unidad'],
                    'ANEXOELEMENTO'  => $item['anexoElementoDelECD'],
                    'CUENTADEORDENDELECD'=> $item['cuentaDeOrdenDelECD'],
                    'CONTRACTNUMBERID'   => $item['contractNumberId'],
                    'NIVELTENSIONGROUP'  => $item['nivelTensionGroup'],
                    'TIPODETECNOLOGIA'   => $item['tipoDeTecnologia'],
                    'FECHAINICIODEOPERACION' => $item['fechaInicioDeOperacion'],
                    'ANEXOELEMENTODELECDDEUNIDAD' => $item['anexoElementoDelECDDeUnidad'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('PLANTASGENERACION')->upsert(
                    $chunk,
                    [
                        'ID',
                        'NAME',
                    ],
                    [
                        'NIVELTENSION',
                        'SISTEMA',
                        'CREATED_AT',
                        'RMU',
                        'CLVCENTRAL',
                        'NODOP',
                        'ZONACARGA',
                        'UNIDAD',
                        'ANEXOELEMENTO',
                        'CUENTADEORDENDELECD',
                        'CONTRACTNUMBERID',
                        'NIVELTENSIONGROUP',
                        'TIPODETECNOLOGIA',
                        'FECHAINICIODEOPERACION',
                        'ANEXOELEMENTODELECDDEUNIDAD',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function centrosDeCargaTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where teamId = %s and updated_at >= '%s'",
            "id, anexoElementoDelECD, cuentaDeOrdenDelECD, rpu, rmu, grupoTarifario, nivelTension, nivelTensionGroup, nodoP, zonaCarga, sistema, divisionDistribucion, factorCarga, created_at, updated_at, contractNumberId, ceAsociada, address_cc",
            "enegence_dev.centrosCarga",
            $this->teamId,
            $this->startDate,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'ID' => $item['id'],
                    'ANEXOELEMENTODELECD' => $item['anexoElementoDelECD'],
                    'CUENTADEORDENDELECD' => $item['cuentaDeOrdenDelECD'],
                    'RPU' => $item['rpu'],
                    'RMU' => $item['rmu'],
                    'GRUPOTARIFARIO' => $item['grupoTarifario'],
                    'NIVELTENSION' => $item['nivelTension'],
                    'NIVELTENSIONGROUP' => $item['nivelTensionGroup'],
                    'NODOP' => $item['nodoP'],
                    'ZONACARGA' => $item['zonaCarga'],
                    'SISTEMA' => $item['sistema'],
                    'DIVISIONDISTRIBUCION' => $item['divisionDistribucion'],
                    'FACTORCARGA' => $item['factorCarga'],
                    'CREATED_AT' => $item['created_at'],
                    'UPDATED_AT' => $item['updated_at'],
                    'CONTRACTNUMBERID' => $item['contractNumberId'],
                    'CEASOCIADA' => $item['ceAsociada'],
                    'ADDRESSCC' => $item['address_cc'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('CENTROSDECARGA')->upsert(
                    $chunk,
                    [
                        'ID',
                        'RPU',
                    ],
                    [
                        'ANEXOELEMENTODELECD',
                        'CUENTADEORDENDELECD',
                        'RPU',
                        'RMU',
                        'GRUPOTARIFARIO',
                        'NIVELTENSION',
                        'NIVELTENSIONGROUP',
                        'NODOP',
                        'ZONACARGA',
                        'SISTEMA',
                        'DIVISIONDISTRIBUCION',
                        'FACTORCARGA',
                        'CREATED_AT',
                        'UPDATED_AT',
                        'CONTRACTNUMBERID',
                        'CEASOCIADA',
                        'ADDRESSCC',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function tipoCambioFixTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where UpdateDate >= '%s'",
            "Serie_Name, Period, Value, Units",
            "enegence_dev.tipocambio_fix",
            $this->startDate,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'NOMBREDESERIE' => $item['Serie_Name'],
                    'FECHA'         => $item['Period'],
                    'TIPOCAMBIO'    => $item['Value'],
                    'UNIDADES'      => $item['Units'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('TIPOCAMBIOFIX')->upsert(
                    $chunk,
                    [
                        'NOMBREDESERIE',
                        'FECHA',
                    ],
                    [
                        'TIPOCAMBIO',
                        'UNIDADES'
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function energiaAsignadaZonadeCargaTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where updated_at >= '%s'",
            "Zona_Carga, Fecha, Hora, Total_Cargas",
            "enegence_dev.energiaasignadazonascarga_historico",
            $this->startDate,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'ZONADECARGA' => $item['Zona_Carga'],
                    'FECHA'       => $item['Fecha'],
                    'HORA'        => $item['Hora'],
                    'TOTALCARGAS' => $item['Total_Cargas'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('ENERGIAASIGNADAZONADECARGA')->upsert(
                    $chunk,
                    [
                        'ZONADECARGA',
                        'FECHA',
                        'HORA',
                    ],
                    [
                        'TOTALCARGAS'
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function energiaGeneradaporTipodeTeconologiaTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where dateTime >= '%s'",
            "Sistema, Dia, Hora, Eolica, Fotovoltaica, Biomasa, Carboelectrica, CicloCombinado, CombustionInterna, Geotermoelectrica, Hidroelectrica, Nucleoelectrica, TermicaConvencional, TurboGas, TotalOfEnergies",
            "enegence_dev.energiaGeneradaTipoTecnologia",
            $this->startDate,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'SISTEMA' => $item['Sistema'],
                    'DIA'     => $item['Dia'],
                    'HORA'    => $item['Hora'],
                    'EOLICA'  => $item['Eolica'],
                    'FOTOVOLTAICA' => $item['Fotovoltaica'],
                    'BIOMASA' => $item['Biomasa'],
                    'CARBOELECTRICA' => $item['Carboelectrica'],
                    'CICLOCOMBINADO' => $item['CicloCombinado'],
                    'COMBUSTIONINTERNA' => $item['CombustionInterna'],
                    'GEOTERMOELECTRICA' => $item['Geotermoelectrica'],
                    'HIDROELECTRICA' => $item['Hidroelectrica'],
                    'NUCLEOELECTRICA' => $item['Nucleoelectrica'],
                    'TERMICACONVENCIONAL' => $item['TermicaConvencional'],
                    'TURBOGAS'     => $item['TurboGas'],
                    'TOTALOFENERGIES' => $item['TotalOfEnergies'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('ENERGIAGENERADAPORTIPODETECONOLOGIA')->upsert(
                    $chunk,
                    [
                        'SISTEMA',
                        'DIA',
                        'HORA',
                    ],
                    [
                        'EOLICA',
                        'FOTOVOLTAICA',
                        'BIOMASA',
                        'CARBOELECTRICA',
                        'CICLOCOMBINADO',
                        'COMBUSTIONINTERNA',
                        'GEOTERMOELECTRICA',
                        'HIDROELECTRICA',
                        'NUCLEOELECTRICA',
                        'TERMICACONVENCIONAL',
                        'TURBOGAS',
                        'TOTALOFENERGIES'
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function medicionesHorariasCCTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where createdAt >= '%s' and teamId = %s",
            "rpu, date, hour, energy, KVARh, ogEnergy, tipo, ogTipo, block, createdAt",
            "enegence_dev.measurements",
            $this->startDate,
            $this->teamId,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'RPU' => $item['rpu'],
                    'FECHA' => $item['date'],
                    'HORA' => $item['hour'],
                    'ENERGIA' => $item['energy'],
                    'KVAR' => $item['KVARh'],
                    'ENERGIAORIGINAL' => $item['ogEnergy'],
                    'TIPO' => $item['tipo'],
                    'TIPOORIGINAL' => $item['ogTipo'],
                    'BLOQUEBIP' => $item['block'],
                    'CREATED_AT' => $item['createdAt'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('MEDICIONESHORARIASCC')->upsert(
                    $chunk,
                    [
                        'RPU',
                        'FECHA',
                        'HORA',
                    ],
                    [
                        'ENERGIA',
                        'KVAR',
                        'ENERGIAORIGINAL',
                        'TIPO',
                        'TIPOORIGINAL',
                        'BLOQUEBIP',
                        'CREATED_AT',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function medicionesHorariasCETable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where createdAt >= '%s' and teamId = %s",
            "nombre, unidad, claveNodo, fecha, hora, energiakWh, blockCE, ogEnergy, tipo, ogTipo, createdAt",
            "enegence_dev.medicionesCentralElectrica",
            $this->startDate,
            $this->teamId,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'NOMBRE' => $item['nombre'],
                    'UNIDAD' => $item['unidad'],
                    'CLAVENODO' => $item['claveNodo'],
                    'FECHA' => $item['fecha'],
                    'HORA' => $item['hora'],
                    'ENERGIA' => $item['energiakWh'],
                    'BLOQUEBIP' => $item['blockCE'],
                    'ENERGIAORIGINAL' => $item['ogEnergy'],
                    'TIPO' => $item['tipo'],
                    'TIPOORIGINAL' => $item['ogTipo'],
                    'CREATED_AT' => $item['createdAt'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('MEDICIONESHORARIASCE')->upsert(
                    $chunk,
                    [
                        'NOMBRE',
                        'FECHA',
                        'HORA',
                    ],
                    [
                        'UNIDAD',
                        'CLAVENODO',
                        'ENERGIA',
                        'BLOQUEBIP',
                        'ENERGIAORIGINAL',
                        'TIPO',
                        'TIPOORIGINAL',
                        'CREATED_AT',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function ofertaDeCompraPorTipoTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM ( %s ) as Results GROUP BY sistema, fecha ORDER BY sistema, fecha",
            "sistema, fecha, SUM(ofertaCompra) as ofertaCompra, SUM(consumoGI) as consumoGI, SUM(ofertaImportacion) as ofertaImportacion",
            sprintf( // From union of 3 selects (ofertaCompra, ofertasDelGIProgramaDeConsumo, ofertasDeImportacion)
                "%s UNION ALL %s UNION ALL %s",
                sprintf(
                    "SELECT %s FROM enegence_dev.ofertaCompra WHERE fecha >= '%s' GROUP BY sistema, fecha",
                    "sistema, fecha, SUM(demandaFija) AS ofertaCompra, 0 AS consumoGI, 0 AS ofertaImportacion",
                    $this->startDate,
                ),
                sprintf(
                    "SELECT %s FROM enegence_dev.ofertasDelGIProgramaDeConsumo WHERE fechaOperacion >= '%s' GROUP BY sistema, fechaOperacion",
                    "sistema, fechaOperacion as fecha, 0 AS ofertaCompra, SUM(potenciaMedia) AS consumoGI, 0 AS ofertaImportacion",
                    $this->startDate,
                ),
                sprintf(
                    "SELECT %s FROM enegence_dev.ofertasDeImportacion WHERE fechaOperacion >= '%s' GROUP BY sistema, fechaOperacion",
                    "sistema, fechaOperacion as fecha, 0 AS ofertaCompra, 0 As consumoGI, SUM(ImportacionFija + BloquePotencia01 + BloquePotencia02 + BloquePotencia03) AS ofertaImportacion",
                    $this->startDate,
                )
            )
        );

        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'SISTEMA'      => $item['sistema'],
                    'FECHA'        => $item['fecha'],
                    'OFERTACOMPRA' => $item['ofertaCompra'],
                    'CONSUMOGI'    => $item['consumoGI'],
                    'OFERTAIMPORTACION' => $item['ofertaImportacion'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('OFERTADECOMPRAPORTIPO')->upsert(
                    $chunk,
                    [
                        'SISTEMA',
                        'FECHA',
                    ],
                    [
                        'OFERTACOMPRA',
                        'CONSUMOGI',
                        'OFERTAIMPORTACION',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function preciosGasHBTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where created_at >= '%s' and team_id = %s",
            "indexed_gas_prices.index, date, price",
            "enegence_dev.indexed_gas_prices",
            $this->startDate,
            $this->teamId,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'NOMBREDESERIE' => $item['index'],
                    'FECHA' => $item['date'],
                    'PRECIO' => $item['price'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('PRECIOSGASHB')->upsert(
                    $chunk,
                    [
                        'NOMBREDESERIE',
                        'FECHA',
                    ],
                    [
                        'PRECIO',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function preciosPMLMTRTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where updated_at >= '%s'",
            "Proceso, Sistema, Clv_Nodo, Fecha, Hora, PML, PML_ENE, PML_PER, PML_CNG",
            "enegence_dev.pmlMtr",
            $this->startDate,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'PROCESO' => $item['Proceso'],
                    'SISTEMA' => $item['Sistema'],
                    'NODOP' => $item['Clv_Nodo'],
                    'FECHA' => $item['Fecha'],
                    'HORA' => $item['Hora'],
                    'PML' => $item['PML'],
                    'COMPONENTEENERGIA' => $item['PML_ENE'],
                    'COMPONENTEPERDIDAS' => $item['PML_PER'],
                    'COMPONENTECONGESTION' => $item['PML_CNG'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 2000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('PRECIOSPML_MTR')->upsert(
                    $chunk,
                    [
                        'PROCESO',
                        'SISTEMA',
                        'NODOP',
                        'FECHA',
                        'HORA',
                    ],
                    [
                        'PML',
                        'COMPONENTEENERGIA',
                        'COMPONENTEPERDIDAS',
                        'COMPONENTECONGESTION',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function preciosPNDMDATable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where updated_at >= '%s'",
            "Proceso, Sistema, ZonaCarga, Fecha, Hora, Precio_Zonal, Componente_Energia, Componente_Perdida, Componente_Congestion",
            "enegence_dev.precioEnergiaNodoDistribuidoMda",
            $this->startDate,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'PROCESO' => $item['Proceso'],
                    'SISTEMA' => $item['Sistema'],
                    'ZONACARGA' => $item['ZonaCarga'],
                    'FECHA' => $item['Fecha'],
                    'HORA' => $item['Hora'],
                    'PRECIOZONAL' => $item['Precio_Zonal'],
                    'COMPONENTEENERGIA' => $item['Componente_Energia'],
                    'COMPONENTEPERDIDA' => $item['Componente_Perdida'],
                    'COMPONENTECONGESTION' => $item['Componente_Congestion'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('PRECIOSPND_MDA')->upsert(
                    $chunk,
                    [
                        'PROCESO',
                        'SISTEMA',
                        'ZONACARGA',
                        'FECHA',
                        'HORA',
                    ],
                    [
                        'PRECIOZONAL',
                        'COMPONENTEENERGIA',
                        'COMPONENTEPERDIDA',
                        'COMPONENTECONGESTION',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function preciosPNDMTRTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where updated_at >= '%s'",
            "Proceso, Sistema, ZonaCarga, Fecha, Hora, Precio_Zonal, Componente_Energia, Componente_Perdida, Componente_Congestion",
            "enegence_dev.precioEnergiaNodoDistribuidoMtr",
            $this->startDate,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'PROCESO' => $item['Proceso'],
                    'SISTEMA' => $item['Sistema'],
                    'ZONACARGA' => $item['ZonaCarga'],
                    'FECHA' => $item['Fecha'],
                    'HORA' => $item['Hora'],
                    'PRECIOZONAL' => $item['Precio_Zonal'],
                    'COMPONENTEENERGIA' => $item['Componente_Energia'],
                    'COMPONENTEPERDIDA' => $item['Componente_Perdida'],
                    'COMPONENTECONGESTION' => $item['Componente_Congestion'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('PRECIOSPND_MTR')->upsert(
                    $chunk,
                    [
                        'PROCESO',
                        'SISTEMA',
                        'ZONACARGA',
                        'FECHA',
                        'HORA',
                    ],
                    [
                        'PRECIOZONAL',
                        'COMPONENTEENERGIA',
                        'COMPONENTEPERDIDA',
                        'COMPONENTECONGESTION',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function preciosSPOTERCOTTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where updated_at >= '%s'",
            "Fecha, Hora, Enlace, PrecioEnlace",
            "enegence_dev.ercot",
            $this->startDate,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'FECHA'  => $item['Fecha'],
                    'HORA'   => $item['Hora'],
                    'ENLACE' => $item['Enlace'],
                    'PRECIOENLACE' => $item['PrecioEnlace'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('PRECIOSSPOTERCOT')->upsert(
                    $chunk,
                    [
                        'FECHA',
                        'HORA',
                        'ENLACE',
                    ],
                    [
                        'PRECIOENLACE',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function proyeccionesDeEnergiaTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where createdAt >= '%s' AND teamId = %s",
            "rpu, date, hour, energy, createdAt",
            "enegence_dev.proyecciones",
            $this->startDate,
            $this->teamId,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'RPU'  => $item['rpu'],
                    'FECHA'   => $item['date'],
                    'HORA' => $item['hour'],
                    'ENERGIA' => $item['energy'],
                    'CREATED_AT' => $item['createdAt'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('PROYECCIONESENERGIA')->upsert(
                    $chunk,
                    [
                        'RPU',
                        'FECHA',
                        'HORA',
                    ],
                    [
                        'ENERGIA',
                        'CREATED_AT',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function tipoDeCambioLiquidacionTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where UpdateDate >= '%s'",
            "Serie_Name, Period, Value, Units",
            "enegence_dev.tipoDeCambioLiquidacion",
            $this->startDate,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'NOMBREDESERIE'  => $item['Serie_Name'],
                    'FECHA'   => $item['Period'],
                    'TIPOCAMBIO' => $item['Value'],
                    'UNIDADES' => $item['Units'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('TIPOCAMBIOLIQUIDACION')->upsert(
                    $chunk,
                    [
                        'NOMBREDESERIE',
                        'FECHA',
                        'UNIDADES',
                    ],
                    [
                        'TIPOCAMBIO',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function liquidacionesDiariasECDTable()
    {
        // Query origin data
        $query= sprintf(
            "SELECT %s FROM %s where createdAt >= '%s' AND teamId= %s",
            "cuenta_de_orden, fecha_oper, fecha_fuf, fuecd, fuf, folio, liquidacion, ful, mes, semana, monto_total, iva, total_neto, monto_total_dif, iva_dif, total_neto_dif",
            "enegence_dev.ecd_montos_diarios",
            $this->startDate,
            $this->teamId,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'CUENTADEORDEN'  => $item['cuenta_de_orden'],
                    'FECHAOPER'   => $item['fecha_oper'],
                    'FECHAFUF' => $item['fecha_fuf'],
                    'FUECD' => $item['fuecd'],
                    'FUF' => $item['fuf'],
                    'FOLIO' => $item['folio'],
                    'LIQUIDACION' => $item['liquidacion'],
                    'FUL' => $item['ful'],
                    'MES' => $item['mes'],
                    'SEMANA' => $item['semana'],
                    'MONTOTOTAL' => $item['monto_total'],
                    'IVA' => $item['iva'],
                    'TOTALNETO' => $item['total_neto'],
                    'MONTOTOTALDIF' => $item['monto_total_dif'],
                    'IVADIF' => $item['iva_dif'],
                    'TOTALNETODIF' => $item['total_neto_dif'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('LIQUIDACIONESDIARIASECD')->upsert(
                    $chunk,
                    [
                        'CUENTADEORDEN',
                        'FUF',
                        'FUECD',
                        'FUL',
                        'LIQUIDACION',
                        'FECHAOPER',
                        'FECHAFUF',
                        'FOLIO',
                    ],
                    [
                        'MES',
                        'SEMANA',
                        'MONTOTOTAL',
                        'IVA',
                        'TOTALNETO',
                        'MONTOTOTALDIF',
                        'IVADIF',
                        'TOTALNETODIF',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function liquidacionesHorariasECDTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s %s %s FROM %s where createdAt >= '%s' AND teamId= %s",
            "id, cuenta_de_orden, fuf, folio, liquidacion, ful, anexo_elemento, nodo, fecha_oper, hora, monto_horario, precio,  potencia_mda, potencia_mtr, monto_diario, precio_gsi, fdp,  precio_rsup, precio_rnr10, precio_rr10, precio_rreg, ",
            " potencia_erc_mda, potencia_erc_mtr,  cap_prog_rsup_mda, cap_prog_rsup_mtr, cap_prog_rnr10_mda, cap_prog_rnr10_mtr, cap_prog_rr10_mda, cap_prog_rr10_mtr, cap_prog_rreg_mda, cap_prog_rreg_mtr,  zona_reserva, monto, potencia, factor, ",
            " clv_elemento_tbf, division_distribucion, tipo_tarifa, precio_tarifa,  cantidad, elemento, clv_nodo_origen, clv_nodo_retiro, pml_cng_origen, pml_cng_retiro,  factor_pond_retiro, factor_pond_origen, energia, energia_fisica, precio_sobrecobro, fuecd ",
            "enegence_dev.ecd_registros_horarios",
            $this->startDate,
            $this->teamId,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'ID'  => $item['id'],
                    'CUENTADEORDEN'  => $item['cuenta_de_orden'],
                    'FUF' => $item['fuf'],
                    'FOLIO' => $item['folio'],
                    'LIQUIDACION' => $item['liquidacion'],
                    'FUL' => $item['ful'],
                    'ANEXOELEMENTO' => $item['anexo_elemento'],
                    'NODO' => $item['nodo'],
                    'FECHAOPER'   => $item['fecha_oper'],
                    'HORA'   => $item['hora'],
                    'MONTOHORARIO'   => $item['monto_horario'],
                    'PRECIO'   => $item['precio'],
                    'POTENCIAMDA'   => $item['potencia_mda'],
                    'POTENCIAMTR'   => $item['potencia_mtr'],
                    'MONTODIARIO'   => $item['monto_diario'],
                    'PRECIOGSI'   => $item['precio_gsi'],
                    'FDP'   => $item['fdp'],
                    'PRECIORSUP'   => $item['precio_rsup'],
                    'PRCIORNR10'   => $item['precio_rnr10'],
                    'PRECIORR10'   => $item['precio_rr10'],
                    'PRECIORREG'   => $item['precio_rreg'],
                    'POTENCIAERCMDA'   => $item['potencia_erc_mda'],
                    'POTENCIAERCMTR'   => $item['potencia_erc_mtr'],
                    'CAPPROGRSUPMDA'   => $item['cap_prog_rsup_mda'],
                    'CAPPROGRSUPMTR'   => $item['cap_prog_rsup_mtr'],
                    'CAPPROGRNR10MDA'   => $item['cap_prog_rnr10_mda'],
                    'CAPPROGRNR10MTR'   => $item['cap_prog_rnr10_mtr'],
                    'CAPPROGRR10MDA'   => $item['cap_prog_rr10_mda'],
                    'CAPPROGRR10MTR'   => $item['cap_prog_rr10_mtr'],
                    'CAPPROGRREGMDA'   => $item['cap_prog_rreg_mda'],
                    'CAPPROGRREGMTR'   => $item['cap_prog_rreg_mtr'],
                    'ZONARESERVA'   => $item['zona_reserva'],
                    'MONTO'   => $item['monto'],
                    'POTENCIA'   => $item['potencia'],
                    'FACTOR'   => $item['factor'],
                    'CLAVEELEMENTOTBF'   => $item['clv_elemento_tbf'],
                    'DIVISIONDISTRIBUCION'   => $item['division_distribucion'],
                    'TIPOTARIFA'   => $item['tipo_tarifa'],
                    'PRECIOTARIFA'   => $item['precio_tarifa'],
                    'CANTIDAD'   => $item['cantidad'],
                    'ELEMENTO'   => $item['elemento'],
                    'CLAVENODOORIGEN'   => $item['clv_nodo_origen'],
                    'CLAVENODORETIRO'   => $item['clv_nodo_retiro'],
                    'PMLCONGESTIONORIGEN'   => $item['pml_cng_origen'],
                    'PMLCONGESTIONRETIRO'   => $item['pml_cng_retiro'],
                    'FACTORPONDRETIRO'   => $item['factor_pond_retiro'],
                    'FACTORPONDORIGEN'   => $item['factor_pond_origen'],
                    'ENERGIA'   => $item['energia'],
                    'ENERGIAFISICA'   => $item['energia_fisica'],
                    'PRECIOSOBRECOBRO'   => $item['precio_sobrecobro'],
                    'FUECD'   => $item['fuecd'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('LIQUIDACIONESHORARIASECD')->upsert(
                    $chunk,
                    [
                        'ID'
                    ],
                    [
                        'CUENTADEORDEN',
                        'FUF',
                        'FUECD',
                        'FUL',
                        'LIQUIDACION',
                        'HORA',
                        'FECHAOPER',
                        'FOLIO',
                        'ANEXOELEMENTO',
                        'ZONARESERVA',
                        'CLAVEELEMENTOTBF',
                        'DIVISIONDISTRIBUCION',
                        'CLAVENODOORIGEN',
                        'CLAVENODORETIRO',
                        'NODO',
                        'MONTOHORARIO',
                        'PRECIO',
                        'POTENCIAMDA',
                        'POTENCIAMTR',
                        'MONTODIARIO',
                        'PRECIOGSI',
                        'FDP',
                        'PRECIORSUP',
                        'PRCIORNR10',
                        'PRECIORR10',
                        'PRECIORREG',
                        'POTENCIAERCMDA',
                        'POTENCIAERCMTR',
                        'CAPPROGRSUPMDA',
                        'CAPPROGRSUPMTR',
                        'CAPPROGRNR10MDA',
                        'CAPPROGRNR10MTR',
                        'CAPPROGRR10MDA',
                        'CAPPROGRR10MTR',
                        'CAPPROGRREGMDA',
                        'CAPPROGRREGMTR',
                        'MONTO',
                        'POTENCIA',
                        'FACTOR',
                        'TIPOTARIFA',
                        'PRECIOTARIFA',
                        'CANTIDAD',
                        'ELEMENTO',
                        'PMLCONGESTIONORIGEN',
                        'PMLCONGESTIONRETIRO',
                        'FACTORPONDRETIRO',
                        'FACTORPONDORIGEN',
                        'ENERGIA',
                        'ENERGIAFISICA',
                        'PRECIOSOBRECOBRO',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function preciosPMLMDATable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where updated_at >= '%s'",
            "Proceso, Sistema, Clv_Nodo, Fecha, Hora, PML, PML_ENE, PML_PER, PML_CNG",
            "enegence_dev.pmlMda",
            $this->startDate,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'PROCESO'  => $item['Proceso'],
                    'SISTEMA' => $item['Sistema'],
                    'NODOP' => $item['Clv_Nodo'],
                    'FECHA' => $item['Fecha'],
                    'HORA' => $item['Hora'],
                    'PML' => $item['PML'],
                    'COMPONENTEENERGIA' => $item['PML_ENE'],
                    'COMPONENTEPERDIDAS' => $item['PML_PER'],
                    'COMPONENTECONGESTION' => $item['PML_CNG'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 2000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('PRECIOSPML_MDA')->upsert(
                    $chunk,
                    [
                        'PROCESO',
                        'SISTEMA',
                        'NODOP',
                        'FECHA',
                        'HORA',
                    ],
                    [
                        'PML',
                        'COMPONENTEENERGIA',
                        'COMPONENTEPERDIDAS',
                        'COMPONENTECONGESTION',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    // TODO
    public function facturacionCENACETable()
    {
        // Disable non grouped fiels error
        $this->sourceConnection->select("SET sql_mode = ''");

        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s %s where %s GROUP BY enegence_dev.ecd_montos_diarios.fuf",
            // SELECT fields
            sprintf(
                " %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s ",
                "enegence_dev.ecd_montos_diarios.cuenta_de_orden",
                "enegence_dev.ecd_montos_diarios.fecha_oper",
                "enegence_dev.ecd_montos_diarios.fecha_fuf",
                "enegence_dev.ecd_montos_diarios.fuecd",
                "enegence_dev.ecd_montos_diarios.fuf",
                "enegence_dev.ecd_montos_diarios.liquidacion",
                "enegence_dev.ecd_montos_diarios.mes",
                "enegence_dev.ecd_montos_diarios.semana",
                "CASE
                    WHEN enegence_dev.ecd_montos_diarios.liquidacion > 0 THEN SUM(enegence_dev.ecd_montos_diarios.monto_total_dif)
                    ELSE SUM(enegence_dev.ecd_montos_diarios.monto_total)
                END AS monto",
                "CASE 
                    WHEN enegence_dev.ecd_montos_diarios.liquidacion > 0 THEN SUM(enegence_dev.ecd_montos_diarios.iva_Dif)
                    ELSE SUM(enegence_dev.ecd_montos_diarios.iva)
                END AS iva",
                "CASE 
                    WHEN enegence_dev.ecd_montos_diarios.liquidacion > 0 THEN SUM(enegence_dev.ecd_montos_diarios.total_neto_dif)
                    ELSE SUM(enegence_dev.ecd_montos_diarios.total_neto)
                END total",
                "enegence_dev.ecd_montos_diarios.uuid",
                "enegence_dev.ecd_montos_diarios.fechaTimbrado",
                "enegence_dev.ecd_montos_diarios.emisor",
                "enegence_dev.ecd_montos_diarios.tipoDocumento",
                "enegence_dev.ecd_montos_diarios.tipoComprobante",
                "enegence_cloud.invoices.cfdi"
            ),
            "enegence_dev.ecd_montos_diarios",
            "INNER JOIN enegence_cloud.invoices ON enegence_dev.ecd_montos_diarios.uuid =  enegence_cloud.invoices.uuid",
            // Where sentenses
            sprintf(
                "teamId = %s AND enegence_cloud.invoices.created_at >= '%s' AND enegence_dev.ecd_montos_diarios.fuf LIKE '_______________P__'",
                $this->teamId,
                $this->startDate,
            ),
        );

        $sourceData =  $this->sourceConnection->select($query);

        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);

        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'CUENTADEORDEN' => $item['cuenta_de_orden'],
                    'FECHAOPER' => $item['fecha_oper'],
                    'FECHAFUF' => $item['fecha_fuf'],
                    'FUECD' => $item['fuecd'],
                    'FUF' => $item['fuf'],
                    'LIQUIDACION' => $item['liquidacion'],
                    'MES' => $item['mes'],
                    'SEMANA' => $item['semana'],
                    'MONTOTOTAL' => $item['monto'],
                    'IVA' => $item['iva'],
                    'TOTALNETO' => $item['total'],
                    'UUID' => $item['uuid'],
                    'FECHATIMBRADO' => $item['fechaTimbrado'],
                    'EMISOR' => $item['emisor'],
                    'TIPODOCUMENTO' => $item['tipoDocumento'],
                    'TIPOCOMPROBANTE' => $item['tipoComprobante'],
                    'CFDI' => $item['cfdi']

                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('FACTURACIONMEM')->upsert(
                    $chunk,
                    [
                        'CUENTADEORDEN',
                        'FECHAOPER',
                        'FECHAFUF',
                        'FUECD',
                        'FUF',
                        'LIQUIDACION',
                        'MES',
                        'SEMANA',
                    ],
                    [
                        'MONTOTOTAL',
                        'IVA',
                        'TOTALNETO',
                        'UUID',
                        'FECHATIMBRADO',
                        'EMISOR',
                        'TIPODOCUMENTO',
                        'TIPOCOMPROBANTE',
                        'CFDI'
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function facturacionUCTable()
    {
        $cenaceInvoiceHelper = new CENACEInvoicesHelper();
        $params = [
            "tipoDeFecha" => "fuf",
            "usuario" => config('app.CENACE_USER'),
            "password" => config('app.CENACE_PASSWORD'),
            "sistema" => "SIN",
            "participante" =>config('app.CENACE_PARTICIPANT'),
            "subcuenta" => null
        ];

        // Set custom period in order to apply SMART rules for ecdcenaceTask
        $startDate = date('Y-m-d', strtotime('-3 days'));
        $endDate = date('Y-m-d', strtotime('-1 days'));
        $dateDataArray = $cenaceInvoiceHelper->getFacturasEmitidas($params, $startDate, $endDate);

        // Prepare  insertion
        foreach ($dateDataArray as $dateData) {
            $data = $dateData['data']  ?? [];
            foreach ($data as $itemData) {
                $data = [
                    'FILENAME' => $itemData['fileName'],
                    'SUBCUENTA' => $itemData['subcuenta'],
                    'FILECONTENTXML' => $itemData['fileContent'],
                    'FILECONTENTPDF' => $itemData['fileContentPdf']
                ];

                try {
                    // Prepare base query with where conditions
                    $query = $this->targetConnection->table('FACTURASCENANCE')
                        ->where('FILENAME', $data['FILENAME'])
                        ->where('SUBCUENTA', $data['SUBCUENTA']);
                    
                    if ($query->exists()) {
                        // Update existing record
                        $query->update($data);
                    } else {
                        // Insert new record
                        $this->targetConnection->table('FACTURASCENANCE')->insert($data);
                    }
                } catch (Exception $e) {
                    error_log(
                        date("[Y-m-d H:i:s]") . $e->getMessage() . PHP_EOL,
                        3,
                        storage_path('logs/table_errors.log')
                    );
                }
            }
        }
    }

    public function ecdsCENACETable()
    {
        $cenaceInvoiceHelper = new CENACEInvoicesHelper();
        $params = [
            "tipoDeFecha" => "fuf",
            "usuario" => config('app.CENACE_USER'),
            "password" => config('app.CENACE_PASSWORD'),
            "sistema" => "SIN",
            "participante" =>config('app.CENACE_PARTICIPANT'),
            "subcuenta" => null
        ];

        // Set custom period in order to apply SMART rules for ecdcenaceTask
        $startDate = date('Y-m-d', strtotime('-9 days'));
        $endDate = date('Y-m-d', strtotime('-5 days'));

        $dates = [];

        $currentDate = $startDate;
        while ($currentDate <= $endDate) {
            $dates[] = $currentDate;
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }

        foreach ($dates as $date) {
            $ecdsByDateListResult = $cenaceInvoiceHelper->getCENACEECDs($params, $date, false);
            if (is_string($ecdsByDateListResult)) {
                continue;
            }

            if ('true' !== $ecdsByDateListResult['statusValue']) {
                continue;
            }

            foreach ($ecdsByDateListResult['data'] as $dateEcds) {
                $data = [
                    'FILENAME' => $dateEcds['fileName'],
                    'SUBCUENTA' => $dateEcds['subcuenta'],
                    'FILECONTENTXML' => $dateEcds['fileContent']
                ];

                try {
                    // Prepare base query with where conditions
                    $query = $this->targetConnection->table('ECDSCENANCE')
                        ->where('FILENAME', $data['FILENAME'])
                        ->where('SUBCUENTA', $data['SUBCUENTA']);
                    
                    if ($query->exists()) {
                        // Update existing record
                        $query->update($data);
                    } else {
                        // Insert new record
                        $this->targetConnection->table('ECDSCENANCE')->insert($data);
                    }
                } catch (Exception $e) {
                    error_log(
                        date("[Y-m-d H:i:s]") . $e->getMessage() . PHP_EOL,
                        3,
                        storage_path('logs/table_errors.log')
                    );
                }

            }
        }
    }

    public function nodosPTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where updatedAt >= '%s'",
            "Sistema, CentroControlRegional, ZonaCarga, Clave, NombreNodo, NivelTension, TipoCargaDM, TipoCargaIM, TipoGeneracionDM, TipoGeneracionIM, ZonaOpeTrans, GerenciaRegTrans, ZonaDistribucion, GerenciaDivDist, EntidadInegi, Municipio, RegionTransmision",
            "enegence_dev.nodosPAccumulative",
            $this->startDate,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'SISTEMA' => $item['Sistema'],
                    'CENTROCONTROLREGIONAL' => $item['CentroControlRegional'],
                    'ZONACARGA' => $item['ZonaCarga'],
                    'CLAVE' => $item['Clave'],
                    'NOMBRENODO' => $item['NombreNodo'],
                    'NIVELTENSION' => $item['NivelTension'],
                    'TIPOCARGADM' => $item['TipoCargaDM'],
                    'TIPOCARGAIM' => $item['TipoCargaIM'],
                    'TIPOGENERACIONDM' => $item['TipoGeneracionDM'],
                    'TIPOGENERACIONIM' => $item['TipoGeneracionIM'],
                    'ZONAOPETRANS' => $item['ZonaOpeTrans'],
                    'GERENCIAREGTRANS' => $item['GerenciaRegTrans'],
                    'ZONADISTRIBUCION' => $item['ZonaDistribucion'],
                    'GERENCIADIVDIST' => $item['GerenciaDivDist'],
                    'ENTIDADINEGI' => $item['EntidadInegi'],
                    'MUNICIPIO' => $item['Municipio'],
                    'REGIONTRANSMISION' => $item['RegionTransmision'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('NODOSP')->upsert(
                    $chunk,
                    [
                        'SISTEMA',
                        'CENTROCONTROLREGIONAL',
                        'ZONACARGA',
                        'CLAVE',
                        'NOMBRENODO',
                        'ZONAOPETRANS',
                        'ENTIDADINEGI',
                        'MUNICIPIO',
                        'REGIONTRANSMISION',
                    ],
                    [
                        'GERENCIADIVDIST',
                        'NIVELTENSION',
                        'TIPOCARGADM',
                        'TIPOCARGAIM',
                        'TIPOGENERACIONDM',
                        'TIPOGENERACIONIM',
                        'GERENCIAREGTRANS',
                        'ZONADISTRIBUCION'
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function ofertasCompraEnergiaTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where created_at >= '%s' AND teamId = %s",
            "proceso, usuarioCalificado, anexoElementoDelECD, nodo, participante, fecha, hora, demandaFijaMw, estatusEnvio",
            "enegence_dev.ofertasGeneradasCompra",
            $this->startDate,
            $this->teamId,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'PROCESO' => $item['proceso'],
                    'USUARIOCALIFICADO' => $item['usuarioCalificado'],
                    'ANEXOELEMENTO' => $item['anexoElementoDelECD'],
                    'NODOP' => $item['nodo'],
                    'PARTICIPANTE' => $item['participante'],
                    'FECHA' => $item['fecha'],
                    'HORA' => $item['hora'],
                    'OFERTACOMPRAENERGIA' => $item['demandaFijaMw'],
                    'ESTATUSENVIO' => $item['estatusEnvio'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('OFERTASCOMPRAENERGIA')->upsert(
                    $chunk,
                    [
                        'PROCESO',
                        'USUARIOCALIFICADO',
                        'ANEXOELEMENTO',
                        'NODOP',
                        'PARTICIPANTE',
                        'FECHA',
                        'HORA',
                    ],
                    [
                        'OFERTACOMPRAENERGIA',
                        'ESTATUSENVIO',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function ofertasVentaEnergiaTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where created_at >= '%s' AND teamId = %s",
            "tipoOferta, clvCentral, clvUnidad, proceso, estatusAsignacion, clvParticipante, fecha, fechaFinal, hora, vigencia, estatusEnvio",
            "enegence_dev.ofertasGeneradasVenta",
            $this->startDate,
            $this->teamId,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'TIPOOFERTA' => $item['tipoOferta'],
                    'CENTRALELECTRICA' => $item['clvCentral'],
                    'UNIDADCENTRAL' => $item['clvUnidad'],
                    'PROCESOMERCADO' => $item['proceso'],
                    'ESTATUSASIGNACION' => $item['estatusAsignacion'],
                    'PARTICIPANTE' => $item['clvParticipante'],
                    'FECHAINICIO' => $item['fecha'],
                    'FECHAFINAL' => $item['fechaFinal'],
                    'HORA' => $item['hora'],
                    'VIGENCIA' => $item['vigencia'],
                    'ESTATUSENVIO' => $item['estatusEnvio'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('OFERTASVENTAENERGIA')->upsert(
                    $chunk,
                    [
                        'PROCESOMERCADO',
                        'PARTICIPANTE',
                        'FECHAINICIO',
                        'FECHAFINAL',
                        'HORA',
                        'TIPOOFERTA',
                        'CENTRALELECTRICA',
                        'UNIDADCENTRAL',
                    ],
                    [
                        'ESTATUSASIGNACION',
                        'VIGENCIA',
                        'ESTATUSENVIO',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function ofertasVentaPorTipoTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM ( %s ) AS Result GROUP BY Sistema, FechaOperacion ORDER BY Sistema, FechaOperacion;",
            " Sistema, FechaOperacion, SUM(LimiteDespacho_Max_Termica) AS LimiteDespacho_Max_Termica, SUM(LimiteDespacho_Max_Hidro) AS LimiteDespacho_Max_Hidro, SUM(PronosticoMW) AS PronosticoMW, SUM(PotenciaMedia_NoDespachable) AS PotenciaMedia_NoDespachable, SUM(PotenciaMedia_GIPG) AS PotenciaMedia_GIPG, SUM(ExportacionFija + BloquePotencia01 + BloquePotencia02 +  BloquePotencia03) AS ofertaExportacion",
            sprintf(
                " %s UNION ALL %s UNION ALL %s UNION ALL %s UNION ALL %s UNION ALL %s ",
                sprintf(
                    "SELECT Sistema, Fecha AS FechaOperacion, SUM(LimiteDespacho_Max) AS LimiteDespacho_Max_Termica, 0 AS LimiteDespacho_Max_Hidro, 0 AS PronosticoMW, 0 AS PotenciaMedia_NoDespachable, 0 AS PotenciaMedia_GIPG, 0 AS ExportacionFija, 0 AS BloquePotencia01, 0 AS BloquePotencia02, 0 AS BloquePotencia03 FROM enegence_dev.ofertaVentaTermica WHERE Fecha >= '%s' GROUP BY Sistema, Fecha",
                    $this->startDate
                ),
                sprintf(
                    "SELECT Sistema, Fecha AS FechaOperacion, 0 AS LimiteDespacho_Max_Termica, SUM(LimiteDespacho_Max) AS LimiteDespacho_Max_Hidro, 0 AS PronosticoMW, 0 AS PotenciaMedia_NoDespachable, 0 AS PotenciaMedia_GIPG, 0 AS ExportacionFija, 0 AS BloquePotencia01, 0 AS BloquePotencia02, 0 AS BloquePotencia03 FROM enegence_dev.ofertaVentaHidroelectrica WHERE Fecha >= '%s' GROUP BY Sistema, Fecha",
                    $this->startDate
                ),
                sprintf(
                    "SELECT Sistema, Fecha AS FechaOperacion, 0 AS LimiteDespacho_Max_Termica, 0 AS LimiteDespacho_Max_Hidro, SUM(PronosticoMW) AS PronosticoMW, 0 AS PotenciaMedia_NoDespachable, 0 AS PotenciaMedia_GIPG, 0 AS ExportacionFija, 0 AS BloquePotencia01, 0 AS BloquePotencia02, 0 AS BloquePotencia03 FROM enegence_dev.ofertaVentaRecursoIntermitenteDespachable WHERE Fecha >= '%s' GROUP BY Sistema, Fecha",
                    $this->startDate
                ),
                sprintf(
                    "SELECT Sistema, Fecha AS FechaOperacion, 0 AS LimiteDespacho_Max_Termica, 0 AS LimiteDespacho_Max_Hidro, 0 AS PronosticoMW, SUM(PotenciaMedia) AS PotenciaMedia_NoDespachable, 0 AS PotenciaMedia_GIPG, 0 AS ExportacionFija, 0 AS BloquePotencia01, 0 AS BloquePotencia02, 0 AS BloquePotencia03 FROM enegence_dev.ofertaVentaNoDespachable WHERE Fecha >= '%s' GROUP BY Sistema, Fecha",
                    $this->startDate
                ),
                sprintf(
                    "SELECT Sistema, FechaOperacion, 0 AS LimiteDespacho_Max_Termica, 0 AS LimiteDespacho_Max_Hidro, 0 AS PronosticoMW, 0 AS PotenciaMedia_NoDespachable, SUM(PotenciaMedia) AS PotenciaMedia_GIPG, 0 AS ExportacionFija, 0 AS BloquePotencia01, 0 AS BloquePotencia02, 0 AS BloquePotencia03 FROM enegence_dev.ofertasDelGIProgramaDeGeneracion WHERE FechaOperacion >= '%s' GROUP BY Sistema, FechaOperacion",
                    $this->startDate
                ),
                sprintf(
                    "SELECT Sistema, FechaOperacion, 0 AS LimiteDespacho_Max_Termica, 0 AS LimiteDespacho_Max_Hidro, 0 AS PronosticoMW, 0 AS PotenciaMedia_NoDespachable, 0 AS PotenciaMedia_GIPG, SUM(ExportacionFija) AS ExportacionFija, SUM(BloquePotencia01) AS BloquePotencia01, SUM(BloquePotencia02) AS BloquePotencia02, SUM(BloquePotencia03) AS BloquePotencia03 FROM enegence_dev.ofertasDeExportacion WHERE FechaOperacion >= '%s' GROUP BY Sistema, FechaOperacion",
                    $this->startDate
                ),
            )
        );

        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'SISTEMA'              => $item['Sistema'],
                    'FECHA'                => $item['FechaOperacion'],
                    'OFERTATERMICA'        => $item['LimiteDespacho_Max_Termica'],
                    'OFERTAHIDROELECTRICA' => $item['LimiteDespacho_Max_Hidro'],
                    'OFERTARENOVABLE'      => $item['PronosticoMW'],
                    'OFERTANODESPACHABLE'  => $item['PotenciaMedia_NoDespachable'],
                    'PROGRAMAGENERACIONGI' => $item['PotenciaMedia_GIPG'],
                    'OFERTAEXPORTACION'    => $item['ofertaExportacion'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('OFERTASVENTAPORTIPO')->upsert(
                    $chunk,
                    [
                        'SISTEMA',
                        'FECHA',
                    ],
                    [
                        'OFERTATERMICA',
                        'OFERTAHIDROELECTRICA',
                        'OFERTARENOVABLE',
                        'OFERTANODESPACHABLE',
                        'PROGRAMAGENERACIONGI',
                        'OFERTAEXPORTACION',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }

    public function calculoDeContratosTable()
    {
        // Query origin data
        $query = sprintf(
            "SELECT %s FROM %s where created_at >= '%s' AND teamId = %s",
            "contractCalculationNumber, startDateParam, endDateParam, centrosDeCargaParam, energyAmount, capacityAmount, cleanEnergyCertificateAmount, regulatedTariffAmount, associatedProductsAmount, marketCostAmount, othersAmount, subtotal, iva, total",
            "enegence_dev.calculationsResults",
            $this->startDate,
            $this->teamId,
        );
        $sourceData =  $this->sourceConnection->select($query);
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);
        error_log(
            date("[Y-m-d H:i:s]") . $query . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );

        // Exit function if there is no data to migrate
        if (count($sourceDataArray) == 0) {
            return;
        }

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'CONTRATO' => $item['contractCalculationNumber'],
                    'FECHAINICIO' => $item['startDateParam'],
                    'FECHAFIN' => $item['endDateParam'],
                    'CENTROSDECARGA' => $item['centrosDeCargaParam'],
                    'ENERGIA' => $item['energyAmount'],
                    'POTENCIA' => $item['capacityAmount'],
                    'CELS' => $item['cleanEnergyCertificateAmount'],
                    'TARIFASREGULADAS' => $item['regulatedTariffAmount'],
                    'PRODUCTOSASOCIADOS' => $item['associatedProductsAmount'],
                    'COSTOSDEMERCADO' => $item['marketCostAmount'],
                    'OTROS' => $item['othersAmount'],
                    'SUBTOTAL' => $item['subtotal'],
                    'IVA' => $item['iva'],
                    'TOTAL' => $item['total'],
                ];
            },
            $sourceDataArray
        );

        // Parce to Chunks for optimization.
        $chunks = array_chunk($targetDataArray, 1000);

        // Run insert query in target connection transaction
        DB::connection('oracle')->transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $this->targetConnection->table('CALCULOCONTRATOS')->upsert(
                    $chunk,
                    [
                        'CONTRATO',
                        'FECHAINICIO',
                        'FECHAFIN',
                        'CENTROSDECARGA',
                    ],
                    [
                        'ENERGIA',
                        'POTENCIA',
                        'CELS',
                        'TARIFASREGULADAS',
                        'PRODUCTOSASOCIADOS',
                        'COSTOSDEMERCADO',
                        'OTROS',
                        'SUBTOTAL',
                        'IVA',
                        'TOTAL',
                    ]
                );
            }
        });
        error_log(
            date("[Y-m-d H:i:s]") . " migrated ". count($targetDataArray).' rows.' . PHP_EOL . PHP_EOL,
            3,
            storage_path('logs/tables.log')
        );
    }
}