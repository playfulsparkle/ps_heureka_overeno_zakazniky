<?php

namespace Heureka;

use Heureka\ShopCertification\IRequester;
use Mockery;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * @author Jakub Chábek <jakub.chabek@heureka.cz>
 */
class ShopCertificationTest extends \PHPUnit\Framework\TestCase
{

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testLogSetOrderWithInvalidOrderId(): void
    {
        $requester = Mockery::mock('\Heureka\ShopCertification\IRequester');
        $requester->shouldReceive('setApiEndpoint')
            ->once()
            ->with(Mockery::type('\Heureka\ShopCertification\ApiEndpoint'));

        $shopCertification = new ShopCertification('xxxxxxxx', [], $requester);

        $this->expectException('\Heureka\ShopCertification\InvalidArgumentException');
        $shopCertification->setOrderId(str_repeat('x', 256));
    }

    public function testLogOrderSuccess(): void
    {
        $requester = Mockery::mock('\Heureka\ShopCertification\IRequester');
        $requester->shouldReceive('setApiEndpoint')
            ->once()
            ->with(Mockery::type('\Heureka\ShopCertification\ApiEndpoint'));

        $response = Mockery::mock('\Heureka\ShopCertification\Response');
        $response->code = 200;
        $response->message = 'ok';

        $postData = [
            'apiKey' => $apiKey = 'xxxxxxxxxx',
            'email' => $email = 'john@doe.com',
            'orderId' => $orderId = 12345,
            'productItemIds' => [
                $product1 = 'ab12345',
                $product2 = '123459',
            ],
        ];

        $requester->shouldReceive('request')
            ->once()
            ->with(IRequester::ACTION_LOG_ORDER, Mockery::mustBe([]), Mockery::mustBe($postData))
            ->andReturn($response);

        $shopCertification = new ShopCertification($apiKey, [], $requester);
        $shopCertification->setEmail($email);
        $shopCertification->setOrderId($orderId);
        $shopCertification->addProductItemId($product1);
        $shopCertification->addProductItemId($product2);
        $result = $shopCertification->logOrder();

        $this->assertInstanceOf('\Heureka\ShopCertification\Response', $result);
    }

    public function testLogOrderSuccessWithoutOptionalFields(): void
    {
        $requester = Mockery::mock('\Heureka\ShopCertification\IRequester');
        $requester->shouldReceive('setApiEndpoint')
            ->once()
            ->with(Mockery::type('\Heureka\ShopCertification\ApiEndpoint'));

        $response = Mockery::mock('\Heureka\ShopCertification\Response');
        $response->code = 200;
        $response->message = 'ok';

        $postData = [
            'apiKey' => $apiKey = 'xxxxxxxxxx',
            'email' => $email = 'john@doe.com',
        ];

        $requester->shouldReceive('request')
            ->once()
            ->with(IRequester::ACTION_LOG_ORDER, Mockery::mustBe([]), Mockery::mustBe($postData))
            ->andReturn($response);

        $shopCertification = new ShopCertification($apiKey, [], $requester);
        $shopCertification->setEmail($email);
        $result = $shopCertification->logOrder();

        $this->assertInstanceOf('\Heureka\ShopCertification\Response', $result);
    }

    public function testLogOrderMissingEmail(): void
    {
        $requester = Mockery::mock('\Heureka\ShopCertification\IRequester');
        $requester->shouldReceive('setApiEndpoint')
            ->once()
            ->with(Mockery::type('\Heureka\ShopCertification\ApiEndpoint'));

        $shopCertification = new ShopCertification('xxxxxxxxxx', [], $requester);

        $this->expectException('\Heureka\ShopCertification\MissingInformationException');
        $shopCertification->logOrder();
    }

    public function testLogOrderDoubleSend(): void
    {
        $requester = Mockery::mock('\Heureka\ShopCertification\IRequester');
        $requester->shouldReceive('setApiEndpoint')
            ->once()
            ->with(Mockery::type('\Heureka\ShopCertification\ApiEndpoint'));

        $response = Mockery::mock('\Heureka\ShopCertification\Response');
        $response->code = 200;
        $response->message = 'ok';

        $postData = [
            'apiKey' => $apiKey = 'xxxxxxxxxx',
            'email' => $email = 'john@doe.com',
            'productItemIds' => [
                $product1 = 'ab12345',
                $product2 = '123459',
            ],
        ];

        $requester->shouldReceive('request')
            ->once()
            ->with(IRequester::ACTION_LOG_ORDER, Mockery::mustBe([]), Mockery::mustBe($postData))
            ->andReturn($response);

        $shopCertification = new ShopCertification($apiKey, [], $requester);
        $shopCertification->setEmail($email);
        $shopCertification->addProductItemId($product1);
        $shopCertification->addProductItemId($product2);
        $shopCertification->logOrder();

        $this->expectException('\Heureka\ShopCertification\Exception');

        $shopCertification->logOrder();
    }

}
