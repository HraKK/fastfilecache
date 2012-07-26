<?php

/**
 * Factory cache storage.
 *
 */

abstract class Core_Cache_Storage
{
    static function factory( $options )
    {
        if( class_exists( 'Core_Cache_Driver_'.ucfirst( $options['driver'] ) ) )
        {
            $driverClass = 'Core_Cache_Driver_'.$options['driver'];
            return new $driverClass( $options );
        }
        else
        {
            throw new Core_Cache_Exception( 'Not exists driver Core_Cache_Driver_'.ucfirst( $options['driver'] ), Core_Cache_Exception::NO_FOUND_DRIVER );
            return false;
        }
    }
}