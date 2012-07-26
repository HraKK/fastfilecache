<?php

class Core_Cache_Data_Type_Array
{
    public function untype( $data )
    {
        return serialize( $data );
    }
    public function type( $data )
    {
        return (array) unserialize( $data );
    }
}