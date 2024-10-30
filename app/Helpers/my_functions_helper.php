<?php

namespace App\Helpers;

if (!function_exists('extraction_chaine')) {

	/**
	 * Extracts a string longer than 30 characters and appends '...'.
	 *
	 * @param string $chaine_param The input string.
	 * @return string The processed string.
	 */
	function extraction_chaine(string $chaine_param): string
	{
		$max = 30;
		return strlen($chaine_param) >= $max ? substr($chaine_param, 0, $max) . '...' : $chaine_param;
	}
}

if (!function_exists('hash_password')) {

	/**
	 * Hashes a password with a salt.
	 *
	 * @param string $password The plain text password.
	 * @return string The salted and hashed password.
	 */
	function hash_password(string $password): string
	{
		$salt = bin2hex(random_bytes(32));
		$hash = hash('sha256', $salt . $password);
		return $salt . $hash;
	}
}

if (!function_exists('verif_null')) {

	/**
	 * Checks if a value is null.
	 *
	 * @param mixed $valeur The value to check.
	 * @return mixed The value or '-' if null.
	 */
	function verif_null($valeur)
	{
		return isset($valeur) ? $valeur : "-";
	}
}

if (!function_exists('format_temps_jours')) {

	/**
	 * Formats time in days from the given object or array.
	 *
	 * @param mixed $tab The object or array containing time information.
	 * @param mixed $value Optional parameter to specify a specific value.
	 * @return string Formatted time string.
	 */
	function format_temps_jours($tab, $value = null): string
	{
		$str = "";
		if (is_null($value)) {
			$str .= (isset($tab->nb_days) ? $tab->nb_days : 0) . ' jour(s) ';
			$str .= (isset($tab->nb_days_mod) ? $tab->nb_days_mod : 0) . ' heure(s)';
		} else {
			$str .= (isset($tab[$value->id]->nb_days) ? $tab[$value->id]->nb_days : 0) . ' jour(s) ';
			$str .= (isset($tab[$value->id]->nb_days_mod) ? $tab[$value->id]->nb_days_mod : 0) . ' heure(s)';
		}
		return $str;
	}
}

if (!function_exists('format_temps_heures')) {

	/**
	 * Formats time in hours from the given object or array.
	 *
	 * @param mixed $tab The object or array containing time information.
	 * @param mixed $value Optional parameter to specify a specific value.
	 * @return string Formatted time string.
	 */
	function format_temps_heures($tab, $value = null): string
	{
		$str = "";
		if (is_null($value)) {
			$str .= (isset($tab->total) ? $tab->total : 0) . ' heure(s) ';
		} else {
			$str .= (isset($tab[$value->id]->total) ? $tab[$value->id]->total : 0) . ' heure(s) ';
		}
		return $str;
	}
}

if (!function_exists('dateFR')) {

	/**
	 * Converts a date from the database format to French format (DD/MM/YYYY).
	 *
	 * @param string $date The date in YYYY-MM-DD format.
	 * @return string The formatted date.
	 */
	function dateFR(string $date): string
	{
		$datefr = explode("-", $date);
		return isset($datefr[2], $datefr[1], $datefr[0]) ? $datefr[2] . '/' . $datefr[1] . '/' . $datefr[0] : $date;
	}
}
