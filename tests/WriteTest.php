<?php
/**
 * The file for the write-file service tests
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2016 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\EncodeFile;

use Jstewmc\TestCase\TestCase;

/**
 * Tests for the write-file service interface
 */
class WriteTest extends TestCase
{
    public function testSyntax()
    {
        $class = new class implements Write {
            public function __invoke(string $filename, string $contents): int {
                return 1;
            }  
        };
        
        return;
    } 
}
