<?php

/**
 * Return the debug state
 * 
 * @return boolean
 */
function isDebugging()
{
    return false;
}

/**
 * Return the base url for creating absolute urls
 * 
 * @return string
 */
function base_url()
{
    return 'http://localhost/daytime';
}

/**
 * Get the path for a folder
 * 
 * @param string $where
 * @return string
 */
function path($where = 'base')
{
    $paths = array(
        'base'              => __DIR__.'/..',
        'app'               => __DIR__,
        'templates'         => __DIR__.'/templates',
        'templates.storage' => __DIR__.'/storage/templates',
    );
    
    return $paths[$where];
}
