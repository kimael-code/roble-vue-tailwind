<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseCollection extends ResourceCollection
{
    /**
     * Elimina los enlaces "&laquo; Anterior" y "Siguiente &raquo;".
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array $paginated
     * @param  array $default
     * @return array
     */
    public function paginationInformation($request, $paginated, $default)
    {
        array_shift($default['meta']['links']);
        array_pop($default['meta']['links']);

        return $default;
    }
}
