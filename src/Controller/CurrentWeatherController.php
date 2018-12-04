<?php

namespace App\Controller;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use App\Service\Weather\WeatherProviderInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;

class CurrentWeatherController extends FOSRestController
{
    /**
     * @SWG\Response(
     *     response=200,
     *     description="Returns current weather for a given city, and stores it in app database",
     *     @Model(type=App\Entity\WeatherRecord::class, groups={"public"})
     * )
     *
     * @param WeatherProviderInterface $weatherProvider
     * @param $city
     * @return View
     *
     * @Rest\View(serializerGroups={"public"})
     * @Rest\Get("/current-weather/{city}")
     */
    public function getAction(WeatherProviderInterface $weatherProvider, string $city): View
    {
        $data = $weatherProvider->fetchByCity($city);
        return View::create($data[0] ?? [], Response::HTTP_OK);
    }
}