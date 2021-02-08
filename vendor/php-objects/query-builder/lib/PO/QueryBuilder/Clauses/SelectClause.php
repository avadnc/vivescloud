<?php

namespace PO\QueryBuilder\Clauses;

/**
 * @author Marcelo Jacobus <marcelo.jacobus@gmail.com>
 */
class SelectClause extends Base
{
    /**
     * Informs that the query is not empty
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return false;
    }

    /**
     * Return the resulting query
     * @return string
     */
    public function toSql()
    {
        if (0 === count($this->getParams())) {
            return 'SELECT *';
        } else {
            return 'SELECT ' . implode(', ', $this->getParams());
        }
    }
}
