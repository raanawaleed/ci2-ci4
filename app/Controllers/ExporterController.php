<?php

namespace App\Controllers;

use App\Models\ProjectsModel;
use App\Models\FactureModel;
use App\Models\EstimateModel;
use App\Models\RefTypeOccurencesModel;
use App\Models\RefTypeModel;
use App\Models\ItemsModel;
use App\Models\ItemFamilyModel;
use App\Models\FactureHasItemModel;
use App\Models\CompanyModel;
use CodeIgniter\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExporterController extends Controller
{
	protected $itemModel;
	protected $invoiceModel;
	protected $estimateModel;
	protected $referentielsModel;
	protected $refTypeModel;
	protected $companiesModel;

	public function __construct()
	{
		$this->itemModel = new ItemsModel();
		$this->invoiceModel = new FactureModel();
		$this->estimateModel = new EstimateModel();
		$this->referentielsModel = new RefTypeOccurencesModel();
		$this->refTypeModel = new RefTypeModel();
		$this->companiesModel = new CompanyModel();

		// Check user authentication
		if (!session()->get('client') && !session()->get('user')) {
			return redirect()->to('login');
		}
	}/*
	
	function salaries_import_template() {
		$filename = "Salaries-template.xls";
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');

		$char = "A";
		foreach ($this->keys2 as $key2 => $value) {
			$this->excel->setActiveSheetIndex(0)->setCellValue("{$char}1", $value);
			$this->excel->getActiveSheet()->getStyle("{$char}1")->getFont()->setBold(true);
			$char++;
		}
		
		$writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$writer->save('php://output');
	}



function salaries_import_excel() {
		if($_POST){

			$config['upload_path'] = './files/excel/';
			$config['encrypt_name'] = TRUE;
			$config['allowed_types'] = '*';
			$this->load->library('upload', $config);

			$extention = end(explode(".", $_FILES["userfile"]["name"]));
			

			$this->upload->initialize($config);

			if ($extention == "xls" || $extention == "xlsx") {
				if ($this->upload->do_upload())
				{

						//var_dump('file upload success'); 

					//$core = Setting::find(array("id_vcompanies" => $_SESSION['current_company'])); 
					$data = array('upload_data' => $this->upload->data());
					$filepath = $config['upload_path'] . $data['upload_data']['file_name'];

					$this->load->helper('excel');

					try {
						$excel =  PHPExcel_IOFactory::load($filepath);
						$data = $excel->getActiveSheet()->toArray(null, true, true, true);
						$head = array_slice($data, 0, 1)[0];
						$data = array_slice($data, 1);

						if (count($data) > 0 && array_values($this->keys2) == array_values($head)) {
							$counter = $core->company_reference;
							$count = 0;
							$model = array_keys($this->keys2);


								$salaries=$this->db->select('*')->from('salaries')->where('idcompanie',(int)$_SESSION['current_company'])->order_by('id','desc')->get()->result();
					
							foreach ($data as $row) {

								$z = 0;
									

								if (is_null($row["A"])) break;

								$record = array_values($row);
								$insertion = array_combine($model, $record);
								$insertion['idcompanie'] = $_SESSION['current_company'];
								//$insertion['reference'] = sprintf("%04d", $counter);
								
									$salaries=$this->db->select('numerocin')->from('salaries')->where('idcompanie',(int)$_SESSION['current_company'])->order_by('id','desc')->get()->result();

									foreach ($salaries as $sarl) 
									{

										$a1 = (int)$sarl->numerocin;
										var_dump($a1);
										$a2 = (int)($insertion['numerocin']);
										var_dump($a2);

										if($a1 == $a2)
										{
											$z++;	
										}


									};	
									if($z == 0)
									{
										$this->db->insert('salaries',$insertion);
									}						

										$counter++;
										$count++;


							}


							$this->session->set_flashdata('message', 'success:' . str_replace("{N}", $count, $this->lang->line('messages_import_success')));
						} else {
							$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_file_invalid'));
						}
					} catch (Exception $e) {
						$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_file_invalid'));
					}
				} else {
					
					$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_file_upload_error'));
				}
			} else {
				$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_file_type_invalid'));
			}

			redirect('gestionsalarie');
		}else
		{
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_import');
			$this->view_data['form_action'] = 'exporter/salaries_import_excel';
			$this->content_view = 'rhpaie/_import';
		}	
	}



		function salaries_as_excel() {

	$genre = $this->db->select('*')->from('ref_type_occurences')->where('id_type',13)->get()->result();
		$situations =$this->db->select('*')->from('ref_type_occurences')->where('id_type',12)->get()->result();


		$filename = "Salaries-" . date("d-m-Y") . ".xls";
		 
		$this->db->select(implode(",", array_keys($this->keys2)));
		$this->db->where('idcompanie',(int)$_SESSION['current_company']);



		$result = $this->db->get('salaries')->result_array();



					// foreach ($result as $vas1)
					// {

					// 	foreach ($genre as $vas2) 
					// 	{

					// 			if((int)$vas1['genre'] == (int)$vas2->id)
					// 			{
					// 				$vas1['genre'] = $vas2->name;


					// 			}

					// 		foreach ($situations as $vas3) 
					// 		{
									

					// 			if((int)$vas1['situationfamiliale'] == (int)$vas3->id)
					// 			{
					// 				$vas1['situationfamiliale'] = $vas3->name;

					// 			}

					// 		}

					// 	}
					// }
						

		$head    = false;
		$counter = 1;


		foreach ($result as $company) {
			$char = "A";

			if (!$head) {
				foreach ($company as $key2 => $value) {

					$this->excel->setActiveSheetIndex(0)->setCellValue("$char$counter", $this->keys2[$key2]);
					$this->excel->getActiveSheet()->getStyle("$char$counter")->getFont()->setBold(true);
					$char++;
				}
				
				$head = true;
				$counter++;
				$char = "A";
			}

			foreach ($company as $key2 => $value) {
				$this->excel->setActiveSheetIndex(0)->setCellValue("$char$counter", $value);
				$char++;
			}

			$counter++;
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		
		$writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$writer->save('php://output');
		exit;
	}



		function conges_as_excel() {
		$filename = "Congés-" . date("d-m-Y") . ".xls";
		 
		$this->db->select(implode(",", array_keys($this->keys3)));
		$this->db->where('id_companie',(int)$_SESSION['current_company']);
		$result = $this->db->get('conges')->result_array();

		$head    = false;
		$counter = 1;

		foreach ($result as $company) {
			$char = "A";

			if (!$head) {
				foreach ($company as $key3 => $value) {

					$this->excel->setActiveSheetIndex(0)->setCellValue("$char$counter", $this->keys3[$key3]);
					$this->excel->getActiveSheet()->getStyle("$char$counter")->getFont()->setBold(true);
					$char++;
				}
				
				$head = true;
				$counter++;
				$char = "A";
			}

			foreach ($company as $key3 => $value) {
				$this->excel->setActiveSheetIndex(0)->setCellValue("$char$counter", $value);
				$char++;
			}

			$counter++;
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		
		$writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$writer->save('php://output');
		exit;
	}



	function prets_as_excel() {
		$filename = "Prêt-" . date("d-m-Y") . ".xls";
		 
		$this->db->select(implode(",", array_keys($this->keys4)));
		$this->db->where('id_companie',(int)$_SESSION['current_company']);
		$result = $this->db->get('prets')->result_array();

		$head    = false;
		$counter = 1;

		foreach ($result as $company) {
			$char = "A";

			if (!$head) {
				foreach ($company as $key4 => $value) {

					$this->excel->setActiveSheetIndex(0)->setCellValue("$char$counter", $this->keys4[$key4]);
					$this->excel->getActiveSheet()->getStyle("$char$counter")->getFont()->setBold(true);
					$char++;
				}
				
				$head = true;
				$counter++;
				$char = "A";
			}

			foreach ($company as $key4 => $value) {
				$this->excel->setActiveSheetIndex(0)->setCellValue("$char$counter", $value);
				$char++;
			}

			$counter++;
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		
		$writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$writer->save('php://output');
		exit;
	}
	//end of all

//RHPAIE FIN






	function clients_import_template() {
		$filename = "Clients-template.xls";
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');

		$char = "A";
		foreach ($this->keys as $key => $value) {
			$this->excel->setActiveSheetIndex(0)->setCellValue("{$char}1", $value);
			$this->excel->getActiveSheet()->getStyle("{$char}1")->getFont()->setBold(true);
			$char++;
		}
		
		$writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$writer->save('php://output');
	}

	function clients_import_excel() {
		if($_POST){
			$config['upload_path'] = './files/excel/';
			$config['encrypt_name'] = TRUE;
			$config['allowed_types'] = '*';
			$this->load->library('upload', $config);

			$extention = end(explode(".", $_FILES["userfile"]["name"]));

			if ($extention == "xls" || $extention == "xlsx") {
				if ($this->upload->do_upload())
				{
					$core = Setting::find(array("id_vcompanies" => $_SESSION['current_company'])); 
					$data = array('upload_data' => $this->upload->data());
					$filepath = $config['upload_path'] . $data['upload_data']['file_name'];

					$this->load->helper('excel');

					try {
						$excel =  PHPExcel_IOFactory::load($filepath);
						$data = $excel->getActiveSheet()->toArray(null, true, true, true);
						$head = array_slice($data, 0, 1)[0];
						$data = array_slice($data, 1);

						if (count($data) > 0 && array_values($this->keys) == array_values($head)) {
							$counter = $core->company_reference;
							$count = 0;
							$model = array_keys($this->keys);

							foreach ($data as $row) {
								if (is_null($row["A"])) break;

								$record = array_values($row);
								$insertion = array_combine($model, $record);
								$insertion['id_vcompanies'] = $_SESSION['current_company'];
								$insertion['reference'] = sprintf("%04d", $counter);
								
								Company::create($insertion);

								$counter++;
								$count++;
							}

							$core->company_reference = $counter;
							$core->save();

							$this->session->set_flashdata('message', 'success:' . str_replace("{N}", $count, $this->lang->line('messages_import_success')));
						} else {
							$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_file_invalid'));
						}
					} catch (Exception $e) {
						$this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_file_invalid'));
					}
				} else {
					$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_file_upload_error'));
				}
			} else {
				$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_file_type_invalid'));
			}

			redirect('clients');
		}else
		{
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_import');
			$this->view_data['form_action'] = 'exporter/clients_import_excel';
			$this->content_view = 'clients/_import';
		}	
	}

	*/

	//-- Export des articles (items) dans un Excel
	public function itemsxlsx()
	{
		$filename = "articles-export-" . date("d-m-Y") . ".xlsx";
		$result = $this->itemModel->getForExport();

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$styleHeader = [
			'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff']],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				'wrap' => true,
			],
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => ['rgb' => '333333'],
			],
		];

		$header = [
			'name' => 'Nom',
			'description' => 'Description',
			'libelle' => 'Famille',
			'value' => 'Prix HT',
			'ttc' => 'Prix TTC',
			'tva' => 'TVA',
			'unit' => 'Unité',
		];

		$col = 1;
		foreach ($header as $val) {
			$sheet->setCellValueByColumnAndRow($col++, 1, $val);
			$sheet->getStyleByColumnAndRow($col - 1, 1)->applyFromArray($styleHeader);
		}

		$row = 2;
		foreach ($result as $company) {
			$col = 1;
			foreach ($header as $key => $val) {
				if (isset($company[$key])) {
					$sheet->setCellValueByColumnAndRow($col++, $row, $company[$key]);
				}
			}
			$row++;
		}

		return $this->downloadExcel($spreadsheet, $filename);
	}

	public function clientsAsExcel($passager = false)
	{
		$filename = "clients-export-" . date("d-m-Y") . ".xlsx";
		$this->companiesModel->setCompanyId(session()->get('current_company'));
		$result = $passager ? $this->companiesModel->getForExportPassagers() : $this->companiesModel->getForExport();

		$header = [
			'reference' => 'Reference',
			'name' => 'Nom',
			'phone' => 'Téléphone',
			'mobile' => 'Mobile',
			'address' => 'Adresse',
			'zipcode' => 'Code Zip',
			'city' => 'Ville',
			'website' => 'Site Web',
			'email' => 'Email',
			'country' => 'Pays',
			'vat' => 'MF',
			'timbre_fiscal' => 'Timbre fiscal',
			'guarantee' => 'Garantie',
			'tva' => 'TVA',
			'note' => 'Note',
		];

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$styleHeader = $this->getHeaderStyle();

		$col = 1;
		foreach ($header as $val) {
			$sheet->setCellValueByColumnAndRow($col++, 1, $val);
			$sheet->getStyleByColumnAndRow($col - 1, 1)->applyFromArray($styleHeader);
		}

		$row = 2;
		foreach ($result as $company) {
			$col = 1;
			foreach ($header as $key => $val) {
				$value = isset($company[$key]) ? $company[$key] : '';
				if (in_array($key, ['timbre_fiscal', 'guarantee', 'passager'])) {
					$value = ($value == 1) ? 'Oui' : 'Non';
				}
				$sheet->setCellValueByColumnAndRow($col++, $row, $value);
			}
			$row++;
		}

		return $this->downloadExcel($spreadsheet, $filename);
	}

	public function devisAsExcel()
	{
		$data = $this->estimateModel->getElementForExcel();
		foreach ($data as $val) {
			$val->company_id = $this->companiesModel->find($val->company_id)->name;
			$val->estimate_status = lang('application_' . $this->referentielsModel->getReferentielsById($val->estimate_status)->name);
		}

		$header = [
			'Référence',
			'Client',
			'Objet',
			"Date d'émission",
			'Devise',
			'Total TTC',
			'Status',
		];
		$filename = 'devis.xlsx';

		return $this->export($filename, $data, $header);
	}

	public function facturesAsExcel()
	{
		$data = $this->invoiceModel->getElementForExcel();
		foreach ($data as $val) {
			$val->company_id = $this->companiesModel->find($val->company_id)->name;
			$val->status = lang('application_' . $this->referentielsModel->getReferentielsById($val->status)->name);
		}

		$header = [
			'Id Facture',
			'Client',
			'Objet',
			"Date d'émission",
			'Devise',
			'Total HT',
			'Total TTC',
			'Status',
		];
		$filename = 'factures.xlsx';

		return $this->export($filename, $data, $header);
	}

	private function export($filename, $data, $header)
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$styleHeader = $this->getHeaderStyle();

		$col = 1;
		foreach ($header as $val) {
			$sheet->setCellValueByColumnAndRow($col++, 1, $val);
			$sheet->getStyleByColumnAndRow($col - 1, 1)->applyFromArray($styleHeader);
		}

		$row = 2;
		foreach ($data as $tab) {
			$col = 1;
			foreach ($tab as $value) {
				$sheet->setCellValueByColumnAndRow($col++, $row, $value);
			}
			$row++;
		}

		return $this->downloadExcel($spreadsheet, $filename);
	}

	private function downloadExcel($spreadsheet, $filename)
	{
		$writer = new Xlsx($spreadsheet);
		return $this->response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
			->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
			->setBody($writer->save('php://output'));
	}

	private function getHeaderStyle()
	{
		return [
			'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff']],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				'wrap' => true,
			],
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => ['rgb' => '333333'],
			],
		];
	}
}
