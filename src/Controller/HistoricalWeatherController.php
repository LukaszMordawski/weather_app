<?php

namespace App\Controller;

use App\Service\Weather\WeatherProviderInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class HistoricalWeatherController extends FOSRestController
{
    /**
     * @SWG\Response(
     *     response=200,
     *     description="Returns historical weather for a given city, that is stored in app database",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=App\Entity\WeatherRecord::class, groups={"public"}))
     *     )
     *
     * )
     *
     * @param WeatherProviderInterface $weatherProvider
     * @param $city
     * @return View
     *
     * @Rest\View(serializerGroups={"public"})
     * @Rest\Get("/historical-weather/{city}")
     */
    public function getAction(WeatherProviderInterface $weatherProvider, string $city): View
    {
        $data = $weatherProvider->fetchByCity($city);
        return View::create($data ?? [], Response::HTTP_OK);
    }
}