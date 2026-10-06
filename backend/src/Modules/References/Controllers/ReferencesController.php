<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\References\Controllers;

use App\Modules\References\Services\ReferencesService;
use App\Shared\Http\Attributes\{Route, Middleware};
use App\Shared\Http\Controllers\BaseController;
use App\Shared\Http\{Response, Request, Session, DTOValidator, Middleware\TenantResolverMiddleware};
use App\Shared\Http\Middleware\{AuthMiddleware};
use App\Modules\References\DTOs\{CountryRequestContext, StateRequestContext, CityRequestContext};

#[Route(path: '/v1/references/')]
#[Middleware(AuthMiddleware::class)]
#[Middleware(TenantResolverMiddleware::class)]
final class ReferencesController extends BaseController
{
    public function __construct(private ReferencesService $service, Request $request, Session $session, private DTOValidator $validator) {
        parent::__construct($request, $session);
    }

    #[Route(path: 'countries/', methods: ['QUERY', 'GET'])]
    public function getCountries(): Response
    {
        try{
            $parameters = CountryRequestContext::fromArray((array)$this->request->params());
            $countries = $this->service->getCountries($parameters);
            return Response::json(data: object(list: $countries), message: 'success')->send(200, [], true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    #[Route(path: '/countries/{country_id}/states/', methods: ['QUERY', 'GET'])]
    public function getStates(?string $country_id): Response
    {
        try{
            $parameters = StateRequestContext::fromArray([...(array)$this->request->params(), 'country_id' => $country_id]);
            $states = $this->service->getStates($parameters);
            return Response::json(data: object(list: $states), message: 'success')->send(200, [], true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    #[Route(path: '/countries/{country_id}/states/{state_id}/cities/', methods: ['QUERY', 'GET'])]
    public function getCities(?string $country_id,?string $state_id): Response
    {
        try{
            $parameters = CityRequestContext::fromArray([...(array)$this->request->params(), 'state_id' => $state_id, 'country_id' => $country_id]);
            $cities = $this->service->getCities($parameters);
            return Response::json(data: object(list: $cities), message: 'success')->send(200, [], true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: ['QUERY', 'GET'], path: '/marital-statuses/')]
    public function getMaritalStatus(): Response
    {
        try{
            $maritalStatus = $this->service->getMaritalStatuses();
            return Response::json(data: object(list: $maritalStatus), message: 'success')->send(200, [], true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    
    #[Route(methods: ['QUERY', 'GET'], path: '/genders/')]
    public function getGenders(): Response
    {
        try{
            $genders = $this->service->getGenders();
            return Response::json(data: object(list: $genders), message: 'success')->send(200, [], true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: ['QUERY', 'GET'], path: '/educational-levels/')]
    public function getEducationalLevels(): Response
    {
        try{
            $educationalLevels = $this->service->getEducationalLevels();
            return Response::json(data: object(list: $educationalLevels), message: 'success')->send(200, [], true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: ['QUERY', 'GET'], path: '/religions/')]
    public function getReligions(): Response
    {
        try{
            $religions = $this->service->getReligions();
            return Response::json(data: object(list: $religions), message: 'success')->send(200, [], true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: ['QUERY', 'GET'], path: '/sexual-orientations/')]
    public function getSexualOrientations(): Response
    {
        try{
            $sexualOrientations = $this->service->getSexualOrientations();
            return Response::json(data: object(list: $sexualOrientations), message: 'success')->send(200, [], true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    #[Route(path: '/disabilities/', methods: ['QUERY', 'GET'])]
    public function getDisabilities(): Response
    {
        try{
            $disabilities = $this->service->getDisabilities();
            return Response::json(data: object(list: $disabilities), message: 'success')->send(200, [], true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    #[Route(path: '/ethnicities/', methods: ['QUERY', 'GET'])]
    public function getEthnicities(): Response
    {
        try{
            $ethnicities = $this->service->getEthnicities();
            return Response::json(data: object(list: $ethnicities), message: 'success')->send(200, [], true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    #[Route(path: '/nationalities/', methods: ['QUERY', 'GET'])]
    public function getNationalities(): Response
    {
        try{
            $nationalities = $this->service->getNationalities();
            return Response::json(data: object(list: $nationalities), message: 'success')->send(200, [], true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}