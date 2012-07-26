<?php

class Core_Cache_Exception extends Exception
{
    const UNABLE_SAVE_RESOURCE = 2;
    const READ_CONTROL = 4;
    const NO_FOUND_STORAGE = 8;
    const NO_FOUND_DRIVER = 16;
    const NO_FOUND_METADATA = 32;
    const ERROR_SAVE = 64;
    const ERROR_REMOVE = 128;
    const ERROR_WRITABLE = 256;
    const ERROR_CREATE = 512;
}
