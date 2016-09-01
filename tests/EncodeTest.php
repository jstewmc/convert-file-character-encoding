<?php
/**
 * The file for the encode-file service tests
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2016 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\EncodeFile;

use Jstewmc\TestCase\TestCase;
use org\bovigo\vfs\{vfsStream, vfsStreamDirectory, vfsStreamFile};

/**
 * Tests for the encode-file service
 */
class EncodeTest extends TestCase
{
    /**
     * @var  vfsStreamDirectory  the "root" virtual file system directory
     */
    private $root;
    
	
	/* !Framework methods */
    
    /**
     * Called before every test
     *
     * @return  void
     */
    public function setUp()
    {
        $this->root = vfsStream::setup('test');
        
        return;
    }
    
    
    /* !__construct() */
    
    /**
     * __construct() should throw exception if the mbstring extension is not loaded
     */
    public function testConstructThrowsExceptionIfExtensionNotLoaded()
    {
        // hmm, there is no way to unload an extension to test this
    }
    
    /**
     * __construct() should throw exception if the "to" encoding is not valid
     */
    public function testConstructThrowsExceptionIfToIsNotValid()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        new Encode('foo');
        
        return;
    }
    
    /**
     * __construct() should throw exception if the "from" encoding is not valid
     */
    public function testConstructThrowsExceptionIfFromIsNotValid()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        new Encode('UTF-8', 'foo');
        
        return;
    }
    
    /**
     * __construct() should throw exception if the encodings are equal
     */
    public function testConstructThrowsExceptionIfEncodingsAreSame()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        new Encode('UTF-8', 'UTF-8');
        
        return;   
    }
    
    /**
     * __construct() should set the service's properties
     */ 
    public function testConstructSetsPropertiesIfEncodingsAreDifferent()
    {
        $to   = 'Windows-1252';
        $from = 'UTF-8';
        
        $service = new Encode($to, $from);
        
        $this->assertEquals($to, $this->getProperty('to', $service));
        $this->assertEquals($from, $this->getProperty('from', $service));
        
        return;
    }
    
    
    /* !__invoke() */
    
    /**
     * __invoke() should throw an exception if the file does not exist
     */
    public function testInvokeThrowsExceptionIfFileDoesNotExist()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        (new Encode())(vfsStream::url('test/path/to/file.php'));
        
        return;
    }
    
    /**
     * __invoke() should throw an exception if the file is not readable
     */
    public function testInvokeThrowsExceptionIfFileIsNotReadable()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        $filename = 'test/foo.txt';
        
        new vfsStreamFile($filename, 0000);
        
        (new Encode())(vfsStream::url($filename));
        
        return;
    }
     
    /**
     * __invoke() should throw exception if the "from" encoding cannot be detected
     */
    public function testInvokeThrowsExceptionIfFromEncodingIsNotDetectable()
    {
        // hmm, I'm not sure how to test this
    }
    
    /**
     * __invoke() should return void if the file is "to" encoded
     */
    public function testInvokeReturnsVoidIfTheFileIsToEncoded()
    {
        // set the file's name
        $filename = vfsStream::url('test/foo.txt');
    
        // set the file's contents to a UTF-8 string
        $contents = mb_convert_encoding('foo', 'UTF-8');
        
        // create the (readable) file
        file_put_contents($filename, $contents);
        
        // do not give the service a "from" encoding
        // keep in mind, this will force the service to detect the file's encoding
        //
        $service = new Encode('UTF-8');
        
        // convert the file's encoding
        // keep in mind, this should short-circuit without changing anything
        //
        $service($filename);
        
        // get the file's contents
        $contents = file_get_contents($filename);
        
        // assert that the file's contents are UTF-8
        $this->assertTrue(mb_check_encoding($contents, 'UTF-8'));
        
        return; 
    }
    
    /**
     * __invoke() should return void if the file is not "to" encoded
     */
    public function testInvokeReturnsVoidIftheFileIsNotToEncoded()
    {
        // set the file's name
        $filename = vfsStream::url('test/foo.txt');
    
        // set the file's contents to an ASCII string
        $contents = mb_convert_encoding('foo', 'ASCII');
        
        // create the (readable) file
        file_put_contents($filename, $contents);
        
        // assert that the file's contents are not valid UTF-32
        $this->assertFalse(mb_check_encoding($contents, 'UTF-32'));
        
        // do not give the service a "from" encoding...
        // keep in mind, this will force the service to detect the file's encoding
        //
        $service = new Encode('UTF-32');
        
        // convert the file's encoding
        $service($filename);
        
        // get the file's contents
        $contents = file_get_contents($filename);
        
        // assert that the file's contents are UTF-32
        $this->assertTrue(mb_check_encoding($contents, 'UTF-32'));
        
        return;
    }
    
    /**
     * __invoke() should throw an exception if the file is not writable
     */
    public function testInvokeThrowsExceptionIfFileIsNotWriteable()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        $filename = 'test/foo.txt';
        
        new vfsStreamFile($filename, 0444);
        
        (new Encode())(vfsStream::url($filename));
        
        return;
    }
}
