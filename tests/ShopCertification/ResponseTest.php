<?php

namespace Heureka\ShopCertification;

/**
 * @author Jakub Chábek <jakub.chabek@heureka.cz>
 */
class ResponseTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @dataProvider providerConstruct
     *
     * @param string $json
     * @param array  $expected
     */
    public function testConstruct($json, $expected): void
    {
        $response = new Response($json);

        $this->assertSame($expected, (array) $response);
    }

    /**
     * @return array
     */
    public function providerConstruct(): array
    {
        return [
            [
                '{"code":200,"message":"ok"}',
                ['code' => 200, 'message' => 'ok', 'description' => null, 'resourceId' => null],
            ],
            [
                '{"code":404,"message":"not-found","description":"Resource does not exist.","resourceId":"sf812as12"}',
                [
                    'code' => 404,
                    'message' => 'not-found',
                    'description' => 'Resource does not exist.',
                    'resourceId' => 'sf812as12',
                ],
            ],
        ];
    }

    public function testConstructWithMissingFields(): void
    {
        $this->expectException('Heureka\ShopCertification\InvalidResponseException');

        new Response('{"message":"There was an error"}');
    }

    /**
     * @dataProvider providerConstructWithInvalidJsonResponse
     *
     * @param string $json
     */
    public function testConstructWithInvalidJsonResponse($json): void
    {
        $this->expectException('Heureka\ShopCertification\JsonException');
        
        new Response($json);
    }

    /**
     * @return array
     */
    public function providerConstructWithInvalidJsonResponse(): array
    {
        return [
            ['ok'],
            ['Chyba'],
            ['{'],
            ['{ok}'],
            [null],
            [true],
            [false],
            [0],
            [1],
        ];
    }

}
