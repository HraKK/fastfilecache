<?php

class Core_Cache_Driver_File implements Core_Cache_Driver_Interface
{
    const DEEP = 3;
    const TAG_SEPARATOR = '^';
    protected $options;

    public function __construct( $options )
    {
        $this->options = $options;
        $this->checkDirectory( $options['cacheDirectory'] );
        $this->checkDirectory( $options['metaDirectory'] );
        $this->checkDirectory( $options['tagsDirectory'] );
    }

    public function setOptions( array $options )
    {

        $this->options = array_merge( $this->options, $options);
        return $this;
    }

    public function get( $key )
    {
        $fileStorage = $this->id2name( $key );
        if( !file_exists( $this->options['cacheDirectory'] . $fileStorage ) )
        {
            throw new Core_Cache_Exception( 'Storage ' . $fileStorage . ' not found', Core_Cache_Exception::NO_FOUND_STORAGE );
        }
        $metadata = $this->getMetadata( $key );

        if( $this->isExpire( $key, $metadata ) )
        {
            $this->remove( $key );
            return NULL;
        }

        if( $metadata['readControl'] )
        {
            if( md5_file( $this->options['cacheDirectory'] . $fileStorage ) != $metadata['hash'] )
            {
                throw new Core_Cache_Exception( 'Read control is failed in storage' . $fileStorage, Core_Cache_Exception::READ_CONTROL );
            }
        }

        $dataObject = new Core_Cache_Data( file_get_contents( $this->options['cacheDirectory'] . $fileStorage ) );
        return $dataObject->type( $metadata['type'] );
    }

    public function set( $key, $data )
    {
    	$dataObject =  new Core_Cache_Data( $data );
        $fileStorage = $this->id2name( $key );
        $this->checkDirectory( $this->options['cacheDirectory'] . $fileStorage );
        $this->options['type'] = $dataObject->getType();
        $data = $dataObject->untype();
        $this->options['hash'] = sha1( $data );
        if( false === file_put_contents( $this->options['cacheDirectory'] . $fileStorage, $data ) )
        {
            throw new Core_Cache_Exception( 'Error with save to storage ' . $fileStorage, Core_Cache_Exception::ERROR_SAVE );
        }
        try
        {
            $this->setMetadata( $key, $this->options );
        }
        catch ( Core_Cache_Exception  $e )
        {
            throw $e;
        }
    }

    public function remove( $key )
    {
        $fileStorage = $this->id2name( $key );
        if( is_file( $this->options['cacheDirectory'] . $fileStorage ) )
        {
            if( !is_writeable( $this->options['cacheDirectory'] . $fileStorage ) )
            {
                throw new Core_Cache_Exception( 'Cant remove storage ' . $this->options['cacheDirectory'] . $fileStorage, Core_Cache_Exception::ERROR_REMOVE );
            }
            @unlink( $this->options['cacheDirectory'] . $fileStorage );
        }
        if( $this->options['expire'] > 0)
        {
            $fileStorage = 'expire/' . $fileStorage;
        }
        if( is_file( $this->options['metaDirectory'] . $fileStorage ) )
        {
            if(  !is_writeable( $this->options['metaDirectory'] . $fileStorage ) )
            {
                throw new Core_Cache_Exception( 'Cant remove storage ' . $fileStorage, Core_Cache_Exception::ERROR_REMOVE );
            }
            @unlink( $this->options['metaDirectory'] . $fileStorage );
        }
        return true;
    }

    protected function isExpire( $key, $metadata )
    {
        $modifyTime = filemtime( $this->options['cacheDirectory'] . $this->id2name( $key ) );

        if( !empty( $metadata['expire'] ) )
        {
            if( $modifyTime + $metadata['expire'] < microtime( 1 ) )
            {
                return true;
            }
        }
        return false;
    }

    protected function getMetadata( $key )
    {
        $fileMetadataStorage = $this->id2name( $key  );

        if( is_file( $this->options['metaDirectory'] . $fileMetadataStorage ) )
        {
            $metadata = file_get_contents( $this->options['metaDirectory'] . $fileMetadataStorage );
        }
        elseif (is_file( $this->options['metaDirectory'] .'expire/'. $fileMetadataStorage ))
        {
            $metadata = file_get_contents( $this->options['metaDirectory'] .'expire/'. $fileMetadataStorage );
        }
        else
        {
            throw new Core_Cache_Exception( 'Cant fount metadata storage ' .$key, Core_Cache_Exception::NO_FOUND_METADATA );
        }
        return array_merge( array_fill_keys( array_keys( $this->options ), NULL ), (array) unserialize( $metadata ) );
    }

    protected function setMetadata( $key, $options )
    {
        $fileMetadataStorage = $this->id2name( $key );
        if( $options['expire'] > 0)
        {
            $fileMetadataStorage = 'expire/' . $fileMetadataStorage;
        }
        $this->checkDirectory($this->options['metaDirectory'] . $fileMetadataStorage);
        if( !file_put_contents( $this->options['metaDirectory'] . $fileMetadataStorage, serialize( $options ) ) )
        {
            throw new Core_Cache_Exception( 'Error with save to storage ' . $fileMetadataStorage, Core_Cache_Exception::ERROR_SAVE );
        }
        if( count( $this->options['tags'] ) )
        {
            $this->setTagAssign( $this->options['tags'], $key );
        }
    }

