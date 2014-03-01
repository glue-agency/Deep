<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use Illuminate\Database\Eloquent\Collection;

/**
 * Collection of \rsanchez\Deep\Model\Entry
 */
class EntryCollection extends Collection
{
    /**
     * Array of fieldtype => hydrator class name
     * @var array
     */
    protected static $hydrators = array(
        'matrix'       => '\\rsanchez\\Deep\\Hydrator\\MatrixHydrator',
        'grid'         => '\\rsanchez\\Deep\\Hydrator\\GridHydrator',
        'playa'        => '\\rsanchez\\Deep\\Hydrator\\PlayaHydrator',
        'relationship' => '\\rsanchez\\Deep\\Hydrator\\RelationshipHydrator',
        'assets'       => '\\rsanchez\\Deep\\Hydrator\\AssetsHydrator',
        'file'         => '\\rsanchez\\Deep\\Hydrator\\FileHydrator',
        'date'         => '\\rsanchez\\Deep\\Hydrator\\DateHydrator',
    );

    /**
     * Matrix columns used by this collection
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $matrixCols;

    /**
     * Grid columns used by this collection
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $gridCols;

    /**
     * All of the entry IDs in this collection (including related entries)
     * @var array
     */
    protected $entryIds = array();

    /**
     * Fieldtypes used by this collection
     * @var array
     */
    protected $fieldtypes = array();

    /**
     * Map of fieldtypes to field IDs:
     *    'fieldtype_name' => array(1, 2, 3),
     * @var array
     */
    protected $fieldIdsByFieldtype = array();

    /**
     * Loop through all the hydrators to set Entry custom field attributes
     * @return void
     */
    public function hydrate()
    {
        $fieldtypes =& $this->fieldtypes;
        $fieldIdsByFieldtype =& $this->fieldIdsByFieldtype;

        $entryIds = $this->modelKeys();

        $this->entryIds = $entryIds;

        // loop through all the fields used in this collection to gather a list of fieldtypes used
        $this->fetch('channel.fields')->each(function ($rows) use (&$fieldtypes, &$fieldIdsByFieldtype) {
            foreach ($rows as $row) {
                $fieldtypes[] = $row['field_type'];
                $fieldIdsByFieldtype[$row['field_type']][] = $row['field_id'];
            }
        });

        $hydrators = array();

        foreach (self::$hydrators as $fieldtype => $class) {
            if ($this->hasFieldtype($fieldtype)) {
                $hydrators[$fieldtype] = new $class($this);
            }
        }

        // loop through the hydrators for preloading
        foreach ($hydrators as $hydrator) {
            $hydrator->preload($this->entryIds());
        }

        // loop again to actually hydrate
        foreach ($this as $entry) {
            foreach ($hydrators as $hydrator) {
                $hydrator->hydrate($entry);
            }
        }
    }

    /**
     * Get all the entry Ids from this collection.
     * This includes both the entries directly in this collection,
     * and entries found in Playa/Relationship fields
     *
     * @return array
     */
    public function entryIds()
    {
        return $this->entryIds;
    }

    /**
     * Add additional entry ids to this collection
     *
     * @param array $entryIds
     */
    public function addEntryIds(array $entryIds)
    {
        $this->entryIds = array_unique(array_merge($this->entryIds, $entryIds));
    }

    /**
     * Check if this collection uses the specified fieldtype
     *
     * @param  string  $fieldtype
     * @return boolean
     */
    public function hasFieldtype($fieldtype)
    {
        return in_array($fieldtype, $this->fieldtypes);
    }

    /**
     * Get the field IDs for the specified fieldtype
     *
     * @param  string $fieldtype
     * @return array
     */
    public function getFieldIdsByFieldtype($fieldtype)
    {
        return isset($this->fieldIdsByFieldtype[$fieldtype]) ? $this->fieldIdsByFieldtype[$fieldtype] : array();
    }

    /**
     * Set the Matrix columns for this collection
     *
     * @param  \Illuminate\Database\Eloquent\Collection $collection
     * @return void
     */
    public function setMatrixCols(Collection $matrixCols)
    {
        $fieldtypes =& $this->fieldtypes;

        $matrixCols->each(function ($col) use (&$fieldtypes) {
            $fieldtypes[] = $col->col_type;
        });

        $this->matrixCols = $matrixCols;
    }

    /**
     * Get the Matrix columns for this collection
     *
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function getMatrixCols()
    {
        return $this->matrixCols;
    }

    /**
     * Set the Grid columns for this collection
     *
     * @param  \Illuminate\Database\Eloquent\Collection $collection
     * @return void
     */
    public function setGridCols(Collection $gridCols)
    {
        $fieldtypes =& $this->fieldtypes;

        $gridCols->each(function ($col) use (&$fieldtypes) {
            $fieldtypes[] = $col->col_type;
        });

        $this->gridCols = $gridCols;
    }

    /**
     * Get the Grid columns for this collection
     *
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function getGridCols()
    {
        return $this->gridCols;
    }
}