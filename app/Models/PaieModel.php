<?php

namespace App\Models;

use CodeIgniter\Model;

class PaieModel extends Model
{
	protected $table = 'tpasa_paie';  // Define the table name
	protected $primaryKey = 'id';  // Define the primary key
	protected $allowedFields = [
		'salaries_id',
		'nb_jour_presence',
		'created_date',
		'created_by',
		'update_date',
		'Paie_du',
		'Paie_au',
		'salaire_brut',
		'cotisation_cnss',
		'salaire_imposable',
		'nb_cng_rst'
	];  // Allowed fields for mass assignment

	// Get all records from the 'tpasa_paie' table
	public function getAll(): array
	{
		return $this->findAll();
	}

	// Get the last inserted record ID
	public function getLastId(): int|null
	{
		return $this->selectMax('id')->first()['id'] ?? null;
	}

	// Get a record by its ID
	public function getById(int $id): object|null
	{
		return $this->find($id);
	}

	// Get paie record by a specific 'Paie_du' date
	public function getByDatePaie(string $date_paie): array
	{
		return $this->where('Paie_du', $date_paie)->findAll();
	}

	// Update a paie record based on 'salaries_id' and 'Paie_du' date
	public function updatePaie(int $id, string $date_paie, array $data): bool
	{
		return $this->where('salaries_id', $id)
			->where('Paie_du', $date_paie)
			->set($data)
			->update();
	}

	// Delete a paie record based on 'Paie_du' date
	public function deletePaieByDate(string $date_paie): bool
	{
		return $this->where('Paie_du', date('Y-m-01', strtotime($date_paie)))
			->delete();
	}

	// Create new paie entries for all employees not already paid in the given month
	public function createPaie(string $date_paie, string $date_creation, string $date_modif, int $user_id, float $cnss): bool
	{
		$sql = "INSERT INTO tpasa_paie (salaries_id, nb_jour_presence, created_date, created_by, update_date, Paie_du, Paie_au, salaire_brut, cotisation_cnss, salaire_imposable, nb_cng_rst)
                SELECT salaries.id, 26, '$date_creation', '$user_id', '$date_modif', DATE_FORMAT('$date_paie', '%Y-%m-01'), LAST_DAY('$date_paie'), salaire_brut, (salaire_brut * $cnss)/100, salaire_brut - (salaire_brut * $cnss)/100, solde_conge_initiale
                FROM salaries
                LEFT JOIN (SELECT salaries_id FROM tpasa_paie WHERE Paie_du = DATE_FORMAT('$date_paie', '%Y-%m-01') AND Paie_au = LAST_DAY('$date_paie') GROUP BY salaries_id) AS temp_salaries
                ON temp_salaries.salaries_id = salaries.id
                WHERE temp_salaries.salaries_id IS NULL
                AND salaries.date_debut_embauche <= '$date_paie'
                AND (salaries.date_fin_embauche IS NULL OR salaries.date_fin_embauche > '$date_paie')";

		return $this->db->query($sql);
	}

	// Get paie summary for a specific year
	public function getPaieSummaryByYear(int $annee): array
	{
		return $this->select("MONTH(Paie_au) as mois_paie, count(salaries_id) as count_salarie, sum(salaire_brut) as sum_brut, sum(salaire_imposable) as sum_net, sum(cotisation_cnss) as sum_cnss, sum(impot_revenue) as sum_irpp")
			->where("YEAR(Paie_au)", $annee)
			->groupBy("MONTH(Paie_au)")
			->orderBy("mois_paie", "asc")
			->findAll();
	}

	// Get CNSS rate from 'param_rh_paie' table
	public function getTauxCnss(): float|null
	{
		return $this->db->table('param_rh_paie')
			->select('taux_cnss')
			->get()
			->getRow('taux_cnss');
	}

	// Get paie parameters from 'param_rh_paie'
	public function getParamPaie(): array
	{
		return $this->db->table('param_rh_paie')->get()->getResult();
	}
}
