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
    }

    public function plantasDeGeneracionTable()
    {
    }

    public function tipoCambioFixTable()
    {
    }

    public function energiaAsignadaZonadeCargaTable()
    {
    }

    public function energiaGeneradaporTipodeTeconologiaTable()
    {
    }

    public function medicionesHorariasCCTable()
    {
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