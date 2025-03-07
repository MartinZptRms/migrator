<?php

namespace App\Http\Repositories;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class KualionDataRepository
{
    private $sourceConnection;
    private $targetConnection;
    private $teamId = 155;
    private $startDate;

    public function __construct()
    {
        $this->sourceConnection = DB::connection('mysql');
        $this->targetConnection = DB::connection('oracle');
        $this->startDate = Carbon::now()->subWeek()->toDateString();
    }

    public function contrapartesTable()
    {
        // Query origin data
        $sourceData =  $this->sourceConnection->select(
            sprintf(
                "SELECT %s FROM %s where teamId = %s and created_at >= %s",
                "alias, name, rfc, email, municipio, contractNumber, contractNumberId",
                "enegence_cloud.invoiceRecipients",
                $this->teamId,
                $this->startDate,
            )
        );
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'SHORTNAME'      => $item['alias'],
                    'NAME'           => $item['name'],
                    'RFC'            => $item['rfc'],
                    'EMAIL'          => $item['email'],
                    'MUNICIPIO'      => $item['municipio'],
                    'CONTRACTNUMBER' => $item['contractNumber'],
                    'CONTRACTNUMBERID' => $item['contractNumberId'],
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
                        'NAME',
                        'RFC',
                    ],
                    [
                        'SHORTNAME',
                        'NAME',
                        'RFC',
                        'EMAIL',
                        'MUNICIPIO',
                        'CONTRACTNUMBER',
                        'CONTRACTNUMBERID'
                    ]
                );
            }
        });
    }

    public function diccionarioDeFoliosTable()
    {
        // Query origin data
        $sourceData =  $this->sourceConnection->select(
            sprintf(
                "SELECT %s FROM %s",
                "folio, concepto, mercado, clasificacion, descripcion",
                "enegence_dev.ecd_conceptos",
            )
        );
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);

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
    }

    public function listadoDeContratosTable()
    {
        // Query origin data
        $sourceData =  $this->sourceConnection->select(
            sprintf(
                "SELECT %s FROM %s where teamId = %s and updated_at >= %s",
                "id, contractNumber, contractNumberId, name, clients, centrosDeCarga, centralesElectricas, created_at, updated_at, contract_template, vigenciaStartDate, vigenciaEndDate, currency, demandaContratada, energiaContratada, frecuenciaDeCalculo",
                "enegence_cloud.contracts",
                $this->teamId,
                $this->startDate,
            )
        );
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'ID'      => $item['id'],
                    'CONTRACTNUMBER'   => $item['contractNumber'],
                    'CONTRACTNUMBERID' => $item['contractNumberId'],
                    'NAME'             => $item['name'],
                    'CLIENT'           => $item['clients'],
                    'CENTRODECARGA'    => $item['centrosDeCarga'],
                    'CREATED_AT'       => $item['created_at'],
                    'UPDATED_AT'       => $item['updated_at'],
                    'INICIOVIGENCIA'   => $item['vigenciaStartDate'],
                    'FINVIGENCIA'      => $item['vigenciaEndDate'],
                    'MONEDA'           => $item['currency'],
                    'DEMANDA'          => $item['demandaContratada'],
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
                        'CONTRACTNUMBER',
                    ],
                    [
                        'CONTRACTNUMBERID',
                        'NAME',
                        'CLIENT',
                        'CENTRODECARGA',
                        'CREATED_AT',
                        'UPDATED_AT',
                        'INICIOVIGENCIA',
                        'FINVIGENCIA',
                        'MONEDA',
                        'DEMANDA',
                        'CONTRACT_TEMPLATE',
                        'ENERGIACONTRATADA',
                        'CENTRALESELECTRICAS',
                        'FRECUENCIADECALCULO'
                    ]
                );
            }
        });
    }

    public function plantasDeGeneracionTable()
    {
        // Query origin data
        $sourceData =  $this->sourceConnection->select(
            sprintf(
                "SELECT %s FROM %s where teamId = %s and updated_at >= %s",
                "id, name, nivelTension, nivelTensionGroup, clvCentral, nodoP, zonaCarga, unidad, anexoElementoDelECDDeUnidad, anexoElementoDelECD, cuentaDeOrdenDelECD, contractNumberId, fechaInicioDeOperacion, sistema, tipoDeTecnologia, rmu, created_at",
                "enegence_dev.centralElectrica",
                $this->teamId,
                $this->startDate,
            )
        );
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);

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
    }

    public function tipoCambioFixTable()
    {
        // Query origin data
        $sourceData =  $this->sourceConnection->select(
            sprintf(
                "SELECT %s FROM %s where UpdateDate >= %s",
                "Serie_Name, Period, Value, Units",
                "enegence_dev.tipocambio_fix",
                $this->startDate,
            )
        );
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);

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
    }

    public function energiaAsignadaZonadeCargaTable()
    {
        // Query origin data
        $sourceData =  $this->sourceConnection->select(
            sprintf(
                "SELECT %s FROM %s where updated_at >= %s",
                "Zona_Carga, Fecha, Hora, Total_Cargas",
                "enegence_dev.energiaasignadazonascarga_historico",
                $this->startDate,
            )
        );
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);

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
    }

    public function energiaGeneradaporTipodeTeconologiaTable()
    {
        // Query origin data
        $sourceData =  $this->sourceConnection->select(
            sprintf(
                "SELECT %s FROM %s where Dia >= %s",
                "Sistema, Dia, Hora, Eolica, Fotovoltaica, Biomasa, Carboelectrica, CicloCombinado, CombustionInterna, Geotermoelectrica, Hidroelectrica, Nucleoelectrica, TermicaConvencional, TurboGas, TotalOfEnergies",
                "enegence_dev.energiaGeneradaTipoTecnologia",
                $this->startDate,
            )
        );
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);

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
    }

    public function medicionesHorariasCCTable()
    {
        // Query origin data
        $sourceData =  $this->sourceConnection->select(
            sprintf(
                "SELECT %s FROM %s where date >= %s and teamId = %s",
                "rpu, date, hour, energy, ogEnergy, tipo, ogTipo, block, createdAt",
                "enegence_dev.measurements",
                $this->startDate,
                $this->teamId,
            )
        );
        // Parse to Array
        $sourceDataArray = json_decode(json_encode($sourceData), true);

        // Map to target database fields
        $targetDataArray = array_map(
            function ($item) {
                return [
                    'RPU' => $item['rpu'],
                    'FECHA' => $item['date'],
                    'HORA' => $item['hour'],
                    'ENERGIA' => $item['energy'],
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
                        'ENERGIAORIGINAL',
                        'TIPO',
                        'TIPOORIGINAL',
                        'BLOQUEBIP',
                        'CREATED_AT',
                    ]
                );
            }
        });
    }

    public function medicionesHorariasCETable()
    {
    }

    public function ofertadeCompraporTipoTable()
    {
    }

    public function preciosGasHBTable()
    {
    }

    public function preciosPMLMTRTable()
    {
    }

    public function preciosPNDMDATable()
    {
    }

    public function preciosPNDMTRTable()
    {
    }

    public function preciosSPOTERCOTTable()
    {
    }

    public function proyeccionesDeEnergiaTable()
    {
    }

    public function tipoDeCambioLiquidacionTable()
    {
    }

    public function liquidacionesDiariasECDTable()
    {
    }

    public function liquidacionesHorariasECDTable()
    {
    }

    public function preciosPMLMDATable()
    {
    }

    public function facturaciónCENACETable()
    {
    }

    public function facturacionUCTable()
    {
    }

    public function nodosPTable()
    {
    }

    public function ofertasCompraEnergiaTable()
    {
    }

    public function ofertasVentaEnergiaTable()
    {
    }

    public function ofertasVentaPorTipoTable()
    {
    }

    public function calculoDeContratosTable()
    {
    }
}