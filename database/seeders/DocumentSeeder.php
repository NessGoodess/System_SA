<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('documents')->insert([
            [
                'id' => Str::uuid(),
                'title' => 'Solicitud de Baja de Bienes',
                'reference_number' => 'Doc-0024',
                'description' => 'Solicitud de baja de bienes de la Secretaria de Finanzas.',
                'category_id' => 1,
                'status_id' => 1,
                'sender_department_id' => 6,
                'receiver_department_id' => 4,
                'issue_date' => $now,
                'priority' => 'High',
                'created_by' => 1,
                'received_date' => $now,
                'created_at' => $now,
            ],

            [
                'id' => Str::uuid(),
                'title' => 'Expediente de Fulano',
                'reference_number' => 'Doc-0025',
                'description' => 'Expediente completo de Fulano.',
                'category_id' => 3, // Expediente
                'status_id' => 2, // En Tramite
                'sender_department_id' => 7, // Facultad de Sistemas Biológicos e Innovación Tecnológica
                'receiver_department_id' => 5, // Archivo
                'issue_date' => $now,
                'priority' => 'Medium',
                'created_by' => 2, // Usuario 2
                'received_date' => $now,
                'created_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Acta de Reunión de Recursos Humanos',
                'reference_number' => 'Doc-0026',
                'description' => 'Acta de reunión celebrada en la Dirección de Recursos Humanos.',
                'category_id' => 2, // Acta
                'status_id' => 3, // En Firma
                'sender_department_id' => 6, // Secretaría de Finanzas
                'receiver_department_id' => 1, // Dirección de Recursos Humanos
                'issue_date' => $now,
                'priority' => 'Low',
                'created_by' => 3, // Usuario 3
                'received_date' => $now,
                'created_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Informe de Inventarios 2025',
                'reference_number' => 'Doc-0027',
                'description' => 'Informe sobre los inventarios de la Universidad para el año 2025.',
                'category_id' => 4, // Informe
                'status_id' => 4, // Concluido
                'sender_department_id' => 3, // Secretaría General
                'receiver_department_id' => 4, // Dirección de Control Presupuestal E Inventarios
                'issue_date' => $now,
                'priority' => 'None',
                'created_by' => 4, // Usuario 4
                'received_date' => $now,
                'created_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Solicitud de Material de Oficina',
                'reference_number' => 'Doc-0028',
                'description' => 'Solicitud de material de oficina para la Facultad de Sistemas Biológicos.',
                'category_id' => 1, // Oficio
                'status_id' => 5, // Entregado
                'sender_department_id' => 2, // Facultad de Sistemas Biológicos e Innovación Tecnológica
                'receiver_department_id' => 5, // Archivo
                'issue_date' => $now,
                'priority' => 'Medium',
                'created_by' => 5, // Usuario 5
                'received_date' => $now,
                'created_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Reporte de Pago de Nomina',
                'reference_number' => 'Doc-0029',
                'description' => 'Reporte de pagos de nómina del personal académico.',
                'category_id' => 4, // Informe
                'status_id' => 6, // Cancelado
                'sender_department_id' => 1, // Secretaría de Finanzas
                'receiver_department_id' => 2, // Subdirección de Recursos Humanos
                'issue_date' => $now,
                'priority' => 'Low',
                'created_by' => 6, // Usuario 6
                'received_date' => $now,
                'created_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Evaluación de Desempeño de Personal',
                'reference_number' => 'Doc-0030',
                'description' => 'Evaluación del desempeño del personal de la Universidad.',
                'category_id' => 2, // Acta
                'status_id' => 3, // En Firma
                'sender_department_id' => 3, // Secretaría General
                'receiver_department_id' => 1, // Dirección de Recursos Humanos
                'issue_date' => $now,
                'priority' => 'High',
                'created_by' => 7, // Usuario 7
                'received_date' => $now,
                'created_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Solicitud de Permiso de Ausencia',
                'reference_number' => 'Doc-0031',
                'description' => 'Solicitud de permiso por ausencia de un empleado de la Universidad.',
                'category_id' => 1, // Oficio
                'status_id' => 4, // Concluido
                'sender_department_id' => 6, // Secretaría de Finanzas
                'receiver_department_id' => 5, // Archivo
                'issue_date' => $now,
                'priority' => 'Medium',
                'created_by' => 8, // Usuario 8
                'received_date' => $now,
                'created_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Informe de Evaluación de Inventarios',
                'reference_number' => 'Doc-0032',
                'description' => 'Informe sobre la evaluación de inventarios realizada en el último trimestre.',
                'category_id' => 4, // Informe
                'status_id' => 2, // En Tramite
                'sender_department_id' => 3, // Secretaría General
                'receiver_department_id' => 4, // Dirección de Control Presupuestal E Inventarios
                'issue_date' => $now,
                'priority' => 'Low',
                'created_by' => 9, // Usuario 9
                'received_date' => $now,
                'created_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Oficio de Notificación de Reunión',
                'reference_number' => 'Doc-0033',
                'description' => 'Notificación sobre la reunión programada para discutir temas de recursos humanos.',
                'category_id' => 1, // Oficio
                'status_id' => 1, // Recepcionado
                'sender_department_id' => 2, // Facultad de Sistemas Biológicos e Innovación Tecnológica
                'receiver_department_id' => 1, // Dirección de Recursos Humanos
                'issue_date' => $now,
                'priority' => 'High',
                'created_by' => 10, // Usuario 10
                'received_date' => $now,
                'created_at' => $now,
            ],
        ]);
    }
}
