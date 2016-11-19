<?php

namespace MovingImage\Test\Client\VMPro\Factory;

use Doctrine\Common\Annotations\AnnotationException;
use GuzzleHttp\Client;
use JMS\Serializer\Serializer;
use Monolog\Logger;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Entity\Channel;
use MovingImage\Client\VMPro\Manager\TokenManager;
use MovingImage\Test\Client\VMPro\ApiClient\AbstractApiClientImpl;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractApiClientFactoryTest.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class AbstractApiClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractApiClientFactoryImpl
     */
    private $factory;

    /**
     * Set up an instance of our impl class for AbstractApiClient just
     * for this test suite.
     */
    public function setUp()
    {
        $this->factory = new AbstractApiClientFactoryImpl();
    }

    /**
     * Assert whether ::createSerializer() yields an actual
     * JMS serializer instance.
     */
    public function testCreateSerializer()
    {
        $this->assertInstanceOf(Serializer::class, $this->factory->createSerializer());
    }

    /**
     * Assert whether the Serializer instance created by the factory
     * support auto loading annotations.
     */
    public function testSerializerCanUseAnnotations()
    {
        $serializer = $this->factory->createSerializer();
        $data = [
            'id' => 5,
            'name' => 'root_channel',
        ];

        try {
            $serializer->deserialize(json_encode($data), Channel::class, 'json');
        } catch (AnnotationException $e) {
            $this->fail('Could not autoload annotations for serialization classes');
        }
    }

    /**
     * Assert whether calling ::createTokenManager() yields an actual
     * instance of TokenManager.
     */
    public function testCreateTokenManager()
    {
        $creds = new ApiCredentials('example@example.com', 'lkdjfklsdjflkd');
        $tokenManager = $this->factory->createTokenManager('http://google.com', $creds);

        $this->assertInstanceOf(TokenManager::class, $tokenManager);
    }

    /**
     * Assert whether calling ::create() yields an instance of our
     * abstract api client impl class and that when no logger instance is
     * passed, it creates an actual new logger instance internally.
     */
    public function testCreateWithoutLogger()
    {
        $logger = new Logger('test');
        $client = $this->factory->create(new Client(), $this->factory->createSerializer());

        $this->assertInstanceOf(AbstractApiClientImpl::class, $client);
        $this->assertInstanceOf(LoggerInterface::class, $client->getLogger());
        $this->assertNotEquals($logger, $client->getLogger());
    }

    /**
     * Assert whether it actually stores the LoggerInterface instance
     * we provide to ::create().
     */
    public function testCreateWithLogger()
    {
        $logger = new Logger('test');
        $client = $this->factory->create(new Client(), $this->factory->createSerializer(), $logger);

        $this->assertInstanceOf(LoggerInterface::class, $client->getLogger());
        $this->assertEquals($logger, $client->getLogger());
    }
}
