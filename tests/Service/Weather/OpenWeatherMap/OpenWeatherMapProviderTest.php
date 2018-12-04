<?php

namespace tests\Service\Weather\OpenWeatherMap;

use App\Entity\WeatherRecord;
use App\Event\MalformedApiResponseEvent;
use App\Event\WeatherApiEvents;
use App\Service\Weather\Exception\MalformedApiResponseException;
use App\Service\Weather\OpenWeatherMap\OpenWeatherMapProvider;
use App\Service\Weather\WeatherTranslatorInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OpenWeatherMapProviderTest extends TestCase
{
    /**
     * @var MockObject|Client
     */
    private $guzzleClient;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var MockObject|WeatherTranslatorInterface
     */
    private $translator;

    /**
     * @var OpenWeatherMapProvider
     */
    private $provider;

    /**
     * @var EventDispatcherInterface|MockObject
     */
    private $eventDispatcher;

    public function setUp()
    {
        $this->apiUrl = 'api.openweathermap.org/data/2.5/weather';
        $this->apiKey = '1234';

        $this->guzzleClient = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->translator = $this->getMockBuilder(WeatherTranslatorInterface::class)
            ->getMockForAbstractClass();

        $this->eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMockForAbstractClass();

        /** @var OpenWeatherMapProvider $provider */
        $this->provider = new OpenWeatherMapProvider(
            $this->apiUrl,
            $this->apiKey,
            $this->guzzleClient,
            $this->translator,
            $this->eventDispatcher
        );
    }

    public function testFetchByCity_HappyPath()
    {
        /**
         * @var ResponseInterface|MockObject
         */
        $guzzleResponse = $this->getMockBuilder(ResponseInterface::class)
            ->getMockForAbstractClass();

        /**
         * @var StreamInterface|MockObject
         */
        $responseBody = $this->getMockBuilder(StreamInterface::class)
            ->getMockForAbstractClass();

        $this->guzzleClient->expects($this->any())
            ->method('request')
            ->with('GET', 'api.openweathermap.org/data/2.5/weather?appId=1234&q=Krakow')
            ->willReturn($guzzleResponse);

        $guzzleResponse->expects($this->any())
            ->method('getBody')
            ->willReturn($responseBody);

        $responseContent = <<<EOT
{
    "coord":{"lon":19.94,"lat":50.06},
    "weather":[
        {"id":500,"main":"Rain","description":"light rain","icon":"10n"},
        {"id":701,"main":"Mist","description":"mist","icon":"50n"}
    ],
    "base":"stations",
    "main":{"temp":274.15,"pressure":1007,"humidity":72,"temp_min":274.15,"temp_max":274.15},
    "visibility":2500,
    "wind":{"speed":1,"deg":310},
    "clouds":{"all":75},
    "dt":1543788000,
    "sys":{"type":1,"id":1701,"message":0.0028,"country":"PL","sunrise":1543731568,"sunset":1543761610},
    "id":3094802,
    "name":"Krakow",
    "cod":200
}
EOT;

        $responseBody->expects($this->any())
            ->method('getContents')
            ->willReturn($responseContent);

        $this->translator->expects($this->any())
            ->method('translateResult')
            ->with($responseContent)
            ->willReturn(new WeatherRecord());

        $data = $this->provider->fetchByCity('Krakow');

        $this->assertTrue(is_array($data), '$data expected to be array');
        $this->assertCount(1, $data);
        $this->assertInstanceOf(WeatherRecord::class, $data[0]);
    }

    /**
     * @expectedException \App\Service\Weather\Exception\ApiErrorException
     */
    public function testFetchByCity_ClientError_ThrowsApiErrorException()
    {
        $this->guzzleClient->expects($this->any())
            ->method('request')
            ->with('GET', 'api.openweathermap.org/data/2.5/weather?appId=1234&q=Krakow')
            ->willThrowException(new TransferException());

        $this->provider->fetchByCity('Krakow');
    }

    /**
     * @expectedException \App\Service\Weather\Exception\MalformedApiResponseException
     */
    public function testFetchByCity_MalformedApiResponseExceptionCatched_DispatchesEventAndThrowsException()
    {
        /**
         * @var ResponseInterface|MockObject
         */
        $guzzleResponse = $this->getMockBuilder(ResponseInterface::class)
            ->getMockForAbstractClass();

        /**
         * @var StreamInterface|MockObject
         */
        $responseBody = $this->getMockBuilder(StreamInterface::class)
            ->getMockForAbstractClass();

        $this->guzzleClient->expects($this->any())
            ->method('request')
            ->with('GET', 'api.openweathermap.org/data/2.5/weather?appId=1234&q=Krakow')
            ->willReturn($guzzleResponse);

        $guzzleResponse->expects($this->any())
            ->method('getBody')
            ->willReturn($responseBody);

        $responseContent = <<<EOT
absdbdasda>>>!
EOT;

        $responseBody->expects($this->any())
            ->method('getContents')
            ->willReturn($responseContent);

        $this->translator->expects($this->any())
            ->method('translateResult')
            ->with($responseContent)
            ->willThrowException(new MalformedApiResponseException());

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with(WeatherApiEvents::MALFORMED_RESPONSE_EVENT, $this->logicalAnd(
                $this->isInstanceOf(MalformedApiResponseEvent::class),
                $this->callback(function(MalformedApiResponseEvent $event) {
                    return $event->getResponseContent() === 'absdbdasda>>>!';
                })
            ));

        $this->provider->fetchByCity('Krakow');
    }
}