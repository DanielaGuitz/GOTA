<?php

namespace App\Controllers;

use App\Models\ClientesModel;
use App\Models\ContadoresModel;
use App\Models\LecturasModel;
use App\Models\PagosModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $clientesModel = new ClientesModel();
        $contadoresModel = new ContadoresModel();
        $lecturasModel = new LecturasModel();
        $pagosModel = new PagosModel();

        // Obtener datos reales de la BD
        $totalClientes = $clientesModel->where('activo', 1)->countAllResults();
        $totalContadores = $contadoresModel->where('activo', 1)->countAllResults();

        // Consumo total del mes actual
        $consumoTotal = $lecturasModel
            ->selectSum('consumo')
            ->where('MONTH(fecha_lectura)', date('m'))
            ->where('YEAR(fecha_lectura)', date('Y'))
            ->get()
            ->getRow()
            ->consumo ?? 0;

        // Ingresos del mes
        $ingresosMes = $pagosModel
            ->selectSum('monto')
            ->where('MONTH(fecha_pago)', date('m'))
            ->where('YEAR(fecha_pago)', date('Y'))
            ->where('estado', 'Completado')
            ->get()
            ->getRow()
            ->monto ?? 0;

        // Lecturas pendientes (sin pago registrado)
        $lecturasPendientes = $lecturasModel
            ->select('lecturas.*, clientes.nombre as cliente_nombre, contadores.codigo as contador_codigo')
            ->join('contadores', 'contadores.id = lecturas.contador_id')
            ->join('clientes', 'clientes.id = contadores.cliente_id')
            ->whereNotIn('lecturas.id', function($builder) {
                return $builder->select('lectura_id')->from('pagos')->where('estado', 'Completado');
            })
            ->findAll();

        $montoPendiente = array_sum(array_column($lecturasPendientes, 'monto'));

        $data = [
            'totalClientes' => $totalClientes,
            'totalContadores' => $totalContadores,
            'consumoTotal' => $consumoTotal,
            'ingresosMes' => $ingresosMes,
            'lecturasPendientes' => count($lecturasPendientes),
            'montoPendiente' => $montoPendiente,
            'lecturas' => $lecturasPendientes,
            // Datos de ejemplo para analíticas (adaptar según necesidades)
            'tasaEntrega' => 100,
            'tasaApertura' => 23,
            'reenvios' => 16,
            'reportesAbuso' => 2,
            'clientesNuevos' => $clientesModel->where('activo', 1)->where('MONTH(fecha_registro)', date('m'))->countAllResults(),
            'pagosMes' => $pagosModel->where('MONTH(fecha_pago)', date('m'))->where('YEAR(fecha_pago)', date('Y'))->where('estado', 'Completado')->countAllResults(),
            'lecturasMes' => $lecturasModel->where('MONTH(fecha_lectura)', date('m'))->where('YEAR(fecha_lectura)', date('Y'))->countAllResults(),
            'clientesPorcentaje' => 12, // Calcular con datos reales
            'consumoPorcentaje' => 8,
            'ingresosPorcentaje' => 5,
            'pendientesPorcentaje' => 3,
            'inicio' => 1,
            'fin' => min(5, count($lecturasPendientes)),
            'totalRegistros' => count($lecturasPendientes),
            'paginaActual' => 1,
            'totalPaginas' => max(1, ceil(count($lecturasPendientes) / 5)),
        ];

        return view('dashboard/index', $data);
    }
}