<?php

namespace Tests\Features;

use App\Attendize\Utils;
use Tests\TestCase;

class UtilsTest extends TestCase
{
    /**
     * @test
     */
    public function parse_version_correctly()
    {
        $parsed_version = Utils::parse_version('1.1.0');
        $this->assertEquals($parsed_version, '1.1.0');
        $parsed_version = Utils::parse_version('Version 1.1.0 RC');
        $this->assertEquals($parsed_version, '1.1.0');
        $parsed_version = Utils::parse_version('<script>alert(1)</script>');
        $this->assertEquals($parsed_version, '');
    }
}
