<?php

class Core_Cache_Data_Type_Null
{
    public function untype( $data )
    {
        return null;
    }
    public function type( $data )
    {
        return null;
    }
}