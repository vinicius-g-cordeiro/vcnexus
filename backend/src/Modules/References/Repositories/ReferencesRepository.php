<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\References\Repositories;

use App\Infrastructure\Database\Hydrator;
use App\Modules\References\DTOs\{CountryRequestContext,CountryResponseContext, StateRequestContext, StateResponseContext, CityRequestContext, CityResponseContext, MaritalStatusResponseContext};
use App\Modules\References\Models\{Disability, EducationalLevel, Ethnicity, Gender, Nationality, Religion, SexualOrientation};
use App\Shared\Domain\BaseRepository;
use App\Shared\Http\Request;

final class ReferencesRepository extends BaseRepository
{
    public function __construct( \ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private Request $request) {
        parent::__construct($db, $tenant_id, $user_id, $roles);
    }

    protected function table(): string
    {
        return 'roles';
    }

    /**
     * Function to get the roles of the system to be shown on the sidebar
     * @param CountryRequestContext $parameters - Request parameters
     * @return array<CountryResponseContext>
     */
    public function getCountries(CountryRequestContext $parameters) : ?array {
        $query = <<<SQL
            SELECT c.name, c.id, c.uuid, c.iso_alpha2, c.iso_alpha3, c.geonames_id
            FROM countries c
            WHERE c.active = 1
        SQL;

        $params = [];

        // Verify if we are not using the super admin user, if not we will filter by tenant
        if(in_array("1", $this->roles) === false) {
            $query .= ' AND c.tenant_id = ?';
            $params[] = $this->tenant_id;
        }

        if ($parameters->name) {
            $query .= ' AND public.unaccent(lower(c.name)) LIKE public.unaccent(lower(?))';
            $params[] = "%{$parameters->name}%";
        }

        $query .= ' ORDER BY c.name ASC';

        $result = $this->scopedQuery($query, $params);

        if (empty($result) || $result === false) {
            return [];
        }

        $result = Hydrator::hydrateMany(CountryResponseContext::class, $result);
        return $result;
    }

    public function getStates(StateRequestContext $parameters) : ?array {
        $query = <<<SQL
            SELECT s.name, s.id, s.uuid, s.iso_alpha2, s.iso_alpha3, s.geonames_id, s.country_id
            FROM states s
            WHERE s.active = 1 AND s.country_id = ?
        SQL;

        $params = [$parameters->country_id];

        if ($parameters->name) {
            $query .= ' AND public.unaccent(lower(s.name)) LIKE public.unaccent(lower(?))';
            $params[] = "%{$parameters->name}%";
        }

        $query .= ' ORDER BY s.name ASC';

        $result = $this->scopedQuery($query, $params);

        if (empty($result) || $result === false) {
            return [];
        }

        $result = Hydrator::hydrateMany(StateResponseContext::class, $result);
        return $result;
    }

    public function getCities(CityRequestContext $parameters) : ?array {
        $query = <<<SQL
            SELECT c.name, c.id, c.uuid, c.iso_alpha2, c.iso_alpha3, c.geonames_id, c.state_id
            FROM cities c
            WHERE c.active = 1 AND c.state_id = ?
        SQL;

        $params = [$parameters->state_id];

        if ($parameters->name) {
            $query .= ' AND public.unaccent(lower(c.name)) LIKE public.unaccent(lower(?))';
            $params[] = "%{$parameters->name}%";
        }

        $query .= ' ORDER BY c.name ASC';

        $result = $this->scopedQuery($query, $params);

        if (empty($result) || $result === false) {
            return [];
        }

        $result = Hydrator::hydrateMany(CityResponseContext::class, $result);
        return $result;
    }

    public function getMaritalStatuses() : ?array {
        $query = <<<SQL
            SELECT id, uuid, label, description, name
            FROM marital_statuses
            WHERE active = 1
        SQL;

        $result = $this->scopedQuery($query);

        if (empty($result) || $result === false) {
            return [];
        }

        $result = Hydrator::hydrateMany(MaritalStatusResponseContext::class, $result);
        return $result;
    }

    public function getGenders() : ?array {
        $query = <<<SQL
            SELECT id, uuid, label, description, name
            FROM genders
            WHERE active = 1
        SQL;

        $result = $this->scopedQuery($query);

        if (empty($result) || $result === false) {
            return [];
        }

        $result = Hydrator::hydrateMany(Gender::class, $result);
        return $result;
    }

    public function getReligions() : ?array {
        $query = <<<SQL
            SELECT id, uuid, label, description, name
            FROM religions
            WHERE active = 1
        SQL;

        $result = $this->scopedQuery($query);

        if (empty($result) || $result === false) {
            return [];
        }

        $result = Hydrator::hydrateMany(Religion::class, $result);
        return $result;
    }

    public function getEducationalLevels() : ?array {
        $query = <<<SQL
            SELECT id, uuid, label, description, name
            FROM educational_levels
            WHERE active = 1
        SQL;

        $result = $this->scopedQuery($query);

        if (empty($result) || $result === false) {
            return [];
        }

        $result = Hydrator::hydrateMany(EducationalLevel::class, $result);
        return $result;
    }

    public function getEthnicities() : ?array {
        $query = <<<SQL
            SELECT id, uuid, label, description, name
            FROM ethnicity
            WHERE active = 1
        SQL;

        $result = $this->scopedQuery($query);

        if (empty($result) || $result === false) {
            return [];
        }

        $result = Hydrator::hydrateMany(Ethnicity::class, $result);
        return $result;
    }

    public function getSexualOrientations() : ?array {
        $query = <<<SQL
            SELECT id, uuid, label, description, name
            FROM sexual_orientations
            WHERE active = 1
        SQL;

        $result = $this->scopedQuery($query);

        if (empty($result) || $result === false) {
            return [];
        }

        $result = Hydrator::hydrateMany(SexualOrientation::class, $result);
        return $result;
    }

    public function getDisabilities() : ?array {
        $query = <<<SQL
            SELECT id, uuid, label, description, name
            FROM disabilities
            WHERE active = 1
        SQL;

        $result = $this->scopedQuery($query);

        if (empty($result) || $result === false) {
            return [];
        }

        $result = Hydrator::hydrateMany(Disability::class, $result);
        return $result;
    }

    public function getNationalities() : ?array {
        $query = <<<SQL
            SELECT id, uuid, label, description, name
            FROM nationality
            WHERE active = 1
        SQL;

        $result = $this->scopedQuery($query);

        if (empty($result) || $result === false) {
            return [];
        }

        $result = Hydrator::hydrateMany(Nationality::class, $result);
        return $result;
    }


}