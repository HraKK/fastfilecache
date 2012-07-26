<?php

class Core_Cache_Data_Type_String
{
    public function untype( $data )
    {
        return (string) $data;
    }
    public function type( $data )
    {
        return (string) $data;
    }
}