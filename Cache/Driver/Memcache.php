<?php

class Core_Cache_Driver_Memcache implements Core_Cache_Driver_Interface
{   
	protected $Memcache; 
    protected $options;

    public function __construct( $options = array() )
    {    	
        $this->options = $options;
        $this->Memcache = new Memcache();
      	$this->Memcache->connect( $options['host'], $options['port'] );         
    }

    public function setOptions( array $options )
    {
        $this->options = array_merge( $this->options, $options);
        return $this;
    }

    public function get( $key )
    {
        if( false !== $result = $this->Memcache->get( $key ) )
        {
        	return $result;
        }
        else
        {
        	throw new Core_Cache_Exception( 'Empty', Core_Cache_Exception::STORAGE_EMPTY );
        }
    }

    public function set( $key, $data )
    {    	
       if( false === $this->Memcache->set( $key, $data, false, $this->options['expire'] ) )
       {
       		return $this->Memcache->replace( $key, $data, false, $this->options['expire'] );
       }
       return true;
    }

    public function remove( $key )
    {        
        return $this->Memcache->delete( $key );
    }
}