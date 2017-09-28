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

        $jsonApi = [
            'data' => is_array($jsonApi) && array_key_exists('data', $jsonApi) ? $jsonApi['data'] : $jsonApi,
            'included' => @$jsonApi['included'] ?: [],
        ];

        $jsonApi['data'] = self::unwrapData($jsonApi, $jsonApi['included'], false);

        if (empty($jsonApi['included'])) {
            unset($jsonApi['included']);
        }

        return $jsonApi;
    }

    private static function unwrapData(&$jsonApi, &$included, $child)
    {
        if (isset($jsonApi['included']) && $child) {
            foreach ($jsonApi['included'] as $include) {
                self::include($include, $included);
            }
            unset($jsonApi['included']);
        }

        // $jsonApi must be a reference so that we can remove the 'included'
        // property. Its 'data' property, however, is mutated only in its
        // return value and thus the copying ternary below is important.

        $data = !is_array($jsonApi) || !array_key_exists('data', $jsonApi)
            ? $jsonApi
            : (!is_array($jsonApi['data']) || !array_key_exists('data', $jsonApi['data'])
                ? $jsonApi['data']
                : $jsonApi['data']['data']
            );

        if (!is_array($data) || !$data) {
            return $data;
        }

        if (!isset($data['type']) && !isset($data['id'])) {
            return array_map(function ($datum) use (&$included, $child) {
                return self::handleData($datum, $included, $child);
            }, $data);
        }

        return self::handleData($data, $included, $child);
    }

    private static function handleData($data, &$included, $child)
    {
        if (isset($data['data'])) {
            return self::unwrapData($data, $included, $child);
        }

        if (isset($data['relationships'])) {
            foreach ($data['relationships'] as $key => &$relationship) {
                if (!$relationship || !is_array($relationship)) {
                    unset($data['relationships'][$key]);
                    continue;
                }

                $relationship = ['data' => self::unwrapData($relationship, $included, true)];
            }
        }

        if (empty($data['relationships'])) {
            unset($data['relationships']);
        }

        if ($child) {
            self::include($data, $included);
        }

        return $data;
    }

    private static function include(&$data, &$included)
    {
        // Run through $included to ensure we don't duplicate $data in there.
        if (!array_reduce($included, function ($carry, $include) use ($data) {
            return $carry || ($include['type'] === $data['type'] && $include['id'] === $data['id']);
        })) {
            $included[] = $data;
        }

        $data = ['type' => $data['type'], 'id' => $data['id']];
    }
}
