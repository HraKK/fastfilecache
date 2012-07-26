<?php

class Core_Cache_Data_Type_Integer
{
    public function untype( $data )
    {
        return (string) $data;
    }
    public function type( $data )
    {
        return (int) $data;
    }
}