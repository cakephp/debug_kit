<?php
/**
 * DebugKit configuration options.
 *
 * Copy this file to your application's config directory and include it
 * in your bootstrap or application configuration.
 *
 * All options shown below use their default values.
 */
return [
    'DebugKit' => [
        /**
         * Enable or disable panels for DebugKit. You can disable any of the
         * standard panels by setting them to false.
         *
         * Example: ['DebugKit.Packages' => false]
         */
        // 'panels' => [],

        /**
         * Set to true to enable logging of schema reflection queries.
         * Disabled by default.
         */
        // 'includeSchemaReflection' => false,

        /**
         * Set an array of whitelisted TLDs for local development.
         * This can be used to make sure DebugKit displays on hosts
         * it otherwise determines unsafe.
         *
         * Example: ['test', 'local', 'example']
         */
        // 'safeTld' => [],

        /**
         * Force DebugKit to display. Careful with this, it is usually
         * safer to simply whitelist your local TLDs.
         *
         * Can also be set to a callable that returns a boolean.
         * Example: function() { return $_SERVER['REMOTE_ADDR'] === '192.168.2.182'; }
         */
        // 'forceEnable' => false,

        /**
         * Regex pattern (including delimiter) to ignore paths.
         * DebugKit won't save data for request URLs that match this regex.
         *
         * Example: '/\.(jpg|png|gif)$/'
         */
        // 'ignorePathsPattern' => null,

        /**
         * Defines how many levels of nested data should be shown in general
         * for debug output.
         *
         * WARNING: Increasing the max depth level can lead to an out of memory error.
         */
        // 'maxDepth' => 5,

        /**
         * Defines how many levels of nested data should be shown in the
         * variables tab.
         *
         * WARNING: Increasing the max depth level can lead to an out of memory error.
         */
        // 'variablesPanelMaxDepth' => 5,

        /**
         * Number of requests to keep in history panel.
         */
        // 'requestCount' => 20,
    ],
];
