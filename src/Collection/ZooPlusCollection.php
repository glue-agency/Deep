<?php


namespace rsanchez\Deep\Collection;

use Illuminate\Database\Eloquent\Model;
use rsanchez\Deep\Model\ZooPlusEntry;

/**
 * Collection of \rsanchez\Deep\Model\ZooPlusEntry
 */
class ZooPlusCollection extends EntryCollection
{
    /**
     * {@inheritdoc}
     */
    protected $modelClass = '\\rsanchez\\Deep\\Model\\ZooPlusEntry';

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        // flatten the array keys
        return array_values(parent::toArray());
    }

    /**
     * Add an entry based on ID
     * @param $entryId
     */
    public function addEntryId($entryId)
    {
        $this->push(ZooPlusEntry::find($entryId));
    }

    /**
     * Add several entries based on ID
     * @param array $entryIds
     */
    public function addEntryIds(array $entryIds)
    {
        $this->items += ZooPlusEntry::entryId($entryIds)->get()->all();
    }
}
