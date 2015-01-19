<?php


namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Model\FilterQuery;

/**
 * FIXME: Parse comparison operators
 */
class FilterQueryParser
{
    const PROPERTY_MATCH = '/^@([^\{]+)\{([^\}]+)\}$/';

    /**
     * Parses string query $q into a new FilterQuery object
     *
     * @param string $q
     *
     * @return FilterQuery
     */
    public function parse($q)
    {
        $parts = explode(' ', $q);
        $query = new FilterQuery();

        $queryParts = array_filter($parts, function ($part) {
            return preg_match(static::PROPERTY_MATCH, $part) === 0;
        });
        $term       = trim(join(' ', $queryParts));
        if ($term) {
            $query->setTerm($term);
        }

        $propertyParts = array_map(function ($part) {
            preg_match(static::PROPERTY_MATCH, $part, $matches);
            return array($matches[1], $matches[2]);
        }, array_filter($parts, function ($part) {
            return preg_match(static::PROPERTY_MATCH, $part) === 1;
        }));
        foreach ($propertyParts as $property) {
            $query->setProperty($property[0], $property[1]);
        }
        return $query;
    }
}
