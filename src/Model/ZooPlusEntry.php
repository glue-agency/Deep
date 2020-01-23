<?php

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Builder;

/**
 * {@inheritdoc}
 *
 * Joins with playa_relationships table
 */
class ZooPlusEntry extends Entry
{
    /**
     * {@inheritdoc}
     */
    protected $hidden = ['channel', 'site_id', 'forum_topic_id', 'ip_address', 'versioning_enabled', 'relation_id','parent_id','parent_field_id','child_id','child_field_id','rel_order','reverse_rel_order','status','creationdate'];

    /**
     * {@inheritdoc}
     */
    protected $collectionClass = '\\rsanchez\\Deep\\Collection\\ZooPlusCollection';

    /**
     * {@inheritdoc}
     */
    protected static function joinTables()
    {
        //@todo apply by settings!

        return array_merge(parent::joinTables(), [
            'zoo_plus_relationships' => function ($query) {
                $query->leftJoin('zoo_plus_relationships',function($join){
                    $join->on( 'zoo_plus_relationships.child_id', '=', 'channel_titles.entry_id');
                });
            },
        ]);
    }

    /**
     * Filter by Parent Entry ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int|array                             $entryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParentEntryId(Builder $query, $entryId)
    {
        $entryId = is_array($entryId) ? $entryId : [$entryId];

        return $this->requireTable($query, 'zoo_plus_relationships', true)->whereIn('zoo_plus_relationships.parent_id', $entryId);
    }
}
