<?php

namespace App\Shared\Helpers;

class Utils
{

    static function pgArrayToPhp(string $pgArray): array
    {
        $pgArray = trim($pgArray);
        if ($pgArray === '{}' || $pgArray === '') {
            return [];
        }

        // Strip outer braces
        $inner = substr($pgArray, 1, -1);

        $result = [];
        $current = '';
        $inQuotes = false;
        $len = strlen($inner);

        for ($i = 0; $i < $len; $i++) {
            $char = $inner[$i];

            if ($char === '"' && ($i === 0 || $inner[$i - 1] !== '\\')) {
                $inQuotes = !$inQuotes;
                continue;
            }

            if ($char === '\\' && $inQuotes && isset($inner[$i + 1]) && $inner[$i + 1] === '"') {
                $current .= '"';
                $i++; // skip the escaped quote
                continue;
            }

            if ($char === ',' && !$inQuotes) {
                $result[] = trim($current, '{');
                $current = '';
                continue;
            }

            $current .= $char;
        }

        $result[] = trim(trim($current, '{'), '}');

        return $result;
    }


    /**
     * Removes accents from a string.
     *
     * @param string $str The string to remove the accents from.
     * @return string The string with all accents removed.
     */
    static function removeAccents($str) {
		$accentsToRemove = ['á', 'à', 'ã', 'â', 'ä', 'é', 'è', 'ê', 'ë', 'í', 'ì', 'î', 'ï', 'ó', 'ò', 'õ', 'ô', 'ö', 'ú', 'ù', 'û', 'ü', 'ç'];
		$accentsToReplace = ['a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'c'];

		return str_replace($accentsToRemove, $accentsToReplace, $str);
    }

    /**
     * Converts a given date string to a DateTime object.
     *
     * @param string $date The date string to convert.
     * @param string $format The format of the date string. Available formats are:
     *  - Y-m-d
     *  - Y-m-d H:i:s
     *  - d/m/Y
     *  - d/m/Y H:i:s
     *  - success: boolean - Whether the conversion was successful.
     *  - message: string - A message indicating the status of the conversion.
     *  - data: ?DateTime - The converted DateTime object, or null if the conversion failed.
     */
    static function formatDate($date, $format = 'Y-m-d') {
        if (empty($date)) {
            return [
                'success' => false,
                'message' => 'Invalid date',
                'data' => null
            ];
        }
        // Check if the given date is a valid date format
        $validFormats = ['Y-m-d', 'Y-m-d H:i:s', 'd/m/Y', 'd/m/Y H:i:s'];
        if (!in_array($format, $validFormats)) {
            return [
                'success' => false,
                'message' => 'Invalid date format',
                'data' => null
            ];
        }

        $date = \DateTime::createFromFormat($format, $date);
        return $date;
    }

	/**
	 * 
	 */
	static function dateFormat($date, $format = 'd/m/Y') {
		if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date, $matches)) {
			$date = \DateTime::createFromFormat('Y-m-d', $matches[0]);
			return $date->format($format);
		}

