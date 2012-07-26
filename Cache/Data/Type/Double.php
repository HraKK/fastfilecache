<?php

class Core_Cache_Data_Type_Double
{
    public function untype( $data )
    {
        return (string) $data;
    }
    public function type( $data )
    {
        return (double) $data;
    }
}