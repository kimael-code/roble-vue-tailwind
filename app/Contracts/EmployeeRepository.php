<?php

namespace App\Contracts;

interface EmployeeRepository
{
    /**
     * Devuelve todos los empleados del ente u organización.
     * @return array<int, object>
     */
    public function all(): array;

    /**
     * Devuelve un empleado del ente u organización filtrado por su número de
     * cédula.
     * @param int|string $idCardNumber Número de cédula del empleado a buscar.
     * @return array
     */
    public function find(int|string $idCardNumber): array;
}
