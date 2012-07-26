<?php

class Core_Cache extends Core_Cache_Abstract
{
	protected $id;
	protected $Storage;
	protected $off = false;

	protected function getStorage()
	{
		if( null === $this->Storage )
		{
			try
			{
				$this->Storage = Core_Cache_Storage::factory( $this->options );
			}
			catch( Core_Cache_Exception $e )
			{
				$this->debug( $e );
			}
		}
		return $this->Storage;
	}

	public function get( $key )
	{
		
	    if( $this->off )
	    {
	        throw new Core_Cache_Exception();
	    }
		try
		{
			$data =  $this->getStorage()->get( $key );			
			return $data;
		}
		catch ( Core_Cache_Exception $e )
		{			
			$this->debug( $e );
			throw $e;
		}
	}

	public function set( $key, $data = null, $options = array() )
	{
	    if( $this->off )
	    {
	        return false;
	    }
		try
		{
			$this->getStorage()->setOptions( $options )->set( $key, $data );
		}
		catch ( Core_Cache_Exception $e )
		{
			$this->debug( $e );
			throw $e;
		}
		return true;
	}

	public function start( $key )
	{
		ob_start();
		$this->id = $key;
		return $this;
	}

	public function end()
	{
		$this->set( $this->id, ob_get_clean() );
	}

	public function remove( $key )
	{
		try
		{
			$this->getStorage()->remove( $key );
		}
		catch( Core_Cache_Exception $e )
		{
			$this->debug( $e );
			throw $e;
		}
		return true;
	}

	public function clean( $mode = Core_Cache::ALL, $tags = array() )
	{
		try
		{
			$this->getStorage()->clean( $mode, $tags );
		}
		catch( Core_Cache_Exception $e )
		{
			$this->debug( $e );
			throw $e;
		}
		return true;
	}
}