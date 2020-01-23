<?php


namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Builder;

/**
 * Model for the members table
 */
class TranscribeEntry extends Model
{
    use JoinableTrait;

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'transcribe_entries_languages';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'id';

}
