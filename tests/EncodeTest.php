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
    /* !Private properties */
    
    /**
     * @var  Read  the read-file service *stub*
     */
    private $read;

    /**
     * @var  Write  the write-file service *mock*
     */
    private $write;
    
	
	/* !Framework methods */
    
    /**
     * Called before every test
     *
     * I'll create a read-file *stub*, because we need to set the service's return
     * value. I'll also create a write-file *mock*, because we need to verify the
     * servier's arguments.
     *
     * @return  void
     */
    public function setUp()
    {
        // set the read *stub*
        $this->read = $this->createMock(Read::class);
        
        // set the write-file *mock*
        $this->write = $this->getMockBuilder(Write::class)
            ->setMethods(['__invoke'])
            ->getMock();
        
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
        $service = new Encode($this->read, $this->write);
        
        $this->assertSame($this->read, $this->getProperty('read', $service));
        $this->assertSame($this->write, $this->getProperty('write', $service));
        
        return;
    }
    
    
    /* !__invoke() */
    
    /**
     * __invoke() should throw exception if the "to" encoding is not valid
     */
    public function testInvokeThrowsExceptionIfToIsNotValid()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        // note the invalid "to" encoding
        (new Encode($this->read, $this->write))('foo.txt', 'foo');
        
        return;
    }
    
    /**
     * __invoke() should throw exception if the "from" encoding is not valid
     */
    public function testInvokeThrowsExceptionIfFromIsNotValid()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        // note the invalid "from" encoding
        (new Encode($this->read, $this->write))('foo.txt', 'UTF-8', 'foo');
        
        return;
    }
    
    /**
     * __invoke() should return void if the encodings are equal
     */
    public function testInvokeReturnsVoidIfEncodingsAreSame()
    {
        // note the "from" and "to" encodings are the same
        (new Encode($this->read, $this->write))('foo.txt', 'UTF-8', 'UTF-8');
        
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
        $to = 'UTF-8';
        
        // set the read-file service to return a UTF-8 string
        $this->read
            ->method('__invoke')
            ->willReturn(mb_convert_encoding('foo', $to));
            
        // encode the file
        (new Encode($this->read, $this->write))('foo.txt', $to);
        
        return; 
    }
    
    /**
     * __invoke() should return void if the file is not "to" encoded
     */
    public function testInvokeReturnsVoidIftheFileIsNotToEncoded()
    {
        // set our file's information...
        // keep in mind, UTF-32 will not be confused with ASCII, unlike UTF-8
        //
        $filename = 'foo.txt';
        $contents = 'foo';
        $to       = 'UTF-32';
        $from     = 'ASCII';
        
        // set the return value for the stub read-file service
        $this->read
            ->method('__invoke')
            ->willReturn(mb_convert_encoding($contents, $from));
            
        // set the argument for the mock write-file service
        $this->write
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                $this->equalTo($filename),
                $this->equalTo(mb_convert_encoding($contents, $to))
            );
        
        // encode the file
        (new Encode($this->read, $this->write))('foo.txt', $to);
        
        return;
    }
}
