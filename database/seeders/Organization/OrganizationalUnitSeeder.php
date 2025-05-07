<?php

namespace Database\Seeders\Organization;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationalUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::unprepared(
            "INSERT INTO
            organizational_units (
                organization_id,
                organizational_unit_id,
                name,
                acronym,
                floor,
                created_at,
                updated_at,
                disabled_at
            )
            VALUES
            (1,NULL,'JUNTA DIRECTIVA','JD','PA','now()','now()',NULL),
            (1,1,'GERENCIA DE SECRETARÍA DE LA JUNTA DIRECTIVA','GSJD','PA','now()','now()',NULL),
            (1,1,'UNIDAD DE AUDITORÍA INTERNA','UAI','7','now()','now()',NULL),
            (1,3,'GERENCIA DE AUDITORÍA DE ACTIVOS Y LIQUIDACIÓN','GAAL','7','now()','now()',NULL),
            (1,4,'DEPARTAMENTO DE AUDITORÍA DE ACTIVOS Y LIQUIDACIÓN','DAAL','7','now()','now()',NULL),
            (1,3,'GERENCIA DE AUDITORÍA FINANCIERA Y OPERACIONAL','GAFYO','7','now()','now()',NULL),
            (1,6,'DEPARTAMENTO DE AUDITORÍA FINANCIERA Y OPERACIONAL','DAFYO','7','now()','now()',NULL),
            (1,3,'GERENCIA DE DETERMINACIÓN DE RESPONSABILIDADES','GDR','7','now()','now()',NULL),
            (1,1,'OFICINA DE ATENCIÓN AL CIUDADANO','OAC','PB','now()','now()',NULL),
            (1,1,'PRESIDENCIA','PRE','PA','now()','now()',NULL),
            (1,10,'VICEPRESIDENCIA','VP','PA','now()','now()',NULL),
            (1,11,'CONSULTORÍA JURÍDICA','CJ','PA','now()','now()',NULL),
            (1,12,'GERENCIA LEGAL DE ASUNTOS FINANCIEROS','GLAF','PA','now()','now()',NULL),
            (1,13,'DEPARTAMENTO DE DOCUMENTACIÓN Y ASESORÍA FINANCIERA','DDAF','PA','now()','now()',NULL),
            (1,13,'DEPARTAMENTO DE ANÁLISIS LEGAL Y FINANCIERO','DALF','PA','now()','now()',NULL),
            (1,12,'GERENCIA LEGAL DE ASUNTOS JUDICIALES','GLAJ','MZ','now()','now()',NULL),
            (1,16,'DEPARTAMENTO DE CONTROL DE JUICIOS DE LA BANCA EN LIQUIDACIÓN','DCJBL','MZ','now()','now()',NULL),
            (1,16,'DEPARTAMENTO DE CONTROL DE JUICIOS DE FOGADE','DCJF','MZ','now()','now()',NULL),
            (1,16,'DEPARTAMENTO DE CONTROL ADMINISTRATIVO Y ASESORÍAS','DCAA','MZ','now()','now()',NULL),
            (1,12,'GERENCIA LEGAL DE ASUNTOS ADMINISTRATIVOS','GLAA','PA','now()','now()',NULL),
            (1,20,'DEPARTAMENTO DE ASESORÍA Y ESTUDIOS JURÍDICOS','DAEJ','PA','now()','now()',NULL),
            (1,20,'DEPARTAMENTO DE DOCUMENTACIÓN Y OPERACIONES ADMINISTRATIVAS','DDOA','PA','now()','now()',NULL),
            (1,11,'GERENCIA DE RECURSOS HUMANOS','GRH','5','now()','now()',NULL),
            (1,23,'UNIDAD DE ASISTENCIA LEGAL','UAL','5','now()','now()',NULL),
            (1,23,'DEPARTAMENTO TÉCNICO','DT','5','now()','now()',NULL),
            (1,23,'DEPARTAMENTO DE ADMINISTRACIÓN DE PERSONAL','DAP','5','now()','now()',NULL),
            (1,23,'DEPARTAMENTO DE BIENESTAR SOCIAL','DBS','5','now()','now()',NULL),
            (1,11,'GERENCIA DE INFORMÁTICA','GI','S1','now()','now()',NULL),
            (1,28,'DEPARTAMENTO DE DESARROLLO DE SISTEMAS','DDS','S1','now()','now()',NULL),
            (1,28,'DEPARTAMENTO DE ORGANIZACIÓN Y SISTEMAS','DOS','S1','now()','now()',NULL),
            (1,11,'GERENCIA DE RELACIONES INSTITUCIONALES','GRI','6','now()','now()',NULL),
            (1,11,'GERENCIA DE INVESTIGACIÓN Y SEGURIDAD','GIS','6','now()','now()',NULL),
            (1,32,'DEPARTAMENTO DE VIGILANCIA Y SEGURIDAD','DVS','6','now()','now()',NULL),
            (1,32,'DEPARTAMENTO DE INVESTIGACIÓN','DI','6','now()','now()',NULL),
            (1,11,'GERENCIA GENERAL DE OPERACIONES','GGO','3','now()','now()',NULL),
            (1,35,'GERENCIA DE SEGUROS DE DEPÓSITOS','GSD','3','now()','now()',NULL),
            (1,35,'GERENCIA DE ESTUDIOS','GE','3','now()','now()',NULL),
            (1,35,'GERENCIA DE CRÉDITO','GDC','3','now()','now()', 'now()'),
            (1,11,'GERENCIA GENERAL DE ACTIVOS Y LIQUIDACIÓN','GGAL','13','now()','now()',NULL),
            (1,39,'GERENCIA DE COORDINACIÓN DE LIQUIDACIONES','GCL','12','now()','now()',NULL),
            (1,40,'DEPARTAMENTO DE EMPRESAS RELACIONADAS','DER','12','now()','now()',NULL),
            (1,40,'DEPARTAMENTO DE LIQUIDACIÓN DIRECTA','DLD','12','now()','now()',NULL),
            (1,40,'DEPARTAMENTO DE LIQUIDACIONES COORDINADAS','DLC','12','now()','now()',NULL),
            (1,39,'GERENCIA DE ADMINISTRACIÓN DE CARTERA DE CRÉDITO','GACC','9','now()','now()',NULL),
            (1,44,'DEPARTAMENTO DE CRÉDITOS COMERCIALES Y AL CONSUMO','DCCC','9','now()','now()',NULL),
            (1,44,'DEPARTAMENTO DE CARTERAS DE CRÉDITOS HIPOTECARIOS','DCCH','9','now()','now()',NULL),
            (1,44,'DEPARTAMENTO DE CARTERA DE CRÉDITOS AGRÍCOLAS Y ARRENDAMIENTO FINANCIERO','DCCAAF','9','now()','now()',NULL),
            (1,39,'GERENCIA DE ADMINISTRACIÓN DE BIENES MUEBLES E INMUEBLES','GABMI','10','now()','now()',NULL),
            (1,48,'DEPARTAMENTO DE ADMINISTRACIÓN DE BIENES','DAB','10','now()','now()',NULL),
            (1,48,'DEPARTAMENTO DE INSPECCIÓN Y AVALÚOS','DIA','10','now()','now()',NULL),
            (1,39,'GERENCIA EMPRESAS EN MARCHA','GEM','13','now()','now()',NULL),
            (1,39,'GERENCIA MERCADEO Y VENTAS','GMV','12','now()','now()', 'now()'),
            (1,11,'GERENCIA GENERAL DE ADMINISTRACIÓN Y FINANZAS','GGAF','2','now()','now()',NULL),
            (1,53,'DEPARTAMENTO DE CONTROL DE GESTIÓN','DCG','1','now()','now()',NULL),
            (1,53,'GERENCIA DE PLANIFICACIÓN Y PRESUPUESTO','GPP','1','now()','now()',NULL),
            (1,55,'DEPARTAMENTO DE PLANIFICACIÓN Y PROGRAMACIÓN PRESUPUESTARIA','DPPP','1','now()','now()',NULL),
            (1,55,'DEPARTAMENTO DE REGISTRO Y CONTROL PRESUPUESTARIO','DRCP','1','now()','now()',NULL),
            (1,53,'GERENCIA DE CONTABILIDAD','GC','1','now()','now()',NULL),
            (1,58,'DEPARTAMENTO DE REGISTRO Y CONTROL','DRC','1','now()','now()',NULL),
            (1,58,'DEPARTAMENTO DE ANÁLISIS CONTABLE','DAC','1','now()','now()',NULL),
            (1,53,'GERENCIA DE TESORERÍA','GT','2','now()','now()',NULL),
            (1,61,'DEPARTAMENTO DE INVERSIONES','DDI','2','now()','now()',NULL),
            (1,61,'DEPARTAMENTO DE ADMINISTRACIÓN DE EFECTIVO','DAE','2','now()','now()',NULL),
            (1,61,'DEPARTAMENTO DE CUSTODIA DE VALORES','DCV','2','now()','now()',NULL),
            (1,53,'GERENCIA DE SERVICIOS ADMINISTRATIVOS','GSA','2','now()','now()',NULL),
            (1,65,'DEPARTAMENTO DE SERVICIOS GENERALES','DSG','2','now()','now()',NULL),
            (1,66,'SECCIÓN DE ARTES GRÁFICAS Y REPRODUCCIÓN','SAGR','S1','now()','now()',NULL),
            (1,66,'SECCIÓN DE ARCHIVO','SDA','2','now()','now()',NULL),
            (1,66,'SECCIÓN DE CORRESPONDENCIA','SDC','S2','now()','now()',NULL),
            (1,65,'DEPARTAMENTO DE ADQUISICIÓN Y SUMINISTROS','DAS','2','now()','now()',NULL),
            (1,70,'SECCIÓN DE CONTRATACIONES','SC','2','now()','now()',NULL),
            (1,70,'SECCIÓN DE COMPRAS','SDCOMP','2','now()','now()',NULL),
            (1,53,'GERENCIA DE INFRAESTRUCTURA','GDI','S1','now()','now()',NULL),
            (1,73,'DEPARTAMENTO DE SERVICIOS TÉCNICO','DST','S1','now()','now()',NULL),
            (1,74,'SECCIÓN DE CONTROL DE ACTIVOS','SCA','S1','now()','now()',NULL),
            (1,74,'SECCIÓN DE MANTENIMIENTO','SM','S1','now()','now()',NULL),
            (1,10,'UNIDAD DE PREVENCIÓN Y CONTROL DE LA LEGITIMACIÓN DE CAPITALES','UPCLC','MZ','now()','now()',NULL),
            (1,10,'GERENCIA DE ADMINISTRACIÓN INTEGRAL DE RIESGOS','GAIR','3','now()','now()',NULL),
            (1,73,'DEPARTAMENTO DE OBRAS CIVILES','DOC','S1','now()','now()',NULL),
            (1,NULL,'NO DEFINIDA','','','now()','now()',NULL);
        ");
    }
}
