<?php

namespace {

    if (!function_exists('jsonapi_normalize')) {

        /**
        * Normalize JSON API.
        *
        * This makes sure that entities in relationships are only references to
        * their data in the 'included' section. AKA "flattening".
        *
        * @api
        */
        function jsonapi_normalize($jsonApi) {
            return \Tobiassjosten\JsonApi\Normalizer::normalize($jsonApi);
        }

    }


    if (!function_exists('jsonapi_denormalize')) {

        /**
        * Denormalize data.
        *
        * This takes data from the 'included' section and potentially duplicates
        * entities throughout the various relationships. AKA "hydration".
        *
        * @api
        */
        function jsonapi_denormalize($jsonApi) {
            return \Tobiassjosten\JsonApi\Denormalizer::denormalize($jsonApi);
        }

    }
}
