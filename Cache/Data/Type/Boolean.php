<?php

class Core_Cache_Data_Type_Boolean
{
    public function untype( $data )
    {
        return (string) $data;
    }
    public function type( $data )
    {
        return (boolean) $data;
    }
}