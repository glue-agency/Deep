<?php

namespace rsanchez\Deep\Channel\Field;

use rsanchez\Deep\Channel\Field\Field;
use rsanchez\Deep\Property\AbstractCollection as PropertyCollection;

class Collection extends PropertyCollection
{
    protected $filterClass = __CLASS__;

    public function push(Field $field)
    {
        parent::push($field);
    }
}