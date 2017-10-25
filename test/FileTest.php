<?php

namespace LeagueTest\Uri\Schemes;

use League\Uri\Schemes\File;
use League\Uri\Schemes\UriException;
use PHPUnit\Framework\TestCase;

/**
 * @group file
 */
class FileTest extends TestCase
{
    public function testDefaultConstructor()
    {
        $this->assertSame('', (string) File::createFromString());
    }

    /**
     * @dataProvider validUri
     * @param $expected
     * @param $input
     */
    public function testCreateFromString($uri, $expected)
    {
        $this->assertSame($expected, (string) File::createFromString($uri));
    }

    public function validUri()
    {
        return [
            'relative path' => [
                '.././path/../is/./relative',
                '.././path/../is/./relative',
            ],
            'absolute path' => [
                '/path/is/absolute',
                '/path/is/absolute',
            ],
            'empty path' => [
                '',
                '',
            ],
            'with host' => [
                '//example.com/path',
                '//example.com/path',
            ],
            'with normalized host' => [
                '//ExAmpLe.CoM/path',
                '//example.com/path',
            ],
            'with empty host' => [
                '///path',
                '///path',
            ],
            'with scheme' => [
                'file://localhost/path',
                'file://localhost/path',
            ],
            'with normalized scheme' => [
                'FiLe://localhost/path',
                'file://localhost/path',
            ],
            'with empty host and scheme' => [
                'FiLe:///path',
                'file:///path',
            ],
        ];
    }

    /**
     * @dataProvider invalidArgumentExceptionProvider
     */
    public function testConstructorThrowInvalidArgumentException($uri)
    {
        $this->expectException(UriException::class);
        File::createFromString($uri);
    }

    public function invalidArgumentExceptionProvider()
    {
        return [
            'no authority 1' => ['file:example.com'],
            'no authority 2' => ['file:/example.com'],
            'query string' => ['file://example.com?'],
            'fragment' => ['file://example.com#'],
            'user info' => ['file://user:pass@example.com'],
            'port' => ['file://example.com:42'],
        ];
    }

    /**
     * @dataProvider unixpathProvider
     * @param $input
     */
    public function testCreateFromUnixPath($uri, $expected)
    {
        $this->assertSame($expected, (string) File::createFromUnixPath($uri));
    }

    public function unixpathProvider()
    {
        return [
            'relative path' => [
                'input' => 'path',
                'expected' => 'path',
            ],
            'absolute path' => [
                'input' => '/path',
                'expected' => 'file:///path',
            ],
            'path with empty char' => [
                'input' => '/path empty/bar',
                'expected' => 'file:///path%20empty/bar',
            ],
            'relative path with dot segments' => [
                'input' => 'path/./relative',
                'expected' => 'path/./relative',
            ],
            'absolute path with dot segments' => [
                'input' => '/path/./../relative',
                'expected' => 'file:///path/./../relative',
            ],
        ];
    }

    /**
     * @dataProvider windowLocalPathProvider
     * @param $input
     */
    public function testCreateFromWindowsLocalPath($uri, $expected)
    {
        $this->assertSame($expected, (string) File::createFromWindowsPath($uri));
    }

    public function windowLocalPathProvider()
    {
        return [
            'relative path' => [
                'input' => 'path',
                'expected' => 'path',
            ],
            'relative path with dot segments' => [
                'input' => 'path\.\relative',
                'expected' => 'path/./relative',
            ],
            'absolute path' => [
                'input' => 'c:\windows\My Documents 100%20\foo.txt',
                'expected' => 'file:///c:/windows/My%20Documents%20100%2520/foo.txt',
            ],
            'windows relative path' => [
                'input' => 'c:My Documents 100%20\foo.txt',
                'expected' => 'file:///c:My%20Documents%20100%2520/foo.txt',
            ],
            'absolute path with `|`' => [
                'input' => 'c|\windows\My Documents 100%20\foo.txt',
                'expected' => 'file:///c:/windows/My%20Documents%20100%2520/foo.txt',
            ],
            'windows relative path with `|`' => [
                'input' => 'c:My Documents 100%20\foo.txt',
                'expected' => 'file:///c:My%20Documents%20100%2520/foo.txt',
            ],
            'absolute path with dot segments' => [
                'input' => '\path\.\..\relative',
                'expected' => '/path/./../relative',
            ],
            'absolute UNC path' => [
                'input' => '\\\\server\share\My Documents 100%20\foo.txt',
                'expected' => 'file://server/share/My%20Documents%20100%2520/foo.txt',
            ],
        ];
    }
}
