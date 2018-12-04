<?php

namespace App\Service\Weather\OpenWeatherMap;

use App\Entity\WeatherCondition;
use App\Entity\WeatherRecord;
use App\Event\MalformedApiResponseEvent;
use App\Event\WeatherApiEvents;
use App\Event\WeatherDataReceivedEvent;
use App\Service\Weather\Exception\ApiErrorException;
use App\Service\Weather\Exception\MalformedApiResponseException;
use GuzzleHttp\Client;
use App\Service\Weather\WeatherProviderInterface;
use App\Service\Weather\WeatherTranslatorInterface;
use GuzzleHttp\Exception\TransferException;
use function GuzzleHttp\Psr7\build_query;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class OpenWeatherMapProvider
 * @package App\Service\Weather\OpenWeatherMap
 */
final class OpenWeatherMapProvider implements WeatherProviderInterface
{
    /**
     * @var WeatherTranslatorInterface
     */
    private $weatherTranslator;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * OpenWeatherMapProvider constructor.
     * @param string $apiUrl
     * @param string $apiKey
     * @param Client $client
     * @param WeatherTranslatorInterface $weatherTranslator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        string $apiUrl,
        string $apiKey,
        Client $client,
        WeatherTranslatorInterface $weatherTranslator,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
        $this->client = $client;
        $this->weatherTranslator = $weatherTranslator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public function fetchByCity(string $city): array
    {
        $url = $this->apiUrl . '?' . build_query([
            'appId' => $this->apiKey,
            'q' => $city
        ]);

        $content = null;
        try {
            $response = $this->client->request(
                'GET',
                $url
            );

            $content = $response->getBody()->getContents();
            $weatherRecord = $this->weatherTranslator
                ->translateResult($content);

            $this->eventDispatcher->dispatch(WeatherApiEvents::WEATHER_DATA_RECEIVED, new WeatherDataReceivedEvent($weatherRecord));

            return [ $weatherRecord ];

        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new ApiErrorException(
                "Error performing request to OpenWeatherMap API",
                $e->getCode(),
                $e
            );
        } catch (MalformedApiResponseException $e) {
            $this->eventDispatcher->dispatch(WeatherApiEvents::MALFORMED_RESPONSE_EVENT, new MalformedApiResponseEvent($content));
            throw $e;
        }
    }
}