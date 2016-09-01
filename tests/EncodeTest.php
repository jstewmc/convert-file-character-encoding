<?php
/**
 * The file for the encode-file service tests
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2016 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\EncodeFile;

use Jstewmc\ReadFile\Read;
use Jstewmc\WriteFile\Write;
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
     * __construct() should set the properties
     */
    public function testConstructSetsProperties()
    {
        $read  = new Read();
        $write = new Write();
        
        $service = new Encode($read, $write);
        
        $this->assertSame($read, $this->getProperty('read', $service));
        $this->assertSame($write, $this->getProperty('write', $service));
        
        return;
    }
    
    
    /* !__invoke() */
    
    /**
     * __invoke() should throw exception if the "to" encoding is not valid
     */
    public function testInvokeThrowsExceptionIfToIsNotValid()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        $filename = vfsStream::url('test/foo.txt');
        
        file_put_contents($filename, 'foo');
        
        (new Encode(new Read(), new Write()))($filename, 'foo');
        
        return;
    }
    
    /**
     * __invoke() should throw exception if the "from" encoding is not valid
     */
    public function testInvokeThrowsExceptionIfFromIsNotValid()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        $filename = vfsStream::url('test/foo.txt');
        
        file_put_contents($filename, 'foo');
        
        (new Encode(new Read(), new Write()))($filename, 'UTF-8', 'foo');
        
        return;
    }
    
    /**
     * __invoke() should return void if the encodings are equal
     */
    public function testConstructThrowsExceptionIfEncodingsAreSame()
    {
        $filename = vfsStream::url('test/foo.txt');
        
        file_put_contents($filename, 'foo');
        
        (new Encode(new Read(), new Write()))($filename, 'UTF-8', 'UTF-8');
        
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
        $filename = vfsStream::url('test/foo.txt');
    
        // set the file's contents to a UTF-8 string
        $contents = mb_convert_encoding('foo', 'UTF-8');
        
        file_put_contents($filename, $contents);
        
        (new Encode(new Read(), new Write()))($filename, 'UTF-8');
        
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
        $filename = vfsStream::url('test/foo.txt');
    
        // set the file's contents to an ASCII string
        $contents = mb_convert_encoding('foo', 'ASCII');
        
        file_put_contents($filename, $contents);
        
        // assert that the file's contents are not valid UTF-32
        $this->assertFalse(mb_check_encoding($contents, 'UTF-32'));
        
        (new Encode(new Read(), new Write()))($filename, 'UTF-32');
        
        $contents = file_get_contents($filename);
        
        // assert that the file's contents are UTF-32
        $this->assertTrue(mb_check_encoding($contents, 'UTF-32'));
        
        return;
    }
}
