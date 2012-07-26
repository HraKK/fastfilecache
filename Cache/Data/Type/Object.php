<?php

class Core_Cache_Data_Type_Object
{
    public function untype( $data )
    {
        return serialize( $data );
    }
    public function type( $data )
    {
        return (object) unserialize( $data );
    }
}