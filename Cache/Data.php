<?php

class Core_Cache_Data implements Core_Cache_Data_Interface
{
    protected $data;
    protected $type;

    public function __construct( $data )
    {
        $this->type = gettype( $data );
        $this->data = $data;
    }

    public function untype()
    {
        $typeClass = 'Core_Cache_Data_Type_' . ucfirst( $this->type );
        if( class_exists( $typeClass ) )
        {        
            $typeObject = new $typeClass();        
            return $typeObject->untype( $this->data );
        }
        
        return $this->data;
    }

    public function type( $type )
    {
        $typeClass = 'Core_Cache_Data_Type_' . ucfirst( $type );
        if( class_exists( $typeClass ) )
        {
            $typeObject = new $typeClass();
            return $typeObject->type( $this->data );
        }
        return $this->data;
    }
    
    public function getType()
    {
        return $this->type;
    }

}