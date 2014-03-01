<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Model\Entry;

interface HydratorInterface
{
    /**
     * Constructor
     *
     * Set the EntryCollection and load any global elements the hydrator might need
     * @param EntryCollection $collection
     */
    public function __construct(EntryCollection $collection);

    /**
     * Preload any custom field data that resides in another DB table
     * @param  array  $entryIds all the entry IDs in the collection (including related entries)
     * @return void
     */
    public function preload(array $entryIds);

    /**
     * Hydrate an Entry's custom field(s) that match the current hydrator type
     * @param  Entry  $entry
     * @return void
     */
    public function hydrate(Entry $entry);
}