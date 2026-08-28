<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturaModel extends Model
{
    // Nombre de la tabla que maneja este modelo
    protected $table = 'lecturas';

    // Nombre de la llave primaria
    protected $primaryKey = 'id';

    // Le decimos a CodeIgniter que llene created_at/updated_at automáticamente
    protected $useTimestamps = true;

    // Formato en que se guardan esas fechas
    protected $dateFormat = 'datetime';

    // Campos que SÍ se pueden llenar desde $model->save($data) o insert()/update().
    // Es una medida de seguridad: cualquier campo que no esté aquí, CodeIgniter
    // lo ignora aunque venga en el array de datos (evita inyección de campos raros).
    protected $allowedFields = [
        'contador_id',
        'lectura_anterior',
        'lectura_actual',
        'consumo',
        'tarifa_id',
        'monto',
        'fecha',
        'usuario_lector_id',
    ];

    // Reglas de validación básicas. Se aplican automáticamente cuando usas
    // $model->insert() o $model->save(), y bloquean el guardado si fallan.
    protected $validationRules = [
        'contador_id'       => 'required|integer',
        'lectura_actual'    => 'required|decimal',
        'fecha'             => 'required|valid_date',
        'usuario_lector_id' => 'required|integer',
    ];

    protected $validationMessages = [
        'lectura_actual' => [
            'required' => 'Debes ingresar la lectura actual del contador.',
        ],
    ];

    /**
     * Busca la última lectura registrada para un contador específico.
     * Esto es lo que necesita el Lector para ver "lectura anterior" antes
     * de ingresar la nueva, según el flujo de negocio del proyecto.
     *
     * @param int $contadorId
     * @return array|null  El registro de la última lectura, o null si es la primera vez
     */
    public function obtenerUltimaLectura(int $contadorId)
    {
        return $this->where('contador_id', $contadorId)
                    ->orderBy('fecha', 'DESC')
                    ->first(); // trae solo el registro más reciente
    }
}
