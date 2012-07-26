<?php

abstract class Core_Cache_Abstract implements Core_Cache_Interface
{
	const OLD = 1;
	const TAG = 2;
	const ALL = 3;

	protected $options = array(
	'driver'         => 'file',
	'host'			 => 'localhost',
	'port'			 => '11211',
	'cacheDirectory' => '../cache/data/',
	'metaDirectory' => '../cache/meta/',
	'tagsDirectory' => '../cache/tags/',
	'prefix'         => 'xx_',
	'expire'         => 0,
	'readControl'    => false,
	'tags'           => array(),
	'debug'          => false,
	'type'           => ''
	);

    /**
     * Apply options for current storage
     *
     * @param array $options
     * @return Core_Cache_Interface
     */
	public function setOptions( array $options )
	{

		$this->options = array_merge( $this->options, $options);
		return $this;
	}

	protected function debug(  $e )
	{
		if( $this->options['debug'] )
		{
			print_r( $e );
		}
	}
}