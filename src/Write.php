<?php
/**
 * The file for the write-file service interface
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2016 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\EncodeFile;

/**
 * A write-file service interface
 *
 * @since  0.1.0
 */
interface Write
{
    /**
     * Called when the service is treated like a function
     *
     * @param   string  $filename  the file's name
     * @param   string  $contents  the file's contents
     * @return  int
     * @since   0.1.0
     */ 
    public function __invoke(string $filename, string $contents): int;
}
