<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{

    protected $table = ' invoices';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'reference',
        'company_id',
        'subject',
        'notes',
        'status',
        'currency',
        'issue_date',
        'creation_date',
        'due_date',
        'sent_date',
        'paid_date',
        'terms',
        'discount',
        'subscription_id',
        'project_id',
        'project_ref',
        'tax',
        'estimate',
        'estimate_accepted_date',
        'estimate_sent',
        'sum',
        'sumht',
        'second_tax',
        'estimate_reference',
        'paid',
        'outstanding',
        'estimate_num',
        'id_facture',
        'timbre_fiscal',
        'project_name',
        'project_surface',
        'calcul_heure',
        'delivery',
        'chef_projet_client',
        'chef_projet',
        'unite'
    ]; // Update with your table's fields

    /**
     * Get elements for Excel export.
     *
     * @return array
     */
    public function getElementForExcel(): array
    {
        return $this->select('estimate_num, company_id, subject, issue_date, currency, sum')
            ->orderBy('id', 'desc')
            ->findAll();
    }

    /**
     * Get invoices with `sumht` greater than 0.
     *
     * @return array
     */
    public function getDevisDocument(): array
    {
        return $this->where('sumht >', 0)
            ->orderBy('id', 'desc')
            ->findAll();
    }
    /**
     * Get all invoices.
     *
     * @return array
     */
    public function getDocument(): array
    {
        return $this->findAll();
    }

    /**
     * Get invoices with `sumht` equal to 0.
     *
     * @return array
     */
    public function getAttDocument(): array
    {
        return $this->where('sumht', 0)
            ->orderBy('id', 'desc')
            ->findAll();
    }

    /**
     * Get invoices that match 'MMS' in `project_name` or `subject`.
     *
     * @return array
     */
    public function getMms(): array
    {
        return $this->like('project_name', 'MMS')
            ->orLike('subject', 'MMS', 'both')
            ->orderBy('id', 'desc')
            ->findAll();
    }

    /**
     * Get invoices that match 'BIM2D' in `project_name` or `subject`.
     *
     * @return array
     */
    public function getBim2d(): array
    {
        return $this->like('project_name', 'BIM2D')
            ->orLike('subject', 'BIM2D', 'both')
            ->orderBy('id', 'desc')
            ->findAll();
    }

    /**
     * Get invoices that match 'BIM3D' in `project_name` or `subject`.
     *
     * @return array
     */
    public function getBim3d(): array
    {
        return $this->like('project_name', 'BIM3D')
            ->orLike('subject', 'BIM3D', 'both')
            ->orderBy('id', 'desc')
            ->findAll();
    }

    /**
     * Get invoices by project ID.
     *
     * @param int $id
     * @return array
     */
    public function getByIdProject(int $id): array
    {
        return $this->where('project_id', $id)
            ->first();
    }

    /**
     * Get project ID by invoice ID.
     *
     * @param int $ids
     * @return array|null
     */
    public function getById(int $ids): ?array
    {
        return $this->db->table('invoices')
            ->select('project_id')
            ->where('id', $ids)
            ->get()
            ->getRowArray();
    }

    /**
     * Get project name by project ID.
     *
     * @param int $idp
     * @return array|null
     */
    public function getByIdp(int $idp): ?array
    {
        return $this->db->table('projects')
            ->select('name')
            ->where('id', $idp)
            ->get()
            ->getRowArray();
    }

    /**
     * Get project reference by project ID.
     *
     * @param int $idp
     * @return array|null
     */
    public function getByRef(int $idp): ?array
    {
        return $this->db->table('projects')
            ->select('ref_projet as refp')
            ->where('id', $idp)
            ->get()
            ->getRowArray();
    }

    /**
     * Get total income for the given year.
     *
     * @param int $year
     * @return float|null
     */
    public function totalIncomeForYear(int $year): ?float
    {
        $result = $this->db->query("
            SELECT SUM(`sum`) AS summary FROM (
                SELECT `paid_date`, `status`, SUM(`sum`) AS `sum`
                FROM `invoices`
                WHERE `status` = 'Paid'
                AND `paid_date` BETWEEN '{$year}-01-01' AND '{$year}-12-31'
                UNION ALL
                SELECT T3.`date` AS `paid_date`, T4.`status`, SUM(T3.`amount`) AS `sum`
                FROM `invoice_has_payments` AS T3
                LEFT JOIN `invoices` AS T4 ON T3.invoice_id = T4.id
                WHERE T4.`status` = 'PartiallyPaid'
                AND T3.`date` BETWEEN '{$year}-01-01' AND '{$year}-12-31'
            ) t1
        ")->getRow();

        return $result ? (float) $result->summary : null;
    }

    /**
     * Get sum of payments grouped by month for a given year.
     *
     * @param int $year
     * @return array
     */
    public function getStatisticForYear(int $year): array
    {
        $query = "
            SELECT SUBSTR(`paid_date`, 1, 7) AS month, SUM(`sum`) AS summary FROM (
                SELECT `paid_date`, `status`, SUM(`sum`) AS `sum`
                FROM `invoices`
                WHERE `status` = 'Paid' AND `paid_date` BETWEEN '{$year}-01-01' AND '{$year}-12-31'
                GROUP BY SUBSTR(`paid_date`, 1, 7)
                UNION ALL
                SELECT T3.`date` AS `paid_date`, T4.`status`, SUM(T3.`amount`) AS `sum`
                FROM `invoice_has_payments` AS T3
                LEFT JOIN `invoices` AS T4 ON T3.invoice_id = T4.id
                WHERE T4.`status` = 'PartiallyPaid' AND T3.`date` BETWEEN '{$year}-01-01' AND '{$year}-12-31'
                GROUP BY SUBSTR(T3.`date`, 1, 7)
            ) t1 GROUP BY month;
        ";

        return $this->db->query($query)->getResultArray();
    }


    /**
     * Get expenses grouped by month for a given year.
     *
     * @param int $year
     * @return array
     */
    public function getExpensesStatisticForYear(int $year): array
    {
        /*$expenses = $this->db->query("
            SELECT SUBSTR(`date`, 1, 7) AS month, SUM(`value`) AS summary
            FROM `expenses`
            WHERE `date` BETWEEN '{$year}-01-01' AND '{$year}-12-31'
            GROUP BY month
        ")->getResultArray();

        return $expenses;*/
        return $this->db->table('expenses')
            ->select("SUBSTR(`date`, 1, 7) AS month, SUM(`value`) AS summary")
            ->where('date >=', "{$year}-01-01")
            ->where('date <=', "{$year}-12-31")
            ->groupBy("month")
            ->get()
            ->getResultArray();
    }

    /**
     ** Get sum of payments grouped by Month for statistics
     ** return object
     **/
    public static function getExpensesStatisticFor($start, $end)
    {
        $expensesByMonth = ExpenseModel::find_by_sql("SELECT 
                `date`,
                SUM(`value`) AS summary
            FROM 
                `expenses` 
            WHERE 
                `date` BETWEEN '$start' AND '$end' 
            Group BY 
                SUBSTR(`date`, 1, 7);
            ");

        return $expensesByMonth;
    }

    /**
     * Get sum of payments for a given month.
     *
     * @param string $yearMonth
     * @return float|null
     */
    public function paymentsForMonth(string $yearMonth): ?float
    {
        $result = $this->db->query("
            SELECT SUM(`sum`) AS summary FROM (
                SELECT `paid_date`, `status`, SUM(`sum`) AS `sum`
                FROM `invoices`
                WHERE `status` = 'Paid' AND `paid_date` BETWEEN '{$yearMonth}-01' AND '{$yearMonth}-31'
                UNION ALL
                SELECT T3.`date` AS `paid_date`, T4.`status`, SUM(T3.`amount`) AS `sum`
                FROM `invoice_has_payments` AS T3
                LEFT JOIN `invoices` AS T4 ON T3.invoice_id = T4.id
                WHERE T4.`status` = 'PartiallyPaid' AND T3.`date` BETWEEN '{$yearMonth}-01' AND '{$yearMonth}-31'
            ) t1
        ")->getRow();

        return $result ? (float) $result->summary : null;
    }


    /**
     * Get sum of outstanding payments.
     *
     * @param string|false $yearMonth
     * @return float|null
     */
    public function outstandingPayments($yearMonth = false): ?float
    {
        $where = $yearMonth ? "AND `due_date` BETWEEN '{$yearMonth}-01' AND '{$yearMonth}-31'" : '';
        $open = $this->db->query("
            SELECT SUM(`sum`) AS summary
            FROM `invoices`
            WHERE (`status` = 'Sent' OR `status` = 'Open')
            AND `estimate` != 1 $where
        ")->getRow();

        $partially = $this->db->query("
            SELECT SUM(`outstanding`) AS summary
            FROM `invoices`
            WHERE `status` = 'PartiallyPaid' $where
        ")->getRow();

        return $open ? (float) $open->summary + (float) $partially->summary : null;
    }

    /**
     ** Get sum of outstanding payments 
     ** return object
     **/
    public static function totalExpensesForYear($year)
    {
        $expensesModel = new ExpenseModel();
        $expenses = $expensesModel->selectSum('value', 'summary')
            ->where('date >=', "$year-01-01")
            ->where('date <=', "$year-12-31")
            ->first();


        return $expenses->summary ?? null;
    }
    public static function OverdueByDate(int $userId, array $comp_array, string $date): array
    {
        // Initialize the query builder
        $builder = Invoice::query();

        // Add filters based on the company array and user ID
        if (!empty($comp_array)) {
            $builder->groupStart()
                ->where('user_id', $userId)
                ->orWhereIn('company_id', $comp_array)
                ->groupEnd();
        }

        // Build the query for overdue invoices
        $invoices = $builder->select('reference, id, due_date')
            ->where('status !=', 'Paid')
            ->where('status !=', 'Canceled')
            ->where('due_date <', $date)
            ->where('estimate !=', 1)
            ->orderBy('due_date')
            ->get()
            ->getResultArray();

        return $invoices;
    }

    /**
     * Get statistics for given date range.
     *
     * @param string $start
     * @param string $end
     * @return array
     */
    public function getStatisticFor(string $start, string $end): array
    {
        // First subquery for paid invoices
        $subquery1 = $this->select('SUBSTR(paid_date, 1, 7) AS month, SUM(`sum`) AS sum')
            ->table('invoices')
            ->where('status', 'Paid')
            ->where('paid_date >=', $start)
            ->where('paid_date <=', $end)
            ->groupBy('SUBSTR(paid_date, 1, 7');

        // Second subquery for partially paid invoices
        $subquery2 = $this->select('SUBSTR(T3.date, 1, 7) AS month, SUM(T3.amount) AS sum')
            ->table('invoice_has_payments AS T3')
            ->join('invoices AS T4', 'T3.invoice_id = T4.id', 'left')
            ->where('T4.status', 'PartiallyPaid')
            ->where('T3.date >=', $start)
            ->where('T3.date <=', $end)
            ->groupBy('SUBSTR(T3.date, 1, 7)');

        // Combine both subqueries with UNION
        $finalQuery = $this->db->query("
        SELECT month, SUM(sum) AS summary
        FROM (
            {$subquery1->getQueryString()} 
            UNION ALL 
            {$subquery2->getQueryString()}
        ) AS combined
        GROUP BY month
    ");

        return $finalQuery->getResultArray();
    }

    /**
     ** Get sum of payments grouped by Month for statistics
     ** return object
     **/
    public function getStatisticForClients(string $start, string $end): array
    {
        $subQuery1 = $this->select('paid_date AS paid_date, company_id AS company_id, SUM(`sum`) AS `sum`')
            ->from('invoices')
            ->where('status', 'Paid')
            ->where('paid_date >=', $start)
            ->where('paid_date <=', $end)
            ->groupBy('company_id');

        $subQuery2 = $this->select('T3.date AS paid_date, T4.company_id AS company_id, SUM(T3.amount) AS `sum`')
            ->from('invoice_has_payments AS T3')
            ->join('invoices AS T4', 'T3.invoice_id = T4.id', 'left')
            ->where('T4.status', 'PartiallyPaid')
            ->where('T3.date >=', $start)
            ->where('T3.date <=', $end)
            ->groupBy('T4.company_id');

        $query = $this->db->query("
        SELECT paid_date, company_id, SUM(`sum`) AS summary 
        FROM (
            {$subQuery1->getQueryString()} 
            UNION ALL 
            {$subQuery2->getQueryString()}
        ) t1 
        GROUP BY company_id
    ");

        return $query->getResultArray();
    }

    public function getStatisticsFor($start, $end)
    {
        return $this->select('paid_date, SUM(amount) as summary')
            ->where('paid_date >=', $start)
            ->where('paid_date <=', $end)
            ->groupBy('MONTH(paid_date)')
            ->findAll();
    }

    public function getExpensesStatisticsFor($start, $end)
    {
        return $this->select('date, SUM(expense_amount) as summary')
            ->where('date >=', $start)
            ->where('date <=', $end)
            ->groupBy('MONTH(date)')
            ->findAll();
    }

    public function getStatisticsForClients($start, $end)
    {
        return $this->select('company_id, SUM(amount) as summary')
            ->where('date >=', $start)
            ->where('date <=', $end)
            ->groupBy('company_id')
            ->findAll();
    }
}