		if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $date, $matches)) {
			$date = \DateTime::createFromFormat('Y-m-d H:i:s', $matches[0]);
			return $date->format($format);
		}

		if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$/', $date, $matches)) {
			$date = \DateTime::createFromFormat('Y-m-d H:i', $matches[0]);
			return $date->format($format);
		}

		if(preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $date, $matches)) {
			$date = \DateTime::createFromFormat('d/m/Y', $matches[0]);
			return $date->format($format);
		}

		if(preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $date, $matches)) {
			$date = \DateTime::createFromFormat('d/m/Y H:i:s', $matches[0]);
			return $date->format($format);
		}

		if(preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4} [0-9]{2}:[0-9]{2}$/', $date, $matches)) {
			$date = \DateTime::createFromFormat('d/m/Y H:i', $matches[0]);
			return $date->format($format);
		}

		return $date;
	}

    /**
     * Formats a phone number according to the specified format.
     *
     * This function removes any non-numeric characters from the phone number
     * and applies a mask to format it. The default format is '+## (##) #####-####'.
     *
     * @param string $phone The phone number to format.
     * @param string $format The format to apply to the phone number.
     * @return string|array The formatted phone number, or an array with an error message if invalid.
     */
    static function formatPhone($phone, $format = '+## (##) #####-####') {
        if (empty($phone)) {
            return [
                'success' => false,
                'message' => 'Invalid phone',
                'data' => null
            ];
        }

        // Mask the phone number using the given format +## (##) #####-####
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $phone = preg_replace('/(\d{2})(\d{2})(\d{5})(\d{4})/', '+$1 ($2) $3-$4', $phone);

        // Return the formatted phone number
        return $phone;
    }

    /**
     * Formats a CPF number according to the specified format.
     *
     * This function removes any non-numeric characters from the CPF number
     * and applies a mask to format it. The default format is '###.###.###-##'.
     *
     * @param string $cpf The CPF number to format.
     * @param string $format The format to apply to the CPF number.
     * @return string|array The formatted CPF number, or an array with an error message if invalid.
     */
    static function formatCpf($cpf, $format = '###.###.###-##') {
        if (empty($cpf)) {
            return [
                'success' => false,
                'message' => 'Invalid CPF',
                'data' => null
            ];
        }

        // Mask the CPF number using the given format ###.###.###-##
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);

        // Return the formatted CPF number
        return $cpf;
    }

    /**
     * Formats a CNPJ number according to the specified format.
     *
     * This function removes any non-numeric characters from the CNPJ number
     * and applies a mask to format it. The default format is '##.###.###/####-##'.
     *
     * @param string $cnpj The CNPJ number to format.
     * @param string $format The format to apply to the CNPJ number.
     * @return string|array The formatted CNPJ number, or an array with an error message if invalid.
     */
    static function formatCnpj($cnpj, $format = '##.###.###/####-##') {
        if (empty($cnpj)) {
            return [
                'success' => false,
                'message' => 'Invalid CNPJ',
                'data' => null
            ];
        }

        // Mask the CNPJ number using the given format ##.###.###/####-##
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        $cnpj = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);

        // Return the formatted CNPJ number
        return $cnpj;
    }

    /**
     * Formats a CEP number according to the specified format.
     *
     * This function removes any non-numeric characters from the CEP number
     * and applies a mask to format it. The default format is '#####-###'.
     *
     * @param string $cep The CEP number to format.
     * @param string $format The format to apply to the CEP number.
     * @return string|array The formatted CEP number, or an array with an error message if invalid.
     */
    static function formatCep($cep, $format = '#####-###') {
        if (empty($cep)) {
            return [
                'success' => false,
                'message' => 'Invalid CEP',
                'data' => null
            ];
        }

        // Mask the CEP number using the given format #####-###
        $cep = preg_replace('/[^0-9]/', '', $cep);
        $cep = preg_replace('/(\d{5})(\d{3})/', '$1-$2', $cep);

        // Return the formatted CEP number
        return $cep;
    }

    /**
     * Remove all non-alphanumeric characters from a string.
     *
     * @param string $str The string to clear.
     * @return string The cleared string.
     */
    static function clearString($str) {
		$str = self::removeAccents($str);
        return preg_replace('/[^a-zA-Z0-9]/', '', $str);
    }

    /**
     * Remove all non-numeric characters from a string.
     *
     * @param string $str The string to clear.
     * @return string The cleared string.
     */
    static function clearNumber($str) {
        return preg_replace('/[^0-9]/', '', $str);
    }

    /**
     * Returns a string with all alphabetic characters converted to lowercase.
     *
     * @param string $str The string to convert.
     * @return string The string with all alphabetic characters converted to lowercase.
     */
    static function lower($str) {
        return mb_strtolower($str, 'UTF-8');
    }

    /**
     * Returns a string with all alphabetic characters converted to uppercase.
     *
     * @param string $str The string to convert.
     * @return string The string with all alphabetic characters converted to uppercase.
     */
    static function upper($str) {
        return mb_strtoupper($str, 'UTF-8');
    }

    /**
     * Returns a string with all alphabetic characters converted to title case.
     *
     * @param string $str The string to convert.
     * @return string The string with all alphabetic characters converted to title case.
     */
    static function capitalize($str) {
        return mb_convert_case($str, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Returns a portion of a string, starting from the beginning and ending at a specified length.
     *
     * @param string $str The string to truncate.
     * @param int    $length The length of the string to return.
     * @return string The substring of the given string, of the given length.
     */
    static function truncate($str, $length, $ellipsis = '...', $type = 'html') {
        $text = mb_substr($str, 0, $length, 'UTF-8');
		if(mb_strlen($str) > $length) {
			return $text . $ellipsis;
		}

		return $text;
    }

    /**
     * Converts a string into a URL-friendly "slug" by replacing non-alphanumeric characters with hyphens.
     *
     * @param string $str The input string to be slugified.
     * @return string The slugified version of the input string.
     */
    static function slugify($str) {
        return preg_replace('/[^a-zA-Z0-9]/', '-', $str);
    }



}
