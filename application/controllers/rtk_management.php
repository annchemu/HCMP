<?php
/*

*/
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

include_once ('home_controller.php');

class Rtk_Management extends Home_controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        ini_set('memory_limit', '-1');
        ini_set('max_input_vars', 3000);
    }

    public function index() {
        echo "|";
    }

    //SCMLT FUNCTUONS

    public function scmlt_home(){
        $district = $this->session->userdata('district_id');                
        $facilities = Facilities::get_total_facilities_rtk_in_district($district);       
        $district_name = districts::get_district_name_($district);                    
        $table_body = '';
        $reported = 0;
        $nonreported = 0;
        $date = date('d', time());

        $msg = $this->session->flashdata('message');
        if(isset($msg)){
            $data['notif_message'] = $msg;
        }
        if(isset($popout)){
            $data['popout'] = $popout;
        }
        
        $sql = "select distinct rtk_settings.* 
        from rtk_settings, facilities 
        where facilities.zone = rtk_settings.zone 
        and facilities.rtk_enabled = 1";
        $res_ddl = $this->db->query($sql);
        $deadline_date = null;
        $settings = $res_ddl->result_array();
        foreach ($settings as $key => $value) {
            $deadline_date = $value['deadline'];
            $five_day_alert = $value['5_day_alert'];
            $report_day_alert = $value['report_day_alert'];
            $overdue_alert = $value['overdue_alert'];
        }
        date_default_timezone_set("EUROPE/Moscow");

        foreach ($facilities as $facility_detail) {

           $lastmonth = date('F', strtotime("last day of previous month"));
           if($date>$deadline_date){
            $report_link = "<span class='label label-danger'>  Pending for $lastmonth </span> <a href=" . site_url('rtk_management/get_report/' . $facility_detail['facility_code']) . " class='link report'></a></td>";
        }else{
            $report_link = "<span class='label label-danger'>  Pending for $lastmonth </span> <a href=" . site_url('rtk_management/get_report/' . $facility_detail['facility_code']) . " class='link report'> Report</a></td>";
        }


        $table_body .="<tr><td><a class='ajax_call_1' id='county_facility' name='" . base_url() . "rtk_management/get_rtk_facility_detail/$facility_detail[facility_code]' href='#'>" . $facility_detail["facility_code"] . "</td>";
        $table_body .="<td>" . $facility_detail['facility_name'] . "</td><td>" . $district_name['district'] . "</td>";
        $table_body .="<td>";

        $lab_count = lab_commodity_orders::get_recent_lab_orders($facility_detail['facility_code']);
        if ($lab_count > 0) {
            $reported = $reported + 1;              
            $table_body .="<span class='label label-success'>Submitted  for    $lastmonth </span><a href=" . site_url('rtk_management/rtk_orders') . " class='link'> View</a></td>";
        } else {
            $nonreported = $nonreported + 1;
            $table_body .=$report_link;
        }

        $table_body .="</td>";
        }
        $county = $this->session->userdata('county_name');
        $countyid = $this->session->userdata('county_id');
        $data['countyid'] = $countyid;
        $data['county'] = $county;
        $data['table_body'] = $table_body;
        $data['content_view'] = "rtk/rtk/scmlt/dpp_home_with_table";
        $data['title'] = "Home";
        $data['link'] = "home";
        $total = $reported + $nonreported;
        $percentage_complete = $reported / $total * 100;
        $percentage_complete = number_format($percentage_complete, 0);
        $data['percentage_complete'] = $percentage_complete;
        $data['reported'] = $reported;
        $data['nonreported'] = $nonreported;
        $data['facilities'] = Facilities::get_total_facilities_rtk_in_district($district);
        $this->load->view('rtk/template', $data);

    }
    public function scmlt_orders($msg = NULL) {
        $district = $this->session->userdata('district_id');        
        $district_name = Districts::get_district_name($district)->toArray();        
        $d_name = $district_name[0]['district'];
        $countyid = $this->session->userdata('county_id');

        $data['countyid'] = $countyid;

        $data['title'] = "Orders";
        $data['content_view'] = "rtk/rtk/scmlt/rtk_orders_listing_v";
        $data['banner_text'] = $d_name . "Orders";
                //        $data['fcdrr_order_list'] = Lab_Commodity_Orders::get_district_orders($district);
        ini_set('memory_limit', '-1');

        date_default_timezone_set('EUROPE/moscow');
        $last_month = date('m');
                //            $month_ago=date('Y-'.$last_month.'-d');
        $month_ago = date('Y-m-d', strtotime("last day of previous month"));
        $sql = 'SELECT  
        facilities.facility_code,facilities.facility_name,lab_commodity_orders.id,lab_commodity_orders.order_date,lab_commodity_orders.district_id,lab_commodity_orders.compiled_by,lab_commodity_orders.facility_code
        FROM lab_commodity_orders, facilities
        WHERE lab_commodity_orders.facility_code = facilities.facility_code 
        AND lab_commodity_orders.order_date between ' . $month_ago . ' AND NOW()
        AND facilities.district =' . $district . '
        ORDER BY  lab_commodity_orders.id DESC ';
                   /*$query = $this->db->query("SELECT  
                    facilities.facility_code,facilities.facility_name,lab_commodity_orders.id,lab_commodity_orders.order_date,lab_commodity_orders.district_id,lab_commodity_orders.compiled_by,lab_commodity_orders.facility_code
                    FROM lab_commodity_orders, facilities
                    WHERE lab_commodity_orders.facility_code = facilities.facility_code 
                    AND lab_commodity_orders.order_date between '$month_ago ' AND NOW()
                    AND lab_commodity_orders.district_id =' . $district . '
                    ORDER BY  lab_commodity_orders.id DESC");*/
    $query = $this->db->query($sql);

    $data['lab_order_list'] = $query->result_array();
    $data['all_orders'] = Lab_Commodity_Orders::get_district_orders($district);
    $myobj = Doctrine::getTable('districts')->find($district);
                    //$data['district_incharge']=array($id=>$myobj->district);
    $data['myClass'] = $this;
    $data['d_name'] = $d_name;
    $data['msg'] = $msg;

    $this->load->view("rtk/template", $data);
    }
    public function scmlt_allocations($msg = NULL) {
        $district = $this->session->userdata('district_id');
        $district_name = Districts::get_district_name($district)->toArray();
        $countyid = $this->session->userdata('county_id');
        $data['countyid'] = $countyid;
        $d_name = $district_name[0]['district'];     
        $data['title'] = "Allocations";
        $data['content_view'] = "rtk/rtk/scmlt/rtk_allocation_v";
        $data['banner_text'] = $d_name . "Allocation";
    //        $data['lab_order_list'] = Lab_Commodity_Orders::get_district_orders($district);
        ini_set('memory_limit', '-1');

        $start_date = date("Y-m-", strtotime("-3 Month "));
        $start_date .='1';

        $end_date = date('Y-m-d', strtotime("last day of previous month"));      
        $allocations = $this->_allocation(NULL, $county = NULL, $district, $facility = NULL, $sincedate = NULL, $enddate = NULL);
        $data['lab_order_list'] = $allocations;
        $data['all_orders'] = Lab_Commodity_Orders::get_district_orders($district);
        $myobj = Doctrine::getTable('districts')->find($district);
        $data['myClass'] = $this;
        $data['msg'] = $msg;        
        $data['d_name'] = $d_name;

        $this->load->view("rtk/template", $data);
    }

    //Load FCDRR
    public function get_report($facility_code) {       

          $data['title'] = "Lab Commodities 3 Report";
          $data['content_view'] = "rtk/rtk/scmlt/fcdrr";
          $data['banner_text'] = "Lab Commodities 3 Report";
          $data['link'] = "rtk_management";
          $data['quick_link'] = "commodity_list";
          $my_arr = $this->_get_begining_balance($facility_code);
          $my_count = count($my_arr);
          $data['beginning_bal'] = $my_arr;         
          $data['facilities'] = Facilities::get_one_facility_details($facility_code);            
          $data['lab_categories'] = Lab_Commodity_Categories::get_active();
          $this->load->view("rtk/template", $data);
      }

      //Begining Balances
      function _get_begining_balance($facility_code) {
            $result_bal = array();
            $start_date_bal = date('Y-m-d', strtotime("first day of previous month"));
            $end_date_bal = date('Y-m-d', strtotime("last day of previous month"));
            $sql_bal = "SELECT lab_commodity_details.closing_stock from lab_commodity_orders, lab_commodity_details 
            where lab_commodity_orders.id = lab_commodity_details.order_id 
            and lab_commodity_orders.order_date between '$start_date_bal' and '$end_date_bal' 
            and lab_commodity_orders.facility_code='$facility_code'";

            $res_bal = $this->db->query($sql_bal);

            foreach ($res_bal->result_array() as $row_bal) {
                array_push($result_bal, $row_bal['closing_stock']);
            }
                return $result_bal;
      }

      //Save FCDRR
      public function save_lab_report_data() {

        date_default_timezone_set("EUROPE/Moscow");
        $firstday = date('D dS M Y', strtotime("first day of previous month"));
        $lastday = date('D dS M Y', strtotime("last day of previous month"));
        $lastmonth = date('F', strtotime("last day of previous month"));

        $month = $lastmonth;
        $district_id = $_POST['district'];
        $facility_code = $_POST['facility_code'];
        $drug_id = $_POST['commodity_id'];
        $unit_of_issue = $_POST['unit_of_issue'];
        $b_balance = $_POST['b_balance'];
        $q_received = $_POST['q_received'];
        $q_used = $_POST['q_used'];
        $tests_done = $_POST['tests_done'];
        $losses = $_POST['losses'];
        $pos_adj = $_POST['pos_adj'];
        $neg_adj = $_POST['neg_adj'];
        $physical_count = $_POST['physical_count'];
        $q_expiring = $_POST['q_expiring'];
        $days_out_of_stock = $_POST['days_out_of_stock'];
        $q_requested = $_POST['q_requested'];
        $commodity_count = count($drug_id);

        $vct = $_POST['vct'];
        $pitc = $_POST['pitc'];
        $pmtct = $_POST['pmtct'];
        $b_screening = $_POST['blood_screening'];
        $other = $_POST['other2'];
        $specification = $_POST['specification'];
        $rdt_under_tests = $_POST['rdt_under_tests'];
        $rdt_under_pos = $_POST['rdt_under_positive'];
        $rdt_btwn_tests = $_POST['rdt_to_tests'];
        $rdt_btwn_pos = $_POST['rdt_to_positive'];
        $rdt_over_tests = $_POST['rdt_over_tests'];
        $rdt_over_pos = $_POST['rdt_over_positive'];
        $micro_under_tests = $_POST['micro_under_tests'];
        $micro_under_pos = $_POST['micro_under_positive'];
        $micro_btwn_tests = $_POST['micro_to_tests'];
        $micro_btwn_pos = $_POST['micro_to_positive'];
        $micro_over_tests = $_POST['micro_over_tests'];
        $micro_over_pos = $_POST['micro_over_positive'];
        $beg_date = $_POST['begin_date'];
        $end_date = $_POST['end_date'];
        $explanation = $_POST['explanation'];
        $compiled_by = $_POST['compiled_by'];
        $moh_642 = $_POST['moh_642'];
        $moh_643 = $_POST['moh_643'];

        date_default_timezone_set('EUROPE/Moscow');
        $beg_date = date('Y-m-d', strtotime("first day of previous month"));
        $end_date = date('Y-m-d', strtotime("last day of previous month"));

        $user_id = $this->session->userdata('user_id');        

        $order_date = date('y-m-d');
        $count = 1;
        $data = array('facility_code' => $facility_code, 'district_id' => $district_id, 'compiled_by' => $compiled_by, 'order_date' => $order_date, 'vct' => $vct, 'pitc' => $pitc, 'pmtct' => $pmtct, 'b_screening' => $b_screening, 'other' => $other, 'specification' => $specification, 'rdt_under_tests' => $rdt_under_tests, 'rdt_under_pos' => $rdt_under_pos, 'rdt_btwn_tests' => $rdt_btwn_tests, 'rdt_btwn_pos' => $rdt_btwn_pos, 'rdt_over_tests' => $rdt_over_tests, 'rdt_over_pos' => $rdt_over_pos, 'micro_under_tests' => $micro_under_tests, 'micro_under_pos' => $micro_under_pos, 'micro_btwn_tests' => $micro_btwn_tests, 'micro_btwn_pos' => $micro_btwn_pos, 'micro_over_tests' => $micro_over_tests, 'micro_over_pos' => $micro_over_pos, 'beg_date' => $beg_date, 'end_date' => $end_date, 'explanation' => $explanation, 'moh_642' => $moh_642, 'moh_643' => $moh_643, 'report_for' => $lastmonth);
        $u = new Lab_Commodity_Orders();
        $u->fromArray($data);
        $u->save();
        $object_id = $u->get('id');
        $this->logData('13', $object_id);
        $this->update_amc($facility_code);

        $lastId = Lab_Commodity_Orders::get_new_order($facility_code);
        $new_order_id = $lastId->maxId;
        $count++;

        for ($i = 0; $i < $commodity_count; $i++) {            
            $mydata = array('order_id' => $new_order_id, 'facility_code' => $facility_code, 'district_id' => $district_id, 'commodity_id' => $drug_id[$i], 'unit_of_issue' => $unit_of_issue[$i], 'beginning_bal' => $b_balance[$i], 'q_received' => $q_received[$i], 'q_used' => $q_used[$i], 'no_of_tests_done' => $tests_done[$i], 'losses' => $losses[$i], 'positive_adj' => $pos_adj[$i], 'negative_adj' => $neg_adj[$i], 'closing_stock' => $physical_count[$i], 'q_expiring' => $q_expiring[$i], 'days_out_of_stock' => $days_out_of_stock[$i], 'q_requested' => $q_requested[$i]);
            Lab_Commodity_Details::save_lab_commodities($mydata);           
        }
        $q = "select county from districts where id='$district_id'";
        $res = $this->db->query($q)->result_array();
        foreach ($res as $key => $value) {
            $county = $value['county'];
        }
        $this->_update_reports_count('add',$county,$district_id);
        $this->session->set_flashdata('message', 'The report has been saved');
        redirect('rtk_management/scmlt_home');
        
    }

    //Edit FCDRR
    public function edit_lab_order_details($order_id, $msg = NULL) {
        $delivery = $this->uri->segment(3);
        $district = $this->session->userdata('district_id');
        $data['title'] = "Lab Commodity Order Details";    
         ini_set('memory_limit', '-1');
        $data['order_id'] = $order_id;
        $data['content_view'] = "rtk/rtk/scmlt/fcdrr_edit";
        $data['banner_text'] = "Lab Commodity Order Details";
        $data['lab_categories'] = Lab_Commodity_Categories::get_all();
        $data['detail_list'] = Lab_Commodity_Details::get_order($order_id);
        $result = $this->db->query('SELECT * 
            FROM lab_commodity_details, counties, facilities, districts, lab_commodity_orders, lab_commodity_categories, lab_commodities
            WHERE lab_commodity_details.facility_code = facilities.facility_code
            AND counties.id = districts.county
            AND facilities.facility_code = lab_commodity_orders.facility_code
            AND lab_commodity_details.commodity_id = lab_commodities.id
            AND lab_commodity_categories.id = lab_commodities.category
            AND facilities.district = districts.id
            AND lab_commodity_details.order_id = lab_commodity_orders.id
            AND lab_commodity_orders.id = ' . $order_id . '');
        $data['all_details'] = $result->result_array();      
        $this->load->view("rtk/template", $data);
    }

    //Update the FCDRR Online
    public function update_lab_commodity_orders() {
        $rtk = new Rtk_Management();
        $order_id = $_POST['order_id'];
        $detail_id = $_POST['detail_id'];
        $district_id = $_POST['district'];
        $facility_code = $_POST['facility_code'];
        $drug_id = $_POST['commodity_id'];
        $unit_of_issue = $_POST['unit_of_issue'];
        $b_balance = $_POST['b_balance'];
        $q_received = $_POST['q_received'];
        $q_used = $_POST['q_used'];
        $tests_done = $_POST['tests_done'];
        $losses = $_POST['losses'];
        $pos_adj = $_POST['pos_adj'];
        $neg_adj = $_POST['neg_adj'];
        $physical_count = $_POST['physical_count'];
        $q_expiring = $_POST['q_expiring'];
        $days_out_of_stock = $_POST['days_out_of_stock'];
        $q_requested = $_POST['q_requested'];
        $commodity_count = count($drug_id);
        $detail_count = count($detail_id);

        $vct = $_POST['vct'];
        $pitc = $_POST['pitc'];
        $pmtct = $_POST['pmtct'];
        $b_screening = $_POST['blood_screening'];
        $other = $_POST['other2'];
        $specification = $_POST['specification'];
        $rdt_under_tests = $_POST['rdt_under_tests'];
        $rdt_under_pos = $_POST['rdt_under_positive'];
        $rdt_btwn_tests = $_POST['rdt_to_tests'];
        $rdt_btwn_pos = $_POST['rdt_to_positive'];
        $rdt_over_tests = $_POST['rdt_over_tests'];
        $rdt_over_pos = $_POST['rdt_over_positive'];
        $micro_under_tests = $_POST['micro_under_tests'];
        $micro_under_pos = $_POST['micro_under_positive'];
        $micro_btwn_tests = $_POST['micro_to_tests'];
        $micro_btwn_pos = $_POST['micro_to_positive'];
        $micro_over_tests = $_POST['micro_over_tests'];
        $micro_over_pos = $_POST['micro_over_positive'];
        date_default_timezone_set('EUROPE/Moscow');
        $beg_date = date('y-m-d', strtotime($_POST['begin_date']));
        $end_date = date('y-m-d', strtotime($_POST['end_date']));
        $explanation = $_POST['explanation'];
        $compiled_by = $_POST['compiled_by'];

        $moh_642 = $_POST['moh_642'];
        $moh_643 = $_POST['moh_643'];

        $myobj = Doctrine::getTable('Lab_Commodity_Orders')->find($order_id);

        $myobj->vct = $vct;
        $myobj->pitc = $pitc;
        $myobj->pmtct = $pmtct;
        $myobj->b_screening = $b_screening;
        $myobj->other = $other;
        $myobj->specification = $specification;
        $myobj->rdt_under_tests = $rdt_under_tests;
        $myobj->rdt_under_pos = $rdt_under_pos;
        $myobj->rdt_btwn_tests = $rdt_btwn_tests;
        $myobj->rdt_btwn_pos = $rdt_btwn_pos;
        $myobj->rdt_over_tests = $rdt_over_tests;
        $myobj->rdt_over_pos = $rdt_over_pos;
        $myobj->micro_under_tests = $micro_under_tests;
        $myobj->micro_under_pos = $micro_under_pos;
        $myobj->micro_btwn_tests = $micro_btwn_tests;
        $myobj->micro_btwn_pos = $micro_btwn_pos;
        $myobj->micro_over_tests = $micro_over_tests;
        $myobj->micro_over_pos = $micro_over_pos;
        $myobj->beg_date = $beg_date;
        $myobj->end_date = $end_date;
        $myobj->explanation = $explanation;
        $myobj->compiled_by = $compiled_by;
        $myobj->moh_642 = $moh_642;
        $myobj->moh_643 = $moh_643;
        $myobj->save();
        $object_id = $myobj->get('id');
        $this->logData('14', $object_id);
        $q = "select id from lab_commodity_details where order_id = $order_id";
        $res = $this->db->query($q);
        $ids = $res->result_array();  

        for ($i = 0; $i < $detail_count; $i++) {

            $id = $ids[$i]['id'];           
            $sql = "UPDATE `lab_commodity_details` SET `beginning_bal`=$b_balance[$i],
            `q_received`='$q_received[$i]',`q_used`=$q_used[$i],`no_of_tests_done`=$tests_done[$i],`losses`=$losses[$i],
            `positive_adj`=$pos_adj[$i],`negative_adj`=$neg_adj[$i],`closing_stock`=$physical_count[$i],
            `q_expiring`=$q_expiring[$i],`days_out_of_stock`=$days_out_of_stock[$i],`q_requested`=$q_requested[$i] WHERE id= $id ";
            $this->db->query($sql);
        }

        redirect('rtk_management/scmlt_orders');
    }

    //VIew FCDRR Report
     public function lab_order_details($order_id, $msg = NULL) {
        $delivery = $this->uri->segment(3);
        $district = $this->session->userdata('district_id');
        $data['title'] = "Lab Commodity Order Details";       
        $data['order_id'] = $order_id;
        $data['content_view'] = "rtk/rtk/scmlt/fcdrr_report";
        $data['banner_text'] = "Lab Commodity Order Details";

        $data['lab_categories'] = Lab_Commodity_Categories::get_all();
        $data['detail_list'] = Lab_Commodity_Details::get_order($order_id);

        $result = $this->db->query('SELECT * 
            FROM lab_commodity_details, counties, facilities, districts, lab_commodity_orders, lab_commodity_categories, lab_commodities
            WHERE lab_commodity_details.facility_code = facilities.facility_code
            AND counties.id = districts.county
            AND facilities.facility_code = lab_commodity_orders.facility_code
            AND lab_commodity_details.commodity_id = lab_commodities.id
            AND lab_commodity_categories.id = lab_commodities.category
            AND facilities.district = districts.id
            AND lab_commodity_details.order_id = lab_commodity_orders.id
            AND lab_commodity_orders.id = ' . $order_id . '');
        $data['all_details'] = $result->result_array();
        $this->load->view("rtk/template", $data);
    }

    //Print the FCDRR
    public function get_lab_report($order_no, $report_type) {
        $table_head = '<style>table.data-table {border: 1px solid #DDD;margin: 10px auto;border-spacing: 0px;}
        table.data-table th {border: none;color: #036;text-align: center;background-color: #F5F5F5;border: 1px solid #DDD;border-top: none;max-width: 450px;}
        table.data-table td, table th {padding: 4px;}
        table.data-table td {border: none;border-left: 1px solid #DDD;border-right: 1px solid #DDD;height: 30px;margin: 0px;border-bottom: 1px solid #DDD;}
        .col5{background:#D8D8D8;}</style></table>
        <table class="data-table" width="100%">
            <thead>
                <tr>
                    <th><strong>Category</strong></th>
                    <th><strong>Description</strong></th>
                    <th><strong>Unit of Issue</strong></th>
                    <th><strong>Beginning Balance</strong></th>
                    <th><strong>Quantity Received</strong></th>
                    <th><strong>Quantity Used</strong></th>
                    <th><strong>Number of Tests Done</strong></th>
                    <th><strong>Losses</strong></th>
                    <th><strong>Positive Adjustments</strong></th>
                    <th><strong>Negative Adjustments</strong></th>
                    <th><strong>Closing Stock</strong></th>
                    <th><strong>Quantity Expiring in 6 Months</strong></th>
                    <th><strong>Days Out of Stock</strong></th>
                    <th><strong>Quantity Requested</strong></th>
                </tr>
            </thead>
            <tbody>';
                $detail_list = Lab_Commodity_Details::get_order($order_no);
                $table_body = '';
                foreach ($detail_list as $detail) {
                    $table_body .= '<tr><td>' . $detail['category_name'] . '</td>';
                    $table_body .= '<td>' . $detail['commodity_name'] . '</td>';
                    $table_body .= '<td>' . $detail['unit_of_issue'] . '</td>';
                    $table_body .= '<td>' . $detail['beginning_bal'] . '</td>';
                    $table_body .= '<td>' . $detail['q_received'] . '</td>';
                    $table_body .= '<td>' . $detail['q_used'] . '</td>';
                    $table_body .= '<td>' . $detail['no_of_tests_done'] . '</td>';
                    $table_body .= '<td>' . $detail['losses'] . '</td>';
                    $table_body .= '<td>' . $detail['positive_adj'] . '</td>';
                    $table_body .= '<td>' . $detail['negative_adj'] . '</td>';
                    $table_body .= '<td>' . $detail['closing_stock'] . '</td>';
                    $table_body .= '<td>' . $detail['q_expiring'] . '</td>';
                    $table_body .= '<td>' . $detail['days_out_of_stock'] . '</td>';
                    $table_body .= '<td>' . $detail['q_requested'] . '</td></tr>';
                }
                $table_foot = '</tbody></table>';
            $report_name = "Lab Commodities Order " . $order_no . " Details";
            $title = "Lab Commodities Order " . $order_no . " Details";
            $html_data = $table_head . $table_body . $table_foot;

            switch ($report_type) {
                case 'excel' :
                $this->_generate_lab_report_excel($report_name, $title, $html_data);
                break;
                case 'pdf' :
                $this->_generate_lab_report_pdf($report_name, $title, $html_data);
                break;
            }
        }


        //Generate the FCDRR PDF

        function _generate_lab_report_pdf($report_name, $title, $html_data) {

            /*         * ******************************************setting the report title******************** */

            $html_title = "<div ALIGN=CENTER><img src='" . base_url() . "assets/img/coat_of_arms-resized.png' height='70' width='70'style='vertical-align: top;' > </img></div>
            <div style='text-align:center; font-size: 14px;display: block;font-weight: bold;'>$title</div>
            <div style='text-align:center; font-family: arial,helvetica,clean,sans-serif;display: block; font-weight: bold; font-size: 14px;'>
                Ministry of Health</div>
                <div style='text-align:center; font-family: arial,helvetica,clean,sans-serif;display: block; font-weight: bold;display: block; font-size: 13px;'>Health Commodities Management Platform</div><hr />";

            /*         * ********************************initializing the report ********************* */
            $this->load->library('mpdf');
            $this->mpdf = new mPDF('', 'A4-L', 0, '', 15, 15, 16, 16, 9, 9, '');
            $this->mpdf->SetTitle($title);
            $this->mpdf->WriteHTML($html_title);
            $this->mpdf->simpleTables = true;
            $this->mpdf->WriteHTML('<br/>');
            $this->mpdf->WriteHTML($html_data);
            $report_name = $report_name . ".pdf";
            $this->mpdf->Output($report_name, 'D');
        }

        //Generate the FCDRR Excel
        function _generate_lab_report_excel($report_name, $title, $html_data) {
            $data = $html_data;
            $filename = $report_name;
            header("Content-type: application/excel");
            header("Content-Disposition: attachment; filename=$filename.xls");
            echo "$data";
        }













    //Shared Functions

    function _allocation($zone = NULL, $county = NULL, $district = NULL, $facility = NULL, $sincedate = NULL, $enddate = NULL) {
            // function to filter allocation based on multiple parameter
            // zone, county,district, sincedate,
        $conditions = '';
        $conditions = (isset($zone)) ? " AND facilities.Zone = 'Zone $zone'" : '';
        $conditions = (isset($county)) ? $conditions . " AND counties.id = $county" : $conditions . ' ';
        $conditions = (isset($district)) ? $conditions . " AND districts.id = $district" : $conditions . ' ';
        $conditions = (isset($facility)) ? $conditions . " AND facilities.facility_code = $facility" : $conditions . ' ';
        $conditions = (isset($sincedate)) ? $conditions . " AND lab_commodity_details.allocated_date >= $sincedate" : $conditions . ' ';
        $conditions = (isset($enddate)) ? $conditions . " AND lab_commodity_details.allocated_date <= $enddate" : $conditions . ' ';

        $sql = "select facilities.facility_name,facilities.facility_code,facilities.Zone, facilities.contactperson,facilities.cellphone, lab_commodity_details.commodity_id,
        lab_commodity_details.allocated,lab_commodity_details.allocated_date,lab_commodity_orders.order_date,lab_commodities.commodity_name,facility_amc.amc,lab_commodity_details.closing_stock,lab_commodity_details.q_requested
        from facilities, lab_commodity_orders,lab_commodity_details, counties,districts,lab_commodities,lab_commodity_categories,facility_amc
        WHERE facilities.facility_code = lab_commodity_orders.facility_code
        AND lab_commodity_categories.id = 1
        AND lab_commodity_categories.id = lab_commodities.category
        AND counties.id = districts.county
        AND facilities.district = districts.id
        AND facilities.rtk_enabled = 1
        and lab_commodities.id = lab_commodity_details.commodity_id
        and lab_commodities.id = facility_amc.commodity_id
        and facilities.facility_code = facility_amc.facility_code
        AND lab_commodity_orders.id = lab_commodity_details.order_id
        AND lab_commodity_details.commodity_id between 1 AND 3
        $conditions
        GROUP BY facilities.facility_code, lab_commodity_details.commodity_id";
        $res = $this->db->query($sql);
        $returnable = $res->result_array();
        return $returnable;
            #$nonexistent = "AND lab_commodity_orders.order_date BETWEEN '2014-04-01' AND '2014-04-30'";
    }


        //Switch Districts
    public function switch_district($new_dist = null, $switched_as, $month = NULL, $redirect_url = NULL, $newcounty = null, $switched_from = null) {    
        if ($new_dist == 0) {
            $new_dist = null;
        }
        if ($month == 0) {
            $month = null;
        }
        if ($redirect_url == 0) {
            $redirect_url = null;
        }
        if ($newcounty == 0) {
            $newcounty = null;
        }
        if ($redirect_url == NULL) {
            $redirect_url = 'home_controller';
        }           

        if (!isset($newcounty)) {
            $newcounty = $this->session->userdata('county_id');
        }

        $session_data = array("session_id" => $this->session->userdata('session_id'),
         "ip_address" => $this->session->userdata('ip_address'),
         "user_agent" => $this->session->userdata('user_agent'),
         "last_activity" => $this->session->userdata('last_activity'),
         "county_id" => $newcounty,
         "phone_no" => $this->session->userdata('phone_no'),
         "user_email" => $this->session->userdata('user_email'),
         "user_db_id" => $this->session->userdata('user_db_id'),
         "full_name" => $this->session->userdata('full_name'),
         "user_id" => $this->session->userdata('user_id'),
         "user_indicator" => $switched_as,
         "names" => $this->session->userdata('names'),
         "inames" => $this->session->userdata('inames'),
         "identity" => $this->session->userdata('identity'),
         "news" => $this->session->userdata('news'),
         "district_id" => $new_dist,
         "drawing_rights" => $this->session->userdata('drawing_rights'),
         "switched_as" => $switched_as,
         "Month" => $month,
         'switched_from' => $switched_from);


        $this->session->set_userdata($session_data);
        redirect($redirect_url);
     }
     public function summary_tab_display($county, $year, $month) {
        // county may be 1 for Nairobi, 5 for busia or 31 for Nakuru
        $htmltable = '';

        $countyname = counties::get_county_name($county);
        $countyname = $countyname[0]['county'];
        $ish = $this->rtk_summary_county($county, $year, $month);            
        $htmltable .= '<tr>
        <td rowspan ="' . $ish['districts'] . '">' . $countyname . '';            
            $total_punctual = 0;
            $county_percentage = 0;

            foreach ($ish['district_summary'] as $vals) {
                $early = $vals['reported'] - $vals['late_reports'];
                $total_punctual += $early;
                $htmltable .= ' 
            </td><td>' . $vals['district'].'</td>
            <td>' . $vals['total_facilities'] . '</td>
            <td>' . $early . '</td>
            <td>' . $vals['late_reports'] . '</td>
            <td>' . $vals['nonreported'] . '</td>
            <td>' . $vals['reported_percentage'] . '%</td></tr>';
               
              }
              $county_percentage = ($total_punctual + $ish['late_reports']) / $ish['facilities'] * 100;
              $county_percentage = number_format($county_percentage, 0);

              $htmltable .= '<tr style="background: #E9E9E3; border-top: solid 1px #ccc;">
              <td>Totals</td>
              <td>' . $ish['districts'] . ' Sub-Counties</td>
              <td>' . $ish['facilities'] . '</td>
              <td>' . $total_punctual . '</td>
              <td>' . $ish['late_reports'] . '</td>
              <td>' . $ish['nonreported'] . '</td>
              <td>' . $county_percentage . '%</td>

          </tr>';
          echo '
          <table class="data-table">
              <thead><tr>
                  <th>County</th>
                  <th>Sub-County</th>
                  <th>No of facilities</th>
                  <th>No reports before 10th</th>
                  <th>No of late reports (10th-12th)</th>
                  <th>No of non reporting facilities</th>
                  <th>Overall reporting rate in % (no of reports submitted/expected no of reports)</th>
              </tr></thead>
              ' . $htmltable . '

          </table>';
      }
      public function rtk_summary_county($county, $year, $month) {
        date_default_timezone_set('EUROPE/moscow');
        $county_summary = array();
        $county_summary['districts'] = 0;
        $county_summary['facilities'] = 0;
        $county_summary['reported'] = 0;
        $county_summary['reported_percentage'] = 0;
        $county_summary['nonreported'] = 0;
        $county_summary['nonreported_percentage'] = 0;
        $county_summary['late_reports'] = 0;
        $county_summary['late_reports_percentage'] = 0;
        $county_summary['district_summary'] = array();
        /*
         * countyname,numberofdistricts,numberoffacilities,reported,nonreported,late
         */
        $q = 'SELECT * 
        FROM counties, districts
        WHERE counties.id = districts.county
        AND counties.id = ' . $county . '';
        $q_res = $this->db->query($q);
        $districts_num = $q_res->num_rows();
        foreach ($q_res->result_array() as $districts) {
            $dist_id = $districts['id'];
            $dist = $districts['district'];

            //$county_summary['district_summary']['district'] = $dist;
            //$county_summary['district_summary']['district_id'] = $dist_id;

            $district_summary = $this->rtk_summary_district($districts['id'], $year, $month);

            $county_summary['districts'] += 1;
            $county_summary['facilities'] += $district_summary['total_facilities'];
            $county_summary['reported'] += $district_summary['reported'];
            $county_summary['reported_percentage'] += $district_summary['reported_percentage'];
            $county_summary['nonreported'] += $district_summary['nonreported'];
            $county_summary['nonreported_percentage'] += $district_summary['nonreported_percentage'];
            $county_summary['punctual_reports'] = 1;
            $county_summary['late_reports'] += $district_summary['late_reports'];

            $county_summary['late_reports_percentage'] += $district_summary['late_reports_percentage'];
            array_push($county_summary['district_summary'], $district_summary);
        }

        $county_summary['reported_percentage'] = ($county_summary['reported_percentage'] / $county_summary['districts']);
        $county_summary['reported_percentage'] = number_format($county_summary['reported_percentage'], 0);

        $sortArray = array();
        foreach ($county_summary['district_summary'] as $person) {
            foreach ($person as $key => $value) {
                if (!isset($sortArray[$key])) {
                    $sortArray[$key] = array();
                }
                $sortArray[$key][] = $value;
            }
        }

        $orderby = "reported_percentage";

        array_multisort($sortArray[$orderby], SORT_DESC, $county_summary['district_summary']);
        return $county_summary;
    }
    public function rtk_summary_district($district, $year, $month) {
        $distname = districts::get_district_name($district);
        $districtname = $distname[0]['district'];
        $district_id = $district;
        $returnable = array();
        $nonreported;
        $reported_percentage;
        $late_percentage;

        // Sets the timezone and date variables for last day of previous month and this month
        date_default_timezone_set('EUROPE/moscow');
        $month = $month + 1;
        $prev_month = $month - 1;
        $last_day_current_month = date('Y-m-d', mktime(0, 0, 0, $month, 0, $year));
        $first_day_current_month = date('Y-m-', mktime(0, 0, 0, $month, 0, $year));
        $first_day_current_month .= '1';
        $lastday_thismonth = date('Y-m-d', strtotime("last day of this month"));
        $month -= 1;
        $day10 = $year . '-' . $month . '-10';
        $day11 = $year . '-' . $month . '-11';
        $day12 = $year . '-' . $month . '-12';
        $late_reporting = 0;
        $text_month = date('F', strtotime($day10));

        $q = 'SELECT * 
        FROM facilities, districts, counties
        WHERE facilities.district = districts.id
        AND districts.county = counties.id
        AND districts.id = '.$district.' 
        AND facilities.rtk_enabled =1
        ORDER BY  `facilities`.`facility_name` ASC ';
        $q_res = $this->db->query($q);
        $total_reporting_facilities = $q_res->num_rows();
        $q = "SELECT DISTINCT lab_commodity_orders.facility_code, lab_commodity_orders.id,lab_commodity_orders.order_date
        FROM lab_commodity_orders, districts, counties
        WHERE districts.id = lab_commodity_orders.district_id
        AND districts.county = counties.id
        AND districts.id = $district
        AND lab_commodity_orders.order_date
        BETWEEN '$first_day_current_month'
        AND '$last_day_current_month'";
        $q_res1 = $this->db->query($q);
        $total_reported_facilities = $q_res1->num_rows();

        foreach ($q_res1->result_array() as $vals) {
            //            echo "<pre>";var_dump($vals);echo "</pre>";
            if ($vals['order_date'] == $day10 || $vals['order_date'] == $day11 || $vals['order_date'] == $day12) {
                $late_reporting += 1;
                //                echo "<pre>";var_dump($vals);echo "</pre>";
            }
        }

        $nonreported = $total_reporting_facilities - $total_reported_facilities;

        if ($total_reporting_facilities == 0) {
            $non_reported_percentage = 0;
        } else {
            $non_reported_percentage = $nonreported / $total_reporting_facilities * 100;
        }

        $non_reported_percentage = number_format($non_reported_percentage, 0);

        if ($total_reporting_facilities == 0) {
            $reported_percentage = 0;
        } else {
            $reported_percentage = $total_reported_facilities / $total_reporting_facilities * 100;
        }

        $reported_percentage = number_format($reported_percentage, 0);

        if ($total_reporting_facilities == 0) {
            $late_percentage = 0;
        } else {
            $late_percentage = $late_reporting / $total_reporting_facilities * 100;
        }


        $late_percentage = number_format($late_percentage, 0);
        if ($total_reported_facilities > $total_reporting_facilities) {
            $reported_percentage = 100;
            $nonreported = 0;
            $total_reported_facilities = $total_reporting_facilities;
        }
        if ($late_reporting > $total_reporting_facilities) {
            $late_reporting = $total_reporting_facilities;
            $late_percentage = $reported_percentage;
        }
        $returnable = array('Month' => $text_month, 'Year' => $year, 'district' => $districtname, 'district_id' => $district_id, 'total_facilities' => $total_reporting_facilities, 'reported' => $total_reported_facilities, 'reported_percentage' => $reported_percentage, 'nonreported' => $nonreported, 'nonreported_percentage' => $non_reported_percentage, 'late_reports' => $late_reporting, 'late_reports_percentage' => $late_percentage);
        return $returnable;
    }

    //Logging Function
    public function logData($reference, $object) {
            $timestamp = time();
            $user_id = $this->session->userdata('user_id');
            $sql = "INSERT INTO `rtk_logs`(`id`, `user_id`, `reference`,`reference_object`,`timestamp`) VALUES (NULL,'$user_id','$reference','$object','$timestamp')";
            $this->db->query($sql);
    }

    //Update the Average Monthly Consumption
    private function update_amc($mfl) {
        $last_update = time();
        $amc = 0;
        for ($commodity_id = 1; $commodity_id <= 6; $commodity_id++) {
            $amc = $this->_facility_amc($mfl, $commodity_id);
            $sql = "update facility_amc set amc = '$amc', last_update = '$last_update' where facility_code = '$mfl' and commodity_id='$commodity_id'";
            $res = $this->db->query($sql);
        }
    }


    //Facility Amc
    public function _facility_amc($mfl_code, $commodity = null) {
        $three_months_ago = date("Y-m-", strtotime("-3 Month "));
        $three_months_ago .='1';
        $end_date = date("Y-m-", strtotime("-1 Month "));
        $end_date .='31';
        //echo "Three months ago = $three_months_ago and End Date =$end_date ";die();
        $q = "SELECT avg(lab_commodity_details.q_used) as avg_used
        FROM  lab_commodity_details,lab_commodity_orders
        WHERE lab_commodity_orders.id =  lab_commodity_details.order_id
        AND lab_commodity_details.facility_code =  $mfl_code
        AND lab_commodity_orders.order_date BETWEEN '$three_months_ago' AND '$end_date'";
        
        if (isset($commodity)) {
            $q.=" AND lab_commodity_details.commodity_id = $commodity";
        } else {
            $q.=" AND lab_commodity_details.commodity_id = 1";
        }

        $res = $this->db->query($q);
        $result = $res->result_array();
        $result = $result[0]['avg_used'];
        $result = number_format($result, 0);
        return $result;
    }


    //Update the Number of Reports Online
    function _update_reports_count($state,$county,$district){ 
            $month = date('mY',time());             
            if($state=="add"){
                $sql = "update rtk_county_percentage set reported = (reported + 1) where month='$month' and county_id = '$county'";
                $sql1 = "update rtk_district_percentage set reported = (reported + 1) where month='$month' and district_id = '$district'";
            }elseif ($state=="remove") {
                $sql = "update rtk_county_percentage set reported = (reported - 1) where month='$month' and county_id = '$county'";
                $sql1 = "update rtk_district_percentage set reported = (reported - 1) where month='$month' and district_id = '$district'";                
            }
            $this->db->query($sql);
            $this->db->query($sql1);
            $q = "update rtk_district_percentage set percentage = round(((reported/facilities)*100),0) where month='$month' and district_id = '$district'";                
            $q1 = "update rtk_county_percentage set percentage = round(((reported/facilities)*100),0) where month='$month' and county_id = '$county'";
            $this->db->query($q);
            $this->db->query($q1);
        } 




}
?>