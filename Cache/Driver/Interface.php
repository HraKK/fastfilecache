<?php

interface Core_Cache_Driver_Interface
{
    /**
     * Return cache data from file
     *
     * @param string $alias
     */
    public function get( $alias );

    /**
     * Save cache data to file
     *
     * @param string $alias
     * @param mixed $data
     */
    public function set( $alias, $data );

    /**
     * Remove cache data from file
     *
     * @param striing $alias
     */
    public function remove( $alias );
}