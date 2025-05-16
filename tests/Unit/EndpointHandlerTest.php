<?php

use PHPUnit\Framework\TestCase;
use CUT\EndpointHandler;

class EndpointHandlerTest extends TestCase
{
    public function testAddQueryVars()
    {
        $vars = ['foo'];
        $expected = ['foo', 'cut_user_directory'];
        $this->assertEquals($expected, EndpointHandler::add_query_vars($vars));
    }
}
