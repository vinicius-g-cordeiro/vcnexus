<?php

/**
 * @brief Imports GeoNames geographic reference data.
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/10/06
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace Tools\GeonameExtractor\Generators;

use ADOConnection;

const GEONAMES_BASE_URL = 'https://download.geonames.org/export/dump/';
const GEONAMES_DIRECTORY = __DIR__ . '/../../../database/migrations/geonames';

const COUNTRY_FILE = GEONAMES_DIRECTORY . '/countryInfo.txt';
const ADMIN1_FILE = GEONAMES_DIRECTORY . '/admin1CodesASCII.txt';
const CITIES_ZIP = GEONAMES_DIRECTORY . '/cities500.zip';
const CITIES_FILE = GEONAMES_DIRECTORY . '/cities500.txt';

const CITY_BATCH_SIZE = 1000;
const STATE_BATCH_SIZE = 500;

final class ImportGeoNames
{
    private ADOConnection $db;

    /**
     * @var array<string, int>
     */
    private array $countries = [];

    /**
     * @var array<string, int>
     */
    private array $states = [];

    public function __construct(ADOConnection $db)
    {
        $this->db = $db;
    }

    public function run(): void
    {
        $this->prepareDirectory();

        $this->downloadSources();

        $this->importCountries();
        $this->importStates();
        $this->importCities();

        $this->output('GeoNames import completed.');
    }

    private function prepareDirectory(): void
    {
        if (!is_dir(GEONAMES_DIRECTORY)) {
            if (!mkdir(GEONAMES_DIRECTORY, 0775, true) && !is_dir(GEONAMES_DIRECTORY)) {
                throw new \RuntimeException(
                    'Unable to create GeoNames directory: ' . GEONAMES_DIRECTORY
                );
            }
        }
    }

    private function downloadSources(): void
    {
        $this->download(
            'countryInfo.txt',
            COUNTRY_FILE
        );

        $this->download(
            'admin1CodesASCII.txt',
            ADMIN1_FILE
        );

        $this->download(
            'cities500.zip',
            CITIES_ZIP
        );

        $this->extractCities();
    }

    private function download(string $filename, string $destination): void
    {
        if (is_file($destination) && filesize($destination) > 0) {
            $this->output("Using existing {$filename}.");

            return;
        }

        $url = GEONAMES_BASE_URL . $filename;

        $this->output("Downloading {$url}");

        $handle = fopen($destination, 'wb');

        if ($handle === false) {
            throw new \RuntimeException(
                "Unable to open destination: {$destination}"
            );
        }

        $curl = curl_init($url);

        if ($curl === false) {
            fclose($handle);

            throw new \RuntimeException(
                "Unable to initialize cURL for {$url}"
            );
        }

        curl_setopt_array($curl, [
            CURLOPT_FILE => $handle,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_USERAGENT => 'VCNexus GeoNames Importer/1.0',
        ]);

        $result = curl_exec($curl);

        if ($result === false) {
            $error = curl_error($curl);

            curl_close($curl);
            fclose($handle);

            @unlink($destination);

            throw new \RuntimeException(
                "Failed downloading {$url}: {$error}"
            );
        }

        curl_close($curl);
        fclose($handle);

        $this->output("Downloaded {$filename}.");
    }

    private function extractCities(): void
    {
        if (is_file(CITIES_FILE) && filesize(CITIES_FILE) > 0) {
            $this->output('Using existing cities500.txt.');

            return;
        }

        $this->output('Extracting cities500.zip.');

        $zip = new \ZipArchive();

        if ($zip->open(CITIES_ZIP) !== true) {
            throw new \RuntimeException(
                'Unable to open ' . CITIES_ZIP
            );
        }

        if ($zip->extractTo(GEONAMES_DIRECTORY) !== true) {
            $zip->close();

            throw new \RuntimeException(
                'Unable to extract ' . CITIES_ZIP
            );
        }

        $zip->close();

        if (!is_file(CITIES_FILE)) {
            throw new \RuntimeException(
                'cities500.txt was not found after extraction.'
            );
        }
    }

    private function importCountries(): void
    {
        $this->output('Importing countries...');

        $handle = $this->openFile(COUNTRY_FILE);

        $batch = [];
        $count = 0;

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $columns = explode("\t", $line);

            if (count($columns) < 17) {
                continue;
            }

            $isoAlpha2 = strtoupper(trim($columns[0]));
            $isoAlpha3 = strtoupper(trim($columns[1]));
            $name = trim($columns[4]);
            $geonamesId = (int) $columns[16];

            if (
                $isoAlpha2 === '' ||
                $isoAlpha3 === '' ||
                $name === '' ||
                $geonamesId <= 0
            ) {
                continue;
            }

            $batch[] = [
                $isoAlpha2,
                $isoAlpha3,
                $name,
                $geonamesId,
            ];

            if (count($batch) >= STATE_BATCH_SIZE) {
                $this->insertCountries($batch);

                $count += count($batch);
                $batch = [];

                $this->output("Countries processed: {$count}");
            }
        }

        fclose($handle);

        if ($batch !== []) {
            $this->insertCountries($batch);

            $count += count($batch);
        }

        $this->loadCountryMap();

        $this->output("Countries imported: {$count}");
    }

    /**
     * @param array<int, array{0:string,1:string,2:string,3:int}> $rows
     */
    private function insertCountries(array $rows): void
    {
        $values = [];
        $parameters = [];

        foreach ($rows as $row) {
            $values[] = '(?, ?, ?, ?)';

            foreach ($row as $value) {
                $parameters[] = $value;
            }
        }

        $sql = sprintf(
            <<<'SQL'
            INSERT INTO countries
                (iso_alpha2, iso_alpha3, name, geonames_id)
            VALUES
                %s
            ON CONFLICT (geonames_id)
            DO UPDATE SET
                iso_alpha2 = EXCLUDED.iso_alpha2,
                iso_alpha3 = EXCLUDED.iso_alpha3,
                name = EXCLUDED.name
            SQL,
            implode(', ', $values)
        );

        // print the sql statement to a new file with the parameters
        $printSql = sprintf(
            <<<'SQL'
            INSERT INTO countries
                (iso_alpha2, iso_alpha3, name, geonames_id)
            VALUES
                %s
            ON CONFLICT (geonames_id)
            DO UPDATE SET
                iso_alpha2 = EXCLUDED.iso_alpha2,
                iso_alpha3 = EXCLUDED.iso_alpha3,
                name = EXCLUDED.name
            SQL,
            implode(', ', $parameters)
        );

        file_put_contents('countries.sql', $printSql . "\n", FILE_APPEND);

        $this->db->StartTrans();
        try{
            $this->db->Execute($sql, $parameters);
            $this->db->CompleteTrans();
        }catch(\Exception $e){
            $this->db->FailTrans();
            $this->db->CompleteTrans();
            throw $e;
        }
    }

    private function loadCountryMap(): void
    {
        $this->countries = [];

        $sql = <<<'SQL'
            SELECT id, iso_alpha2
            FROM countries
            WHERE geonames_id IS NOT NULL
        SQL;

        $result = $this->db->Execute($sql);

        while (!$result->EOF) {
            $this->countries[
                strtoupper(trim((string) $result->fields['iso_alpha2']))
            ] = (int) $result->fields['id'];

            $result->MoveNext();
        }
    }

    private function importStates(): void
    {
        $this->output('Importing states / administrative divisions...');

        $handle = $this->openFile(ADMIN1_FILE);

        $batch = [];
        $count = 0;

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $columns = explode("\t", $line);

            if (count($columns) < 4) {
                continue;
            }

            $fullCode = trim($columns[0]);
            $name = trim($columns[1]);
            $geonamesId = (int) $columns[3];

            if ($fullCode === '' || $name === '' || $geonamesId <= 0) {
                continue;
            }

            $separator = strpos($fullCode, '.');

            if ($separator === false) {
                continue;
            }

            $countryCode = strtoupper(
                substr($fullCode, 0, $separator)
            );

            $stateCode = substr(
                $fullCode,
                $separator + 1
            );

            if (!isset($this->countries[$countryCode])) {
                continue;
            }

            if ($stateCode === '') {
                continue;
            }

            $batch[] = [
                $name,
                $stateCode,
                $this->countries[$countryCode],
                $geonamesId,
            ];

            if (count($batch) >= STATE_BATCH_SIZE) {
                $this->insertStates($batch);

                $count += count($batch);
                $batch = [];

                $this->output("States processed: {$count}");
            }
        }

        fclose($handle);

        if ($batch !== []) {
            $this->insertStates($batch);

            $count += count($batch);
        }

        $this->loadStateMap();

        $this->output("States imported: {$count}");
    }

    /**
     * @param array<int, array{0:string,1:string,2:int,3:int}> $rows
     */
    private function insertStates(array $rows): void
    {
        $values = [];
        $parameters = [];

        foreach ($rows as $row) {
            $values[] = '(?, ?, ?, ?)';

            foreach ($row as $value) {
                $parameters[] =  $value;
            }
        }

        $sql = sprintf(
            <<<'SQL'
            INSERT INTO states
                (name, code, country_id, geonames_id)
            VALUES
                %s
            ON CONFLICT (geonames_id)
            DO UPDATE SET
                name = EXCLUDED.name,
                code = EXCLUDED.code,
                country_id = EXCLUDED.country_id
            SQL,
            implode(', ', $values)
        );

        // print the sql statement to a new file with the parameters
        $printSql = sprintf(
            <<<'SQL'
            INSERT INTO states
                (name, code, country_id, geonames_id)
            VALUES
                %s
            ON CONFLICT (geonames_id)
            DO UPDATE SET
                name = EXCLUDED.name,
                code = EXCLUDED.code,
                country_id = EXCLUDED.country_id
            SQL,
            implode(', ', $parameters)    
        );

        file_put_contents('states.sql', $printSql . "\n", FILE_APPEND);
        $this->db->StartTrans();
        try{
            $this->db->Execute($sql, $parameters);
            $this->db->CompleteTrans();
        }catch(\Exception $e){
            $this->db->FailTrans();
            $this->db->CompleteTrans();
            throw $e;
        }
    }

    private function loadStateMap(): void
    {
        $this->states = [];

        $sql = <<<'SQL'
            SELECT
                s.id,
                c.iso_alpha2,
                s.code
            FROM states s
            INNER JOIN countries c
                ON c.id = s.country_id
            WHERE s.geonames_id IS NOT NULL
        SQL;

        $result = $this->db->Execute($sql);

        while (!$result->EOF) {
            $country = strtoupper(
                trim((string) $result->fields['iso_alpha2'])
            );

            $code = trim((string) $result->fields['code']);

            $this->states[
                $country . '.' . $code
            ] = (int) $result->fields['id'];

            $result->MoveNext();
        }
    }

    private function importCities(): void
    {
        $this->output('Importing cities...');

        $handle = $this->openFile(CITIES_FILE);

        $batch = [];
        $processed = 0;
        $imported = 0;
        $skipped = 0;

        while (($line = fgets($handle)) !== false) {
            $processed++;

            $columns = explode("\t", rtrim($line, "\r\n"));

            if (count($columns) < 11) {
                $skipped++;

                continue;
            }

            $geonamesId = (int) $columns[0];
            $name = trim($columns[1]);
            $countryCode = strtoupper(trim($columns[8]));
            $admin1Code = trim($columns[10]);

            if (
                $geonamesId <= 0 ||
                $name === '' ||
                $countryCode === ''
            ) {
                $skipped++;

                continue;
            }

            if (!isset($this->countries[$countryCode])) {
                $skipped++;

                continue;
            }

            $stateKey = $countryCode . '.' . $admin1Code;

            if (!isset($this->states[$stateKey])) {
                $skipped++;

                continue;
            }

            $batch[] = [
                $name,
                $this->states[$stateKey],
                $this->countries[$countryCode],
                $geonamesId,
            ];

            if (count($batch) >= CITY_BATCH_SIZE) {
                $this->insertCities($batch);

                $imported += count($batch);
                $batch = [];

                if ($processed % 10000 === 0) {
                    $this->output(
                        "Cities processed: {$processed}; imported: {$imported}; skipped: {$skipped}"
                    );
                }
            }
        }

        fclose($handle);

        if ($batch !== []) {
            $this->insertCities($batch);

            $imported += count($batch);
        }

        $this->output(
            "Cities processed: {$processed}; imported: {$imported}; skipped: {$skipped}"
        );
    }

    /**
     * @param array<int, array{0:string,1:int,2:int,3:int}> $rows
     */
    private function insertCities(array $rows): void
    {
        $values = [];
        $parameters = [];

        foreach ($rows as $row) {
            $values[] = '(?, ?, ?, ?)';

            foreach ($row as $value) {
                $parameters[] = $value;
            }
        }

        $sql = sprintf(
            <<<'SQL'
            INSERT INTO cities
                (name, state_id, country_id, geonames_id)
            VALUES
                %s
            ON CONFLICT (geonames_id)
            DO UPDATE SET
                name = EXCLUDED.name,
                state_id = EXCLUDED.state_id,
                country_id = EXCLUDED.country_id
            SQL,
            implode(', ', $values)
        );

        // print the sql query to a new file
        $printSql = sprintf(
            <<<'SQL'
            INSERT INTO cities
                (name, state_id, country_id, geonames_id)
            VALUES
                %s
            ON CONFLICT (geonames_id)
            DO UPDATE SET
                name = EXCLUDED.name,
                state_id = EXCLUDED.state_id,
                country_id = EXCLUDED.country_id
            SQL,
            implode(', ', $parameters)    
        );

        file_put_contents('cities.sql', $printSql . "\n", FILE_APPEND);

        $this->db->StartTrans();
        try{
            $this->db->Execute($sql, $parameters);
            $this->db->CompleteTrans();
        }catch(\Exception $e){
            $this->db->FailTrans();
            $this->db->CompleteTrans();
            throw $e;
        }
    }

    /**
     * @return resource
     */
    private function openFile(string $filename)
    {
        $handle = fopen($filename, 'rb');

        if ($handle === false) {
            throw new \RuntimeException(
                "Unable to open file: {$filename}"
            );
        }

        return $handle;
    }

    private function output(string $message): void
    {
        fwrite(
            STDOUT,
            '[' . date('Y-m-d H:i:s') . "] {$message}" . PHP_EOL
        );
    }
}