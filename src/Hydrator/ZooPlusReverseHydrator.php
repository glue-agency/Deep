<?php

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Collection\ZooPlusCollection;
use rsanchez\Deep\Collection\ZooPlusReverseCollection;
use rsanchez\Deep\Model\PropertyInterface;
use rsanchez\Deep\Model\AbstractEntity;

use rsanchez\Deep\Model\ZooPlusEntry;
use rsanchez\Deep\Model\ZooPlusReverseEntry;

/**
 * Hydrator for the ZooPlusReverse fieldtype
 */
class ZooPlusReverseHydrator extends AbstractHydrator
{
    /**
     * @var \rsanchez\Deep\Model\ZooPlusEntry
     */
    protected $model;

    /**
     * List of entries in this collection, organized by
     * type, entity and property
     * @var array
     */
    protected $entries;

    /**
     * Collection of entries being loaded by the parent collection
     * @var \rsanchez\Deep\Collection\ZooPlusReverseCollection
     */
    protected $zooPlusReverseCollection;

    /**
     * {@inheritdoc}
     *
     * @param \rsanchez\Deep\Hydrator\HydratorCollection     $hydrators
     * @param string                                         $fieldtype
     * @param \rsanchez\Deep\Model\ZooPlusReverseEntry                $model
     * @param \rsanchez\Deep\Collection\ZooPlusCollection|null $zooPlusCollection
     */
    public function __construct(HydratorCollection $hydrators, $fieldtype, ZooPlusReverseEntry $model, ZooPlusReverseCollection $zooPlusReverseCollection = null)
    {
        parent::__construct($hydrators, $fieldtype);

        $this->model = $model;
        $this->zooPlusReverseCollection = $zooPlusReverseCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function bootFromCollection(EntryCollection $collection)
    {

        $builder = $this->model->parentEntryId($collection->modelKeys())->orderBy('rel_order');

        //if (!$this->childHydrationEnabled) {
            $builder = $this->castToDeepBuilder($builder)->setHydrationDisabled();
        //}

        $this->zooPlusReverseCollection = $builder->get();

        foreach ($this->zooPlusReverseCollection as $entry) {

            $type = 'entry';
            $entityId = $entry->child_id;
            $propertyId = $entry->parent_field_id;

            if (! isset($this->entries[$type][$entityId][$propertyId])) {
                $this->entries[$type][$entityId][$propertyId] = [];
            }

            $this->entries[$type][$entityId][$propertyId][] = $entry;
        }

        // add these entry IDs to the main collection
        $collection->addEntryIds($this->zooPlusReverseCollection->modelKeys());
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, PropertyInterface $property)
    {
        $entity->addCustomFieldSetter($property->getName(), [$this, 'setter']);

        if (! isset($this->zooPlusReverseCollection)) {
            return new ZooPlusReverseCollection();
        }

        $zooPlusFieldId = $property->getSettings()['plus_field_id'];

        $entries = isset($this->entries[$entity->getType()][$entity->getId()][$zooPlusFieldId])
            ? $this->entries[$entity->getType()][$entity->getId()][$zooPlusFieldId] : [];

        return $this->zooPlusReverseCollection->createChildCollection($entries);
    }

    /**
     * Setter callback
     * @param  \rsanchez\Deep\Collection\ZooPlusReverseCollection|array|null $value
     * @return \rsanchez\Deep\Collection\ZooPlusReverseCollection|null
     */
    public function setter($value = null, PropertyInterface $property = null)
    {
        if (is_null($value)) {
            return null;
        }

        if ($value instanceof ZooPlusReverseCollection) {
            return $value;
        }

        // array of entry ids
        if (is_array($value)) {
            $collection = new ZooPlusReverseCollection();

            $collection->addEntryIds($value);

            return $collection;
        }

        throw new \InvalidArgumentException('$value must be of type array, null, or \rsanchez\Deep\Collection\ZooPlusReverseCollection.');
    }
}
