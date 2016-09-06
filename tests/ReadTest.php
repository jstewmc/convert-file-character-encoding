<?php
/**
 * The file for the read-file service tests
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2016 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\EncodeFile;

use Jstewmc\TestCase\TestCase;

/**
 * Tests for the read-file service interface
 */
class ReadTest extends TestCase
{
    public function testSyntax()
    {
        $class = new class implements Read {
            public function __invoke(string $filename): string {
                return 'foo';
            }  
        };
        
        return;
    } 
}
