<?php

interface Core_Cache_Interface
{
    /**
     * Set options of cache:

     *
     * @param
     * driver - alias driver for cache, must exists Core_Cache_Driver_Alias class
     * @param
	 * cacheDirectory - directory where save cache data
	 * @param
	 * metaDirectory - directory where save cache metadata
	 * @param
	 * tagsDirectory - directory where save cache data
	 * @param
	 * prefix - prefix for cache objects
	 * @param
	 * expire - set time when cache object will be expired
	 * @param
	 * readControl - enable or disable read controll
	 * @param
	 * tags - array of cache tags
	 * @param
	 * debug - enable or disable debug info
	 * @param
	 * type - type current cache data
     * @return Core_Cache_Interface
     */
	public function setOptions( array $options );

	/**
	 * Return saved cache data
	 *
	 * @param string $alias
	 */
	public function get( $alias );

	/**
	 * Save cache data
	 *
	 * @param stirng $alias
	 * @param mixed $data
	 * @return Core_Cache_Interface
	 */
	public function set( $alias, $data = null );

    /**
     * Start capture php://output data
     *
     * @param string $alias
     * @return boolean true|false
     */
	public function start( $key );

	/**
	 * Save captured php://output data
	 *
	 * @return Core_Cache_Interface
	 */
	public function end();

	/**
	 * Remove cache data
	 *
	 * @param alias $key
	 * @return boolean true|false
	 */
	public function remove( $key );

	/**
	 * Clean cache data with some mods:
	 * Core_Cache_Abstract::OLD - delete all cache data with expired data
	 * Core_Cache_Abstract::TAG - delete all cache data with some tags
	 * Core_Cache_Abstract::ALL - delete all cache data
	 *
	 *
	 * @param int $mode
	 * @param array $tags
	 * @return boolean true|false
	 */
	public function clean( $mode = Core_Cache_Abstract::ALL, $tags = array() );
}