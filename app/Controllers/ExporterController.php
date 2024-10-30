<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ExporterController extends BaseController
{
	function __construct()
	{
		parent::__construct();	
		$this->load->model('projects_model','project');
		$this->load->model('Facture_model','invoice');
		$this->load->model('estimate_model','estimate');
		$this->load->model('Ref_type_occurences_model','referentiels');
		$this->load->model('RefType_model','refType');
		$this->load->model('item_model','item');
		$this->load->model('Itemfamily_model','itemFamily');
		$this->load->model('factureHasItem_model','factureHasItem');
		$this->load->model('company_model','companies');
		if ($this->client) {
		} elseif ($this->user) {
		} else {
			redirect('login');
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
	function itemsxlsx()
	{
		$filename = "articles-export-" . date("d-m-Y") . ".xls";
		$result = $this->item->getForExport();
		//var_dump($result);exit;

		$this->load->library('PHPExcel', NULL, 'excel');
		$this->excel->setActiveSheetIndex(0);
		$sheet = $this->excel->getActiveSheet();
		$style['entete_td'] = array('font' => array( 'bold'  => true,
														 'color' => array( 'rgb'=>'ffffff')
													),
									'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
														'	vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
															'wrap'    => true
													),
									'fill' => array(    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                'color' => array('rgb' => '333333'
													)
                                    ) );

		$entete = array(
					      'name' => 'Nom',
					      'description' => 'Description',
					      'libelle' => 'Famille',
					      'value' => 'Prix HT',
					      'ttc' => 'Prix TTC',
					      'tva' => 'TVA',
					      'unit' => 'Unité');

		$col = 0;
		foreach ($entete as $key => $val) {
			$sheet->setCellValueByColumnAndRow($col, 1, $val);
			$sheet->getStyleByColumnAndRow($col,1)->applyFromArray($style['entete_td']);
                            
			$col++;				
		}

		$counter = 2;
		foreach ($result as $key => $company) {
			$char = "A";

		foreach ($company as $key => $value) {
				if(isset($entete[$key]) ){
					

					$sheet->setCellValue("$char$counter", $value);
					$char++;
						
				}
			}

			$counter++;
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		
		$writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$writer->save('php://output');
		exit;
	}

	//-- Export des clients dans un Excel
	function clients_as_excel($passager = FALSE) {
		$filename = "clients-export-" . date("d-m-Y") . ".xls";
		$this->db->where('id_vcompanies', $_SESSION['current_company']);
		$this->db->select(implode(",", array_keys($this->keys_clients)));
		if($passager){
			$result = $this->companies->getForExportPassagers();
		}
		else {
			$result = $this->companies->getForExport();
		}
		//$result = $this->db->get('companies')->result_array();

		
		
		
      	$entete = array(
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
					      'guarantee' => 'Guarantie',
					      'tva' => 'TVA',
					      'note' => 'Note');
		
		$col = 0;
		foreach ($entete as $key => $val) {
			$sheet->setCellValueByColumnAndRow($col, 1, $val);
			$sheet->getStyleByColumnAndRow($col,1)->applyFromArray($style['entete_td']);
                            
			$col++;				
		}

		$counter = 2;
		foreach ($result as $key => $company) {
			$char = "A";

			foreach ($company as $key => $value) {
				if(isset($entete[$key]) ){
					if(in_array($key , array( 'timbre_fiscal', 'guarantee' , 'passager')))
						$value = ($value == 1)? 'Oui':'Non';

					$sheet->setCellValue("$char$counter", $value);
					$char++;
						
				}
			}

			$counter++;
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		
		$writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$writer->save('php://output');
		exit;
	}
	

	function devis_as_excel() 
	{
		$this->load->model('estimate_model');
		$data= $this->estimate->getElementForExcel();
		foreach($data as $val){
			$val->company_id = Company::find($val->company_id)->name; 
			$val->estimate_status = $this->lang->line('application_'.($this->referentiels->getReferentielsById($val->estimate_status)->name));
		}  
		$header = array ();
		$header[0] = 'Référence'; 
		$header[1] = 'Client'; 
		$header[2] = 'Objet'; 
		$header[3] = "Date d'émission"; 
		$header[4] = 'Devise'; 
		$header[5] = 'Total TTC'; 
		$header[6] = 'Status';  
		$filename='devis.csv'; 
		$this->export($filename,$data,$header);
	}
	//-- Export des factures dans un Excel
	public function factures_as_excel()
	{   
		$this->load->model('Facture_model');
		$data= $this->invoice->getElementForExcel();
		foreach($data as $val){
			$val->company_id = Company::find($val->company_id)->name; 
			$val->status = $this->lang->line('application_'.($this->referentiels->getReferentielsById($val->status)->name));
		}  
		$header = array ();
		$header[0] = 'Id Facture'; 
		$header[1] = 'Client'; 
		$header[2] = 'Objet'; 
		$header[3] = "Date d'émission"; 
		$header[4] = 'Devise'; 
		$header[5] = 'Total HT'; 
		$header[6] = 'Total TTC'; 
		$header[7] = 'Status'; 
		$filename='factures.csv'; 
		$this->export($filename,$data,$header);
    }
	
	function export($filename,$data,$header)
	{
		
		$this->load->library('PHPExcel', NULL, 'excel');
		$this->excel->setActiveSheetIndex(0);
		$sheet = $this->excel->getActiveSheet();
		$style['entete_td'] = array('font' => array( 'bold'  => true,
														 'color' => array( 'rgb'=>'ffffff')
													),
									'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
														'	vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
															'wrap'    => true
													),
									'fill' => array(    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                'color' => array('rgb' => '333333'
													)
                                    ) );
		$sheet->setCellValue('A1', $header[0]);
		$sheet->setCellValue('B1', $header[1]);
		$sheet->setCellValue('C1', $header[2]);
		$sheet->setCellValue('D1', $header[3]);
		$sheet->setCellValue('E1', $header[4]);
		$sheet->setCellValue('F1', $header[5]);
		$sheet->setCellValue('G1', $header[6]);
		$sheet->setCellValue('H1', $header[7]);
		if(count($data)>0)
		{
			$fields = array_keys($data[0]);
			$col = 0;
			foreach ($fields as $key=>$field):
				$sheet->setCellValueByColumnAndRow($col, 1, $field->id);
				$sheet->getStyleByColumnAndRow($col,1)->applyFromArray($style['entete_td']);
				$col++;
			endforeach;
			$row = 2;
			foreach ($data as $key=>$tab):
				$col = 0;
				foreach ($tab as $key2=>$value):
					$sheet->setCellValueByColumnAndRow($col, $row, $value);
					$col++;
				endforeach;    
				$row++;
			endforeach;
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreads$preadsheetml.sheet');
		header('Content-Disposition: attachment;filename='.$filename);
		header('Cache-Control: max-age=0');
		$writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$writer->save('php://output');
             
	}
}