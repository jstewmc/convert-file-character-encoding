<?php
/**
 * The file for the encode-file service
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2016 Jack Clayton 
 * @license    MIT
 */

namespace Jstewmc\EncodeFile;

use InvalidArgumentException;
use OutOfBoundsException;
use RuntimeException;

/**
 * The encode-file service
 *
 * @since  0.1.0
 */
class Encode
{
    /* !Private properties */
    
    /**
     * @var    string  the name of the "from" encoding (optional)
     * @since  0.1.0
     */
    private $from;
    
    /**
     * @var    string  the name of the "to" encoding
     * @since  0.1.0
     */
    private $to;
    
    
    /* !Magic methods */
    
    /**
     * Called when the service is constructed
     *
     * @param   string  $to    the name of the "to" encoding (optional; if omitted, 
     *     defaults to "UTF-8")
     * @param   string  $from  the name of the "from" encoding (optional; if omitted,
     *     will attempt to be detected, which is not always accurate)
     * @throws  RuntimeException          if "mbstring" library is not loaded
     * @throws  InvalidArgumentException  if $to is not a valid encoding name
     * @throws  InvalidArgumentException  if $from is not a valid encoding name
     * @throws  InvalidArgumentException  if $to and $from are same encoding
     * @since   0.1.0
     */
    public function __construct(string $to = 'UTF-8', string $from = null)
    {
        // if the "mbstring" extension is not loaded, short-circuit
        if ( ! extension_loaded('mbstring')) {
            throw new RuntimeException(
                "This library requires the 'mbstring' PHP extension"
            );
        }
        
        // if $to is invalid encoding, short-circuit
        if ( ! in_array($to, mb_list_encodings())) {
            throw new InvalidArgumentException(
                __METHOD__ . "() expects parameter one, to, to be a valid "
                    . "mbstring character-encoding name"
            );
        }
        
        // if $from is given but invalid encoding, short-circuit
        if ($from !== null && ! in_array($from, mb_list_encodings())) {
            throw new InvalidArgumentException(
                __METHOD__ . "() expects parameter two, from, to be a valid "
                    . "mbstring character-encoding name or null"
            );
        }
        
        // if $from and $to are the same encoding, short-circuit
        if ($from === $to) {
            throw new InvalidArgumentException(
                __METHOD__ . "() expects parameter one, to, and parameter two, "
                    . "from, to be different character-encodings"
            );
        }
        
        $this->to   = $to;
        $this->from = $from;
    }
    
    /**
     * Called when the service is treated like a function
     *
     * @param   string  $filename  the file's name
     * @return  void
     * @throws  InvalidArgumentException  if the file is not readable
     * @throws  InvalidArgumentException  if the file is not writeable
     * @throws  UnexpectedValueException  if the file's "from" encoding is not 
     *     given and cannot be detected
     * @since   0.1.0
     */
    public function __invoke(string $filename)
    {
        // get the file's contents
        $contents = $this->read($filename);
        
        // if the contents are already "to" encoded, short-circuit
        if ($this->check($contents)) {
            return;   
        }
        
        // otherwise, if a "from" encoding does not exist, attempt to detect it
        if ( ! $this->from) {
            $this->from = $this->detect($contents);
        }
            
        // convert the contents' encoding
        $contents = $this->convert($contents);
        
        // save the encoded contents
        $this->write($filename, $contents);
        
        return;
    } 
    
    
    /* !Private methods */
    
    /**
     * Returns true if the contents are "to" encoded
     *
     * @param   string  $contents  the file's contents
     * @return  bool
     * @since   0.1.0
     */
    private function check(string $contents): bool
    {
        return mb_check_encoding($contents, $this->to);
    }
    
    /**
     * Converts the contents' character-encoding to the "to" encoding
     *
     * @param   string  $contents  the file's contents
     * @return  string
     * @since   0.1.0
     */
    private function convert(string $contents): string
    {
        return mb_convert_encoding($contents, $this->to, $this->from);
    }
    
    /**
     * Detects the contents' current character-encoding
     *
     * Keep in mind, this is not perfect! There are myriad issues attempting to
     * detect a string's character-encoding. For example, Windows-1252 is almost
     * never detected, even if the string is 100% Windows-1252. 
     *
     * @param   string  $contents  the file's contents
     * @return  string
     * @throws  OutOfBoundsException  if the contents' character-encoding could not
     *     be detected 
     * @since   0.1.0
     */
    private function detect(string $contents): string
    {
        $encoding = mb_detect_encoding($contents, mb_detect_order(), true);
        
        if ( ! $encoding) {
            throw new OutOfBoundsException(
                __METHOD__ . "() expects the file's current character-encoding to "
                    . "be detectable"
            );    
        }
        
        return $encoding;
    }
    
    /**
     * Returns the file's contents
     *
     * @param   string  $filename  the file's name
     * @return  string
     * @throws  InvalidArgumentException  if $filename is not readable
     * @since   0.1.0
     */
    private function read(string $filename): string
    {
        if ( ! is_readable($filename)) {
            throw new InvalidArgumentException(
                __METHOD__ . "() expects parameter one, filename, to be readable"
            );
        }
        
        return file_get_contents($filename);
    }
    
    /**
     * Writes the file's contents
     *
     * @param   string  $filename  the file's name
     * @param   string  $contents  the file's new contents
     * @return  void
     * @throws  InvalidArgumentException  if $filename is not writeable
     * @since   0.1.0
     */
    private function write(string $filename, string $contents)
    {
        if ( ! is_writeable($filename)) {
            throw new InvalidArgumentException(
                __METHOD__ . "() expects parameter one, filename, to be writeable"
            );
        }
        
        file_put_contents($filename, $contents);
        
        return;
    }
}
