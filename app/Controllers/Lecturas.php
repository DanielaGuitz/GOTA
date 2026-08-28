<?php

namespace App\Controllers;

use App\Models\LecturaModel;

class Lecturas extends BaseController
{
    protected LecturaModel $lecturaModel;

    public function __construct()
    {
        $this->lecturaModel = new LecturaModel();
    }

    /**
     * Muestra el formulario para registrar una nueva lectura de un contador.
     * Ruta esperada: /lecturas/nueva/{contador_id}
     */
    public function nueva(int $contadorId)
    {
        // Buscamos la última lectura registrada para ese contador,
        // según el flujo de negocio: "el sistema muestra la última lectura
        // registrada (o 0 si es la primera)".
        $ultimaLectura = $this->lecturaModel->obtenerUltimaLectura($contadorId);
        $lecturaAnterior = $ultimaLectura['lectura_actual'] ?? 0;

        $data = [
            'contador_id'      => $contadorId,
            'lectura_anterior' => $lecturaAnterior,
        ];

        return view('lecturas/formulario', $data);
    }

    /**
     * Procesa el formulario: calcula consumo, busca tarifa vigente,
     * calcula el monto, y guarda el registro.
     * Ruta esperada: POST /lecturas/guardar
     */
    public function guardar()
    {
        $contadorId     = (int) $this->request->getPost('contador_id');
        $lecturaAnterior = (float) $this->request->getPost('lectura_anterior');
        $lecturaActual   = (float) $this->request->getPost('lectura_actual');
        $fecha           = date('Y-m-d'); // fecha de hoy, cuando se toma la lectura

        // Validación simple: la lectura actual no puede ser menor a la anterior
        if ($lecturaActual < $lecturaAnterior) {
            return redirect()->back()->withInput()
                ->with('error', 'La lectura actual no puede ser menor a la anterior.');
        }

        $consumo = $lecturaActual - $lecturaAnterior;

        // --- ZONA TEMPORAL: aquí va la tarifa vigente ---
        // Cuando exista la tabla "tarifas" y su modelo (TarifaModel),
        // esto se reemplaza por algo como:
        //   $tarifaModel = new TarifaModel();
        //   $tarifa = $tarifaModel->obtenerTarifaVigente($fecha);
        //   $tarifaId = $tarifa['id'];
        //   $montoPorUnidad = $tarifa['monto_por_unidad'];
        $tarifaId       = null; // luego será $tarifa['id']
        $montoPorUnidad = 5.00; // valor de prueba, en Quetzales por unidad
        // --- FIN ZONA TEMPORAL ---

        $monto = $consumo * $montoPorUnidad;

        // Por ahora, el usuario_lector_id lo dejamos fijo en 1
        // (el usuario de prueba que crea el seeder de AUTH).
        // Cuando integres con el login real, esto se reemplaza por
        // el id del usuario en sesión: session()->get('usuario_id')
        $usuarioLectorId = 1;

        $data = [
            'contador_id'       => $contadorId,
            'lectura_anterior'  => $lecturaAnterior,
            'lectura_actual'    => $lecturaActual,
            'consumo'           => $consumo,
            'tarifa_id'         => $tarifaId,
            'monto'             => $monto,
            'fecha'             => $fecha,
            'usuario_lector_id' => $usuarioLectorId,
        ];

        $lecturaId = $this->lecturaModel->insert($data);

        if (! $lecturaId) {
            return redirect()->back()->withInput()
                ->with('error', 'No se pudo guardar la lectura. Revisa los datos.');
        }

        // Redirige directo al recibo imprimible de la lectura recién creada
        return redirect()->to('/lecturas/recibo/' . $lecturaId);
    }

    /**
     * Muestra el recibo imprimible de una lectura ya registrada.
     * Ruta esperada: /lecturas/recibo/{id}
     */
    public function recibo(int $id)
    {
        $lectura = $this->lecturaModel->find($id);

        if (! $lectura) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('lecturas/recibo', ['lectura' => $lectura]);
    }
}