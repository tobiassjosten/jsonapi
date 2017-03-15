<?php

namespace {

    if (!function_exists('jsonapi_normalize')) {

        /**
        * Normalize JSON API.
        *
        * This makes sure that entities in relationships are only references to
        * their data in the 'included' section. AKA "flattening".
        */
        function jsonapi_normalize($jsonApi, &$included = null) {
            if (!($recursion = $included !== null)) {
                if (!isset($jsonApi['included'])) {
                    $jsonApi['included'] = [];
                }
                $included = &$jsonApi['included'];
            }

            $multiple = !isset($jsonApi['data']['type']) || !isset($jsonApi['data']['id']);
            foreach ($multiple ? $jsonApi['data'] : [&$jsonApi['data']] as &$datum) {
                if (isset($datum['relationships'])) {
                    foreach ($datum['relationships'] as &$relationship) {
                        $relationship = jsonapi_normalize($relationship, $included);
                    }
                }

                if ($recursion) {
                    $included[] = $datum;
                    $datum = [
                        'type' => $datum['type'],
                        'id' => $datum['id'],
                    ];
                }
            }

            // Move embedded includes to root level.
            if ($recursion && isset($jsonApi['included'])) {
                $included = array_merge($included, $jsonApi['included']);
                unset($jsonApi['included']);
            }

            if (!$recursion) {
                $jsonApi['included'] = array_merge($jsonApi['included'], $included);

                $duplicated = [];
                for ($i = count($jsonApi['included'])-1; $i >= 0; $i--) {
                    if (!empty($duplicated[$jsonApi['included'][$i]['type']][$jsonApi['included'][$i]['id']])) {
                        unset($jsonApi['included'][$i]);
                    } else {
                        $duplicated[$jsonApi['included'][$i]['type']][$jsonApi['included'][$i]['id']] = true;
                    }
                }

                $jsonApi['included'] = array_values($jsonApi['included']);
            }

            return $jsonApi;
        }

    }


    if (!function_exists('jsonapi_denormalize')) {

        /**
        * Denormalize data.
        *
        * This takes data from the 'included' section and potentially duplicates
        * entities throughout the various relationships. AKA "hydration".
        */
        function jsonapi_denormalize($jsonApi, &$included = null) {
            if (!($recursion = $included !== null)) {
                if (!isset($jsonApi['included'])) {
                    return $jsonApi;
                }
                $included = &$jsonApi['included'];
            }

            $multiple = !isset($jsonApi['data']['type']) || !isset($jsonApi['data']['id']);
            foreach ($multiple ? $jsonApi['data'] : [&$jsonApi['data']] as &$datum) {
                foreach ($included as $include) {
                    if (($datum['type'] === $include['type']) && ($datum['id'] === $include['id'])) {
                        $datum = $include;
                        break;
                    }
                }

                if (isset($datum['relationships'])) {
                    foreach ($datum['relationships'] as &$relationship) {
                        $relationship = jsonapi_denormalize($relationship, $included);
                    }
                }
            }

            unset($jsonApi['included']);

            return $jsonApi;
        }

    }
}
