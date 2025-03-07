<?php

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;

class KualionDataRepository
{
    private $connection;

    public function __construct()
    {
        $this->connection = DB::connection('oracle');
    }

    public function contrapartesTable()
    {
    }

    public function diccionarioDeFoliosTable()
    {
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