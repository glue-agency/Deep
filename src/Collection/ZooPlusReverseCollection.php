<?php


namespace rsanchez\Deep\Collection;

use Illuminate\Database\Eloquent\Model;

use rsanchez\Deep\Model\ZooPlusReverseEntry;

/**
 * Collection of \rsanchez\Deep\Model\ZooPlusReverseEntry
 */
class ZooPlusReverseCollection extends EntryCollection
{
    /**
     * {@inheritdoc}
     */
    protected $modelClass = '\\rsanchez\\Deep\\Model\\ZooPlusReverseEntry';

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
        $this->push(ZooPlusReverseEntry::find($entryId));
    }

    /**
     * Add several entries based on ID
     * @param array $entryIds
     */
    public function addEntryIds(array $entryIds)
    {
        $this->items += ZooPlusReverseEntry::entryId($entryIds)->get()->all();
    }
}
