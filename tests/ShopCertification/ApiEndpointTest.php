<?php

namespace Heureka\ShopCertification;

use Heureka\ShopCertification;

/**
 * @author Jakub Chábek <jakub.chabek@heureka.cz>
 */
class ApiEndpointTest extends \PHPUnit\Framework\TestCase
{

    public function testGetEndpoint(): void
    {
        $apiEndpoint = new ApiEndpoint(ShopCertification::HEUREKA_CZ);

        $this->assertSame(ApiEndpoint::API_ENDPOINT_CZ, $apiEndpoint->getUrl());

        $apiEndpoint = new ApiEndpoint(ShopCertification::HEUREKA_SK);

        $this->assertSame(ApiEndpoint::API_ENDPOINT_SK, $apiEndpoint->getUrl());

        $this->expectException('Heureka\ShopCertification\UnknownServiceException');
        
        new ApiEndpoint(15);
    }

}
