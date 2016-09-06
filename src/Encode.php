<?php
/**
 * The file for the encode-file service
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2016 Jack Clayton 
 * @license    MIT
 */

namespace Jstewmc\EncodeFile;

use BadMethodCallException;
use InvalidArgumentException;
use OutOfBoundsException;


/**
 * The encode-file service
 *
 * @since  0.1.0
 * @since  0.2.0  refactor to use read- and write-file services
 */
class Encode
{
    /* !Private properties */
    
    /**
     * @var    Read  the read-file service
     * @since  0.2.0
     */
    private $read;
    
    /**
     * @var    Write  the write-file service
     * @since  0.2.0
     */
    private $write;
    
    
    /* !Magic methods */
    
    /**
     * Called when the service is constructed
     *
     * @param   Read   $read   the read-file service
     * @param   Write  $write  the write-file service
     * @throws  BadMethodCallException  if "mbstring" extension is not loaded
     * @since   0.2.0  
     */
    public function __construct(Read $read, Write $write)
    {
        // if the "mbstring" extension is not loaded, short-circuit
        if ( ! extension_loaded('mbstring')) {
            throw new BadMethodCallException(
                "This library requires the 'mbstring' PHP extension"
            );
        }
        
        $this->read  = $read;
        $this->write = $write;
    }
    
    /**
     * Called when the service is treated like a function
     *
     * @param   string  $filename  the file's name
     * @param   string  $to        the "to" encoding (optional, defaults to "UTF-8")
     * @param   string  $from      the "from" encoding (optional, will be detected)
     * @return  void
     * @throws  InvalidArgumentException  if $to is not a valid encoding
     * @throws  InvalidArgumentException  if $from is neither valid encoding nor null
     * @throws  OutOfBoundsException      if $from is null and cannot be detected
     * @since   0.2.0
     */
    public function __invoke(
        string $filename, 
        string $to = 'UTF-8', 
        string $from = null
    ) {
        // if the "to" encoding is not valid, short-circuit
        if ( ! in_array($to, mb_list_encodings())) {
            throw new InvalidArgumentException(
                __METHOD__ . "() expects parameter two, to, to be a valid encoding"
            );
        }
        
        // if the "from" encoding is given and not valid, short-circuit
        if ($from !== null && ! in_array($from, mb_list_encodings())) {
            throw new InvalidArgumentException(
                __METHOD__ . "() expects parameter three, from, to be a valid "
                    . "encoding or null"
            );
        }
        
        // if "from" and "to" encodings are the same, short-circuit
        if ($from === $to) {
            return;
        }
        
        // otherwise, get the file's contents
        $contents = ($this->read)($filename);
        
        // if the contents are already "to" encoded, short-circuit
        if (mb_check_encoding($contents, $to)) {
            return;   
        }
        
        // otherwise, if a "from" encoding does not exist, attempt to detect it
        if ( ! $from) {
            $from = mb_detect_encoding($contents, mb_detect_order(), true);
        }
        
        // if a "from" encoding was not given or could not be detected, short-circuit
        if ( ! $from) {
            throw new OutOfBoundsException(
                __METHOD__ . "() could not detect the file's character encoding"
            );    
        }
            
        // otherwise, convert the contents encoding
        $contents = mb_convert_encoding($contents, $to, $from);
        
        // save the encoded contents
        ($this->write)($filename, $contents);
        
        return;
    } 
}
