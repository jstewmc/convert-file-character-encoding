<?php
/**
 * The file for the read-file service interface
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2016 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\EncodeFile;

/**
 * A read-file service interface
 *
 * @since  0.1.0
 */
interface Read
{
    /**
     * Called when the service is treated like a function
     *
     * @param   string  $filename  the file's name
     * @return  string
     * @since   0.1.0
     */ 
    public function __invoke(string $filename): string;
}
