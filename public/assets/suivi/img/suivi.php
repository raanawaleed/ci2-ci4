<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Suivi extends My_Controller
{
    function __construct()
	{
		parent::__construct();
		$access = FALSE;
		if($this->client){	
			if($this->input->cookie('fc2_link') != ""){
					$link = $this->input->cookie('fc2_link');
					$link = str_replace("/tickets/", "/ctickets/", $link);
					redirect($link);
			}else{
				redirect('cprojects');
			}
			
		}elseif($this->user){
			foreach ($this->view_data['menu'] as $key => $value) { 
				if($value->link == "calendar"){ $access = TRUE;

				}
			}
			//if(!$access){redirect('login');}
		}else{
			redirect('login');
		}
		$this->view_data['submenu'] = array(
				 		$this->lang->line('application_all') => 'projects/filter/all',
				 		$this->lang->line('application_open') => 'projects/filter/open',
				 		$this->lang->line('application_closed') => 'projects/filter/closed'
				 		);	
		$this->load->database();
        $this->load->model('user_model');	
        $this->load->model('salarie_model');
        // $this->view_data['conges']  =  Conges::all();
        $this->load->helper('suivi_helper');
        
        $this->load->model('ref_type_occurences_model','referentiels');
        $this->_week_day_format = new IntlDateFormatter('en_US.UTF8', IntlDateFormatter::NONE,
                IntlDateFormatter::NONE, NULL, NULL, "cccccc");
	}	


    public function index($year = 0, $month = 0, $item_id = 0)
    {
  
        $options = array('conditions' => array('statut=?','28'));
        $conges = Conges::find('all',$options); 
        $this->view_data['conges']  =  $conges;
        
         $items = $this->salarie_model->getAll();
         
         $m_num = 1;
       
         $m_data = month_offset($year, $month, 0);
        
        $next_month_data = month_offset($year, $month, $m_num );
        $prev_month_data = month_offset($year, $month, -$m_num);

        $year_prev = $prev_month_data['year'];
        $month_prev = $prev_month_data['month'];

        $year_next = $next_month_data['year'];
        $month_next = $next_month_data['month'];
       
 
     
        $year = $m_data['year'];
        $month = $m_data['month'];
         $header =  $this->_header($year,$month);
         $name = $header['name'];
         $data_m = $header['month_data'];
         $item_id = 1;

        $new_data =array(
            'users' => $items,
           'name' => $name,
            'data_m' =>$data_m,
            'year' => $year,
            'month' => $month,
            'year_prev' => $year_prev,
            'month_prev' => $month_prev,
            'year_next' => $year_next,
            'month_next' => $month_next,
            'item_id' =>$item_id,
            'conges' =>$conges
        );
        
        $this->view_data['data']=$new_data;
        // $this->content_view = 'suivi/parts/header';
        $this->content_view = 'suivi/index';
      
    
    }



    /**
     * calendar function
     */
    public function calendar($year, $month, $events,  $is_editor = false, $show_all = false, $item_to_show = 0)
    {
        $year='2021';
        $month='5';
        $this->_header($year, $month, $is_editor);
    }

    private function _header($year, $month)
    {
        $month_data = $this->get_month_data($year, $month);
        // return;
        $data =array(
            'month_data' =>$month_data,
            'name' => $month_data['name'],
        );
       return  $data;
    }

    function _rows( $year, $month, $rows = array())
    {
        if (count($rows) !== 0)
        {
        ?>
            <p class="no-items">Brak elementów do wyświetlenia.</p>
        <?php
            return;
        }

        $month_data = $this->get_month_data($year, $month);

        $previous_row_item_type_id = null;

        foreach ($rows as $row_data)
        {

            $is_new_type = ($previous_row_item_type_id !== null && $row_data['item']->type->id != $previous_row_item_type_id) ? true : false;

            $previous_row_item_type_id = $row_data['item']->type->id;
            ?>

            <?php if ($is_new_type): ?>
                <div class="separator"></div>
            <?php endif; ?>

            <div class="item" data-item-id="<?php echo $row_data['item']->id ?>" data-type-id="<?php echo $previous_row_item_type_id; ?>">
                <div class="name-column">
                    <?php echo $this->_get_item($row_data['item']); ?>
                </div>
                <div class="days">
                    <?php foreach ($month_data['days'] as $day): ?>
                        <?php $this->_render_day_cell($day, isset($row_data['days'][$day['num']]) ? $row_data['days'][$day['num']] : array()) ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php
        }
    }

      // month_offset allows to add or substract month
      public function get_month_data($year, $month_num)
      {
          $month_num = (int)$month_num; //ensure that is not have leading zeros
          
          $date_obj = DateTime::createFromFormat('!n', $month_num);
  
          $day_num = cal_days_in_month(CAL_GREGORIAN, $month_num, $year);
  
          $month = array(
              'num' => $month_num,
              'name' => get_month_name($month_num),
              'year' => $year,
              'days' => array(),
          );
  
          for ($i = 1; $i <= $day_num; $i++)
          {
              $date_obj = date_create_from_format('Y-n-j', $year . '-' . $month_num . '-' . $i);
              $week_day_num = $date_obj->format('N');
              $week_day_name = datefmt_format( $this->_week_day_format, mktime(0, 0, 0, $month_num, $i, $year));
  
              $now = new DateTime();
              // Setting the time to 0 will ensure the difference is measured only in days
              $now->setTime(0, 0, 0);
              $date_obj->setTime(0, 0, 0);
  
              $today = $now->diff($date_obj)->days === 0; // Today
  
              $day = array(
                  'num' => $i,
                  'week_num' => $week_day_num,
                  'name' => $week_day_name,
                  'date_string' => $date_obj->format('Y-m-d'), //format 
                  'is_today' => $today,
              );
  
              $month['days'][$i] = $day;
          }
  
          return $month;
      }

    /**
     * Prepares html with browse calendars
     * 
     * @param type $year
     * @param type $month
     * @param type $items
     * @param type $item_to_show
     * @return type
     */    
    private function _browse_calendars($year, $month, $items, $item_to_show = null)
    {
        $this->load->model('Event');
        $items = $this->user_model->getAll();
        for ($i = 0; $i <$month; $i++)
        {
            $m_data = month_offset($year, $month, $i);
          
           $events= $this->load->model('Event');
            $calendar_data[] = array(
                'year' => $m_data['year'],
                'month' => $m_data['month'],
                //get events only for allowed items
                'events' => $events
            );
        }


        $this->load->library('cal_renderer');

        $data = array(
            'cal_renderer' => $this->cal_renderer->calendar(),
            'cal_data' => $calendar_data,
            'items' => '$items', //pass all items
            'item_to_show' => $item_to_show ? $item_to_show : false,
        ); 
         $this->view_data['data']=$data['cal_renderer'];
         $this->content_view = 'suivi/parts/cal_months';
        //$this->load->view('puivi/parts/cal_months', $data); 

         
        
    }

    //EDIT MONTH FUNCTION

    public function edit($year = 0, $month = 0, $item_id = 0)
    {
            
        $items = $this->salarie_model->getAll();

        $m_num = 1;
       
        $m_data = month_offset($year, $month, 0);
       
       $next_month_data = month_offset($year, $month, $m_num );
       $prev_month_data = month_offset($year, $month, -$m_num);

       $year_prev = $prev_month_data['year'];
       $month_prev = $prev_month_data['month'];

       $year_next = $next_month_data['year'];
       $month_next = $next_month_data['month'];
      

    
       $year = $m_data['year'];
       $month = $m_data['month'];
        $header =  $this->_header($year,$month);
        $name = $header['name'];
        $data_m = $header['month_data'];
        $item_id = 1;

       $new_data =array(
           'users' => $items,
          'name' => $name,
           'data_m' =>$data_m,
           'year' => $year,
           'month' => $month,
           'year_prev' => $year_prev,
           'month_prev' => $month_prev,
           'year_next' => $year_next,
           'month_next' => $month_next,
           'item_id' =>$item_id
       );
       $this->view_data['data']=$new_data;
       // $this->content_view = 'suivi/parts/header';
       $this->content_view = 'suivi/edit';
    }


}


