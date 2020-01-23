<?php

namespace rsanchez\Deep\Hydrator;

use Illuminate\Support\Collection;

/**
 * Collection of Hydrators
 */
class HydratorCollection extends Collection
{
    /**
     * Throw an exception if attempting to push a Hydrator without specifying type
     */
    public function push($value)
    {
        throw new \Exception('Hydrators must have a key. Use put method instead.');
    }

    /**
     * Add a Hydrator to the collection
     *
     * @param  string                                   $type
     * @param  \rsanchez\Deep\Hydrator\AbstractHydrator $hydrator
     * @return void
     */
    public function put($type, $hydrator)
    {
        return $this->addHydrator($type, $hydrator);
    }

    /**
     * Add a Hydrator to the collection
     *
     * @param  string                                   $type
     * @param  \rsanchez\Deep\Hydrator\AbstractHydrator $hydrator
     * @return void
     */
    public function addHydrator($type, AbstractHydrator $hydrator)
    {
        $this->items[$type] = $hydrator;
    }
}
