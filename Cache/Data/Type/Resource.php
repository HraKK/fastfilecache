<?php

class Core_Cache_Data_Type_Resource
{
    public function untype( $data )
    {
        throw new Core_Cache_Exception( 'Resource type cant stored', Core_Cache_Exception::UNABLE_SAVE_RESOURCE );
        return '';
    }
    public function type( $data )
    {
        return '';
    }
}