    protected function setTagAssign( array $tags, $key )
    {
        sort( $tags );
        $tags = self::TAG_SEPARATOR . join( self::TAG_SEPARATOR , $tags ) . self::TAG_SEPARATOR . '/';
        $md5=md5( $tags );
        $directory = $this->options['tagsDirectory'] . $tags;
        $file = $this->id2name( $key );
        if( $this->checkDirectory( $directory.$file ) )
        {
            touch( $directory . $file );
        }
    }

    protected function removeByTag( $tags )
    {
        $handle = opendir( $this->options['tagsDirectory'] );
        while (false !== ( $file = readdir( $handle ) ) )
        {
            if( $file == '.' || $file == '..' )
            {
                continue;
            }
            if( !is_dir( $this->options['tagsDirectory'] . $file ) )
            {
                continue;
            }
            $missed = false;
            foreach ( $tags as $tag)
            {
                if( false === strpos( $file, self::TAG_SEPARATOR . $tag . self::TAG_SEPARATOR ) )
                {
                    $missed = true;
                    break;
                }
            }
            if( $missed )
            {
                continue;
            }
            $files = glob( $this->options['tagsDirectory'] . $file . str_repeat( '/*', self::DEEP ) , GLOB_ONLYDIR );
            $count = count( $files );

            for( $i = 0; $i < $count; $i++ )
            {
                $this->RemoveRecursive( $files[$i], Core_Cache_Abstract::TAG );
            }
        }
    }

    protected function RemoveRecursive( $directory, $mod = false )
    {
        if( !is_dir($directory ) )
        {
            return false;
        }
        $handle = opendir( $directory );
        while( false !== ( $file = readdir( $handle ) ) )
        {
            if( $file == '.' || $file == '..' )
            {
                continue;
            }
            if( is_dir( $directory . '/' . $file ) )
            {
                $this->RemoveRecursive( $directory . '/' . $file, $mod );
            }
            elseif( is_file( $directory . '/' . $file ) )
            {
                switch ( $mod )
                {
                    case Core_Cache_Abstract::ALL :
                        $id = $this->name2id( $file );
                        $this->remove( $id );
                        break;
                    case Core_Cache_Abstract::TAG :
                        $id = $this->name2id( $file );
                        if ( $this->remove( $id ) )
                        {
                            @unlink( $directory . '/' . $file );
                        }
                        break;
                    case Core_Cache_Abstract::OLD :
                        $id = $this->name2id( $file );
                        $metadata = $this->getMetadata( $id );

                        if( $this->isExpire( $id, $metadata ) )
                        {
                            $this->remove( $id );
                        }
                        break;
                }
            }
        }
    }

    public function clean( $mode, $tags )
    {
        switch ( $mode)
        {
            case Core_Cache_Abstract::ALL :
                $files = glob( $this->options['cacheDirectory'] . str_repeat('/*', self::DEEP ), GLOB_ONLYDIR);
                $count = count( $files );
                for( $i = 0; $i < $count; $i++ )
                {
                    $this->RemoveRecursive( $files[$i], Core_Cache_Abstract::ALL );
                }
                break;
            case Core_Cache_Abstract::OLD :
                $files = glob( $this->options['metaDirectory'] . 'expire' . str_repeat( '/*', self::DEEP ), GLOB_ONLYDIR );
                $count = count( $files );
                for( $i = 0; $i < $count; $i++ )
                {
                    $this->RemoveRecursive( $files[$i], Core_Cache_Abstract::OLD );
                }
                break;
            case Core_Cache_Abstract::TAG :
                $this->removeByTag( $tags );
                break;
        }
    }

    protected function id2name( $key )
    {
        $name =  $this->options['prefix'] . $key ;
        $nameHash = sha1( $name );
        $path = '';
        for( $deep = 0; $deep <= self::DEEP; $deep ++ )
        {
            $path .= $nameHash[$deep] . '/';
        }
        return $path . $name;
    }

    protected function name2id( $name )
    {
        $name = explode('/', $name );
        $name = end( $name );
        return substr( $name, strlen( $this->options['prefix'] ) );
    }

    protected function checkDirectory( $cacheDirectory )
    {
        $cacheDirectory = dirname( $cacheDirectory );
        if( is_dir( $cacheDirectory ) )
        {
            if( !is_writeable( $cacheDirectory ) )
            {
                @chmod( $cacheDirectory, 0766 );
            }
            if( !is_writeable( $cacheDirectory ) )
            {
                throw new Core_Cache_Exception( 'Cache directory ' . $cacheDirectory . ' is not writable', Core_Cache_Exception::ERROR_WRITABLE );
                return false;
            }
            return true;
        }
        return $this->makeDirectory( $cacheDirectory );
    }

    protected function makeDirectory( $cacheDirectory )
    {
        $dir ='';
        $arrayDirectory = explode( '/', str_replace('\\', '/', rtrim( $cacheDirectory, '/\\' ) ) );
        $count = count( $arrayDirectory );

        for( $i = 0; $i < $count; $i++ )
        {
            $dir .= $arrayDirectory[$i] . '/';
            if( !is_dir( $dir ) )
            {
                if( @!mkdir( $dir, 0766 ) )
                {
                    throw new Core_Cache_Exception( 'Cache directory '. $cacheDirectory. ' is not writable', Core_Cache_Exception::ERROR_WRITABLE );
                    return false;
                }
            }
        }

        if( !is_dir( $cacheDirectory ) || !is_writable( $cacheDirectory ) )
        {
            throw new Core_Cache_Exception( 'cant create or make writable cache directory ' . $cacheDirectory, Core_Cache_Exception::ERROR_CREATE ) ;
        }
        return true;
    }
}
