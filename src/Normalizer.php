<?php

namespace Tobiassjosten\JsonApi;

class Normalizer
{
    /**
     * Normalize JSON API.
     *
     * This makes sure that entities in relationships are only references to
     * their data in the 'included' section. AKA "flattening".
     *
     * @api
     */
    public static function normalize($jsonApi)
    {
        if (isset($jsonApi['errors'])) {
            return $jsonApi;
        }

        $included = self::extractIncluded($jsonApi);
        $data = self::formatData($jsonApi);

        return ['data' => $data] + ($included ? ['included' => $included] : []);
    }

    /**
     * Iterate given array to extract related and included entities.
     */
    private static function extractIncluded(&$jsonApi)
    {
        if (!$jsonApi || !is_array($jsonApi)) {
            return [];
        }

        $included = array_merge(
            self::extractNestedIncluded($jsonApi),
            self::extractRelatedIncluded($jsonApi)
        );

        $unique = $exists = [];
        foreach ($included as $include) {
            if (empty($exists[$include['type']][$include['id']])) {
                $unique[] = $include;
                $exists[$include['type']][$include['id']] = true;
            }
        }

        return $unique;
    }

    /**
     * Find and extract all 'included' list of entities.
     */
    private static function extractNestedIncluded(&$jsonApi)
    {
        if ($included = $jsonApi['included'] ?? []) {
            unset($jsonApi['included']);
        }

        foreach ($jsonApi as &$value) {
            if (is_array($value)) {
                $included = array_merge($included, self::extractNestedIncluded($value));
            }
        }

        return $included;
    }

    /**
     * Find and extract all 'relationships' entities.
     */
    private static function extractRelatedIncluded(&$jsonApi)
    {
        $included = [];

        if (!empty($jsonApi['relationships'])) {
            foreach ($jsonApi['relationships'] as &$relationship) {
                self::mapData($relationship, function (&$relationship) use (&$included) {
                    $included = array_merge($included, self::extractRelatedIncluded($relationship), [$relationship]);
                    $relationship = [
                        'type' => $relationship['type'],
                        'id' => $relationship['id'],
                    ];
                });
            }
        }

        foreach ($jsonApi as $key => &$value) {
            if ('relationship' !== $key && is_array($value)) {
                $included = array_merge($included, self::extractRelatedIncluded($value));
            }
        }

        return $included;
    }

    /**
     * Apply given function on all entities in given data structure.
     */
    private static function mapData(&$data, $function)
    {
        // Data can be null, an empty array, or any other non-standard value.
        if (!is_array($data) || !$data) {
            return;
        }

        // Data can already be structured-ish, in which case we unwrap it.
        if (array_key_exists('data', $data)) {
            return self::mapData($data['data'], $function);
        }

        // Data can be an array of entities.
        if (!isset($data['type']) && !isset($data['id'])) {
            return array_map(function (&$data) use ($function) {
                self::mapData($data, $function);
            }, $data);
        }

        if (isset($data['type']) && isset($data['id'])) {
            $function($data);
        }
    }

    /**
     * Format proper data structure.
     */
    private static function formatData($data)
    {
        // Data can be null, an empty array, or any other non-standard value.
        if (!is_array($data) || !$data) {
            return $data;
        }

        // Data can already be structured-ish, in which case we unwrap it.
        if (array_key_exists('data', $data)) {
            return self::formatData($data['data']);
        }

        // Data can be an array of entities.
        if (!isset($data['type']) && !isset($data['id'])) {
            return array_filter(array_map(function ($data) {
                return self::formatData($data);
            }, $data));
        }

        if (isset($data['relationships'])) {
            foreach ($data['relationships'] as $name => &$relationship) {
                if (isset($relationship['links']) || isset($relationship['meta'])) {
                    continue;
                }

                $relationship = ['data' => self::formatData($relationship)];

                if (!$relationship['data'] || !is_array($relationship['data'])) {
                    unset($data['relationships'][$name]);
                    continue;
                }
            }

            if (empty($data['relationships'])) {
                unset($data['relationships']);
            }
        }

        return $data;
    }
}
