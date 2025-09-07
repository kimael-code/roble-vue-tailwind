<?php

namespace App\Support\Traits;

trait MergesAppends
{
    /**
     * Get the accessors that are appendable.
     *
     * @return array
     */
    protected function getArrayableAppends()
    {
        $appends = parent::getArrayableAppends();

        $parentClass = get_parent_class($this);
        if (property_exists($parentClass, 'appends'))
        {
            $parentInstance = new $parentClass;
            $parentAppends = $parentInstance->appends;

            $appends = array_unique(array_merge($parentAppends, $appends));
        }

        return $appends;
    }
}
