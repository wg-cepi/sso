<?php
class UtilsTest extends PHPUnit_Framework_TestCase
{

    public function testGetClassName()
    {
        $this->assertEquals('ShortClassName', getClassName('ShortClassName'));
        $this->assertEquals('\ShortClassName', getClassName('\ShortClassName'));
        $this->assertEquals('Here', getClassName('\Some\Namespace\And\Class\Is\Here'));
    }

}