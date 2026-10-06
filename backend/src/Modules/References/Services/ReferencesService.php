<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\References\Services;

use ADOConnection;
use Redis;

use App\Infrastructure\Redis\RedisConnectionFactory;
use App\Modules\References\DTOs\{
    CountryRequestContext,
    StateRequestContext,
    CityRequestContext
};
use App\Modules\References\Repositories\ReferencesRepository;
use App\Shared\Domain\BaseService;

final class ReferencesService extends BaseService
{
    private Redis $redisConnection;

    public function __construct(ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private ReferencesRepository $referenceRepository, private RedisConnectionFactory $redisConnectionFactory)
    {
        parent::__construct($db, $tenant_id, $user_id, $roles);

        $this->redisConnection = $this->redisConnectionFactory->create();
    }

    private function countriesCacheKey(): string
    {
        return 'countries';
    }

    private function statesCacheKey(string|int $countryId): string
    {
        return "countries:{$countryId}:states";
    }

    private function citiesCacheKey(string|int $countryId, string|int|null $stateId): string
    {
        return "countries:{$countryId}:{$stateId}:cities";
    }

    private function getCached(string $key): ?array
    {
        if (!$this->redisConnection->exists('references:'.$key)) {
            return null;
        }

        $value = $this->redisConnection->get('references:'.$key);

        if ($value === false || $value === null) {
            return null;
        }

        return json_decode($value, true);
    }

    private function setCached(string $key, array $value): void
    {
        $this->redisConnection->set(
            'references:'.$key,
            json_encode($value, JSON_THROW_ON_ERROR)
        );
    }

    public function getCountries(CountryRequestContext $parameters): ?array
    {
        $key = $this->countriesCacheKey();

        $cached = $this->getCached($key);

        if ($cached !== null) {
            return $cached;
        }

        $countries = $this->referenceRepository->getCountries($parameters);

        if ($countries !== null) {
            $this->setCached($key, $countries);

            return $countries;
        }

        return [];
    }

    public function getStates(StateRequestContext $parameters): ?array
    {
        $key = $this->statesCacheKey($parameters->country_id);

        $cached = $this->getCached($key);

        if ($cached !== null) {
            return $cached;
        }

        $states = $this->referenceRepository->getStates($parameters);

        if ($states !== null) {
            $this->setCached($key, $states);

            return $states;
        }

        return [];
    }

    public function getCities(CityRequestContext $parameters): ?array
    {
        $key = $this->citiesCacheKey($parameters->country_id, $parameters->state_id);

        $cached = $this->getCached($key);

        if ($cached !== null) {
            return $cached;
        }

        $cities = $this->referenceRepository->getCities($parameters);

        if ($cities !== null) {
            $this->setCached($key, $cities);

            return $cities;
        }

        return [];
    }

    public function getMaritalStatuses(): ?array
    {
        $key = 'marital_statuses';

        $cached = $this->getCached($key);

        if ($cached !== null) {
            return $cached;
        }

        $maritalStatuses = $this->referenceRepository->getMaritalStatuses();

        if ($maritalStatuses !== null) {
            $this->setCached($key, $maritalStatuses);

            return $maritalStatuses;
        }

        return [];
    }

    public function getGenders(): ?array
    {
        $key = 'genders';

        $cached = $this->getCached($key);

        if ($cached !== null) {
            return $cached;
        }

        $genders = $this->referenceRepository->getGenders();

        if ($genders !== null) {
            $this->setCached($key, $genders);

            return $genders;
        }
    }

    public function getEducationalLevels(): ?array
    {
        $key = 'educational_levels';

        $cached = $this->getCached($key);

        if ($cached !== null) {
            return $cached;
        }

        $educationalLevels = $this->referenceRepository->getEducationalLevels();

        if ($educationalLevels !== null) {
            $this->setCached($key, $educationalLevels);

            return $educationalLevels;
        }
    }

    public function getReligions(): ?array
    {
        $key = 'religions';

        $cached = $this->getCached($key);

        if ($cached !== null) {
            return $cached;
        }

        $religions = $this->referenceRepository->getReligions();

        if ($religions !== null) {
            $this->setCached($key, $religions);

            return $religions;
        }
    }

    public function getEthnicities(): ?array
    {
        $key = 'ethnicities';

        $cached = $this->getCached($key);

        if ($cached !== null) {
            return $cached;
        }

        $ethnicities = $this->referenceRepository->getEthnicities();

        if ($ethnicities !== null) {
            $this->setCached($key, $ethnicities);

            return $ethnicities;
        }
    }

    public function getNationalities(): ?array
    {
        $key = 'nationalities';

        $cached = $this->getCached($key);

        if ($cached !== null) {
            return $cached;
        }

        $nationalities = $this->referenceRepository->getNationalities();

        if ($nationalities !== null) {
            $this->setCached($key, $nationalities);

            return $nationalities;
        }
    }

    public function getSexualOrientations(): ?array
    {
        $key = 'sexual_orientations';

        $cached = $this->getCached($key);

        if ($cached !== null) {
            return $cached;
        }

        $sexualOrientations = $this->referenceRepository->getSexualOrientations();

        if ($sexualOrientations !== null) {
            $this->setCached($key, $sexualOrientations);

            return $sexualOrientations;
        }
    }

    public function getDisabilities(): ?array
    {
        $key = 'disabilities';

        $cached = $this->getCached($key);

        if ($cached !== null) {
            return $cached;
        }

        $disabilities = $this->referenceRepository->getDisabilities();

        if ($disabilities !== null) {
            $this->setCached($key, $disabilities);

            return $disabilities;
        }
    }
}
