<?php

namespace Tobiassjosten\JsonApi;

class Denormalizer
{
    /**
     * Denormalize data.
     *
     * This takes data from the 'included' section and potentially duplicates
     * entities throughout the various relationships. AKA "hydration".
     *
     * @api
     */
    public static function denormalize($jsonApi)
    {
        if (!isset($jsonApi['included'])) {
            return $jsonApi;
        }

        return ['data' => self::unwrapData($jsonApi, $jsonApi['included'])];
    }

    private static function unwrapData($jsonApi, $included)
    {
        if (!isset($jsonApi['data']['type']) && !isset($jsonApi['data']['id'])) {
            return array_map(function ($datum) use ($included) {
                return self::handleData($datum, $included);
            }, $jsonApi['data']);
        }

        return self::handleData($jsonApi['data'], $included);
    }

    private static function handleData($data, $included)
    {
        foreach ($included as $include) {
            if ($data['type'] === $include['type'] && $data['id'] === $include['id']) {
                $data = $include;
                break;
            }
        }

        if (isset($data['relationships'])) {
            foreach ($data['relationships'] as &$relationship) {
                $relationship['data'] = self::unwrapData($relationship, $included);
            }
        }

        return $data;
    }
}
