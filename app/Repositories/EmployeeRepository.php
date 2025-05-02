<?php

namespace App\Repositories;

use App\Contracts\EmployeeRepository as EmployeeContract;
use Illuminate\Support\Facades\DB;

class EmployeeRepository implements EmployeeContract
{
    public function all(): array
    {
        return DB::connection('organization')->select(
            'SELECT
                sno_personal.codemp AS "company_code",
                sno_personal.nacper AS "nationality",
                sno_personal.cedper AS "id_card",
                sno_personal.rifper AS "rif",
                sno_personal.nomper AS "names",
                sno_personal.apeper AS "surnames",
                sno_personal.codtippersss AS "staff_type_code",
                sno_personal.codorg AS "org_unit_code",
                sno_personal.carantper AS "position",
                sno_tipopersonalsss.dentippersss AS "staff_type_name",
                srh_organigrama.desorg AS "org_unit_name"
            FROM
                sno_personal
            INNER JOIN sno_tipopersonalsss ON
                sno_personal.codtippersss = sno_tipopersonalsss.codtippersss
                AND sno_personal.codtippersss IN (
                    :empleado, :empleadoContratado, :empleadoSuplente,
                    :obrero, :obreroContratado, :obreroSuplente,
                    :comisServicio, :altoNivel
                )
            INNER JOIN srh_organigrama ON
                srh_organigrama.codorg = sno_personal.codorg',
        [
            'empleado'           => '0000001',
            'empleadoContratado' => '0000002',
            'empleadoSuplente'   => '0000003',
            'obrero'             => '0000004',
            'obreroContratado'   => '0000005',
            'obreroSuplente'     => '0000006',
            'comisServicio'      => '0000011',
            'altoNivel'          => '0000016',
        ]);
    }

    public function find($idCard): array
    {
        return DB::connection('organization')->select(
            'SELECT
                sno_personal.codemp AS "company_code",
                sno_personal.nacper AS "nationality",
                sno_personal.cedper AS "id_card",
                sno_personal.rifper AS "rif",
                sno_personal.nomper AS "names",
                sno_personal.apeper AS "surnames",
                sno_personal.codtippersss AS "staff_type_code",
                sno_personal.codorg AS "org_unit_code",
                sno_personal.carantper AS "position",
                sno_tipopersonalsss.dentippersss AS "staff_type_name",
                srh_organigrama.desorg AS "org_unit_name"
            FROM
                sno_personal
            INNER JOIN sno_tipopersonalsss ON
                sno_personal.codtippersss = sno_tipopersonalsss.codtippersss
                AND sno_personal.codtippersss IN (
                    :empleado, :empleadoContratado, :empleadoSuplente,
                    :obrero, :obreroContratado, :obreroSuplente,
                    :comisServicio, :altoNivel
                )
            INNER JOIN srh_organigrama ON
                srh_organigrama.codorg = sno_personal.codorg
            WHERE
                cedper ilike :idCard',
        [
            'empleado'           => '0000001',
            'empleadoContratado' => '0000002',
            'empleadoSuplente'   => '0000003',
            'obrero'             => '0000004',
            'obreroContratado'   => '0000005',
            'obreroSuplente'     => '0000006',
            'comisServicio'      => '0000011',
            'altoNivel'          => '0000016',
            'idCard'             => "%$idCard%",
        ]);
    }
}
