<style type="text/css">
	
	.panel-body,span:hover,.status_item:hover
	{ 
		
		cursor: pointer !important; 
	}
	
	.panel {
		
		border-radius: 0;
		
	}
	.panel-body {
		
		padding: 8px;
	}
	#addModal .modal-dialog {
		
		width: 54%;
	}
	
</style>

<div class="container-fluid">
	<div class="page_content">
		<div class="" style="width:65%;margin:auto;">
				<div class="row ">
					<div class="col-md-3">
						
					</div>
					<?php $x = array();
					foreach ($counts as $key) {
						$x[] = $key['count'];
					}
					?>
					<div class="col-md-3">
						<div class="panel panel-default">
							<div class="panel-body" id="active">
								<div class="stat_item color_d">
									<span class="glyphicon glyphicon-user"></span>
									<span><?php echo($x[1]);?>
										Active</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="panel panel-default">
							<div class="panel-body" id="inactive">
								<div class="stat_item color_g">
									<span class="glyphicon glyphicon-user"></span>
									<span><?php echo($x[0]); ?>
										Inactive</span>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-3">
						
					</div>
				</div>
			</div>
		<div class="container-fluid">
			
			<div class="row">

				<div class="col-md-2" style="padding-left: 0;">
					<button class="btn btn-primary add" data-toggle="modal" data-target="#addModal" id="add_new">
						<span class="glyphicon glyphicon-plus"></span>Add User
					</button>
				</div>
				<div class="col-md-10 dt" style="border: 1px solid #ddd;padding-top: 1%; " id="test">

					<table  class="table table-hover table-bordered table-update" id="datatable"  >
						<thead style="background-color: white">
							<tr>
								<th>First name</th>
								<th>Last name</th>
								<th>Email </th>
								<th>Phone No</th>
								<th>Sub-County</th>
								<th>Health Facility</th>
								<th>User Type</th>
								<th>Status</th>
								<th>Action</th>

							</tr>
						</thead>

						<tbody>

							<?php
							foreach ($listing as $list ) {
							?>
							<tr class="edit_tr" >
								<td class="fname" ><?php echo $list['fname']; ?></td>
								<td class="lname"><?php echo $list['lname']; ?>	</td>
								<td class="email" data-attr="<?php echo $list['user_id']; ?>"><?php echo $list['email'];?></td>
								<td class="phone"><?php echo $list['telephone']; ?></td>
								<td class="district" data-attr="<?php echo $list['district_id']; ?>"><?php echo $list['district']; ?></td>
								<td class="facility_name" data-attr="<?php echo $list['facility_code']; ?>"><?php echo $list['facility_name']; ?></td>
								<td class="level" data-attr="<?php echo $list['level_id']; ?>"><?php echo $list['level']; ?></td>
								<td >
								<?php
									if ($list['status']==1) {
								?>
								<div class="status_item color_d" data-attr="true">
									<span>Active</span>
								</div>
								<?php }else{ ?>

								<div class=" status_item color_g" data-attr="false">
									<span>Deactivated</span>
								</div> <?php } ?> </td>
								<td>
								<button class="btn btn-primary btn-xs edit " data-toggle="modal" data-target="#myModal" id="<?php echo $list['user_id']; ?>" data-target="#">
									<span class="glyphicon glyphicon-edit"></span>Edit
								</button></td>

							</tr>
							<?php } ?>
						</tbody>
					</table>

				</div>

			</div>
		</div>
	</div>
</div>
</div>
<!-- Modal add user -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" id="myform">
	<div class="modal-dialog">
		<div class="modal-content">
			
			<div class="modal-header" style="padding-bottom:2px;background: #27ae60;color: white">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id="myModalLabel" style="text-align: center;line-height: 1">Edit User</h4>
			</div>
			<div class="row" style="margin:auto" id="error_msg">
				<div class=" col-md-12">
					<div class="form-group">

					</div>
				</div>

			</div>
			<div class="modal-body" style="padding-top:0">
				<div class="row" style="margin:auto">
					<div class="col-md-12 ">
						<form role="form">

							<fieldset>
								<legend style="font-size:1.5em">
									User details
								</legend>
								<div class="row" >

									<div class="col-md-6">
										<div class="form-group">
											<input type="text" required="required" name="first_name" id="first_name" class="form-control " placeholder="First Name" >
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<input type="text" name="last_name" required="required" id="last_name" class="form-control " placeholder="Last Name" >
										</div>
									</div>
								</div>

								<div class="row">
									<div class=" col-md-6">
										<div class="form-group">
											<input type="telephone" name="telephone" required="required" id="telephone" class="form-control " placeholder="telephone eg, 254" tabindex="5">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<input type="email" name="email" id="email" required="required" class="form-control " placeholder="email@domain.com" tabindex="6">
										</div>
									</div>
								</div>
								<div class="row">
									<div class=" col-md-6">
										<div class="form-group">
											<input type="email" name="username" id="username" required="required" class="form-control " placeholder="email@domain.com" tabindex="5" readonly>
										</div>
									</div>
									<div class="col-md-6">

									</div>
								</div>
							</fieldset>
							<fieldset>
								<legend style="font-size:1.5em">
									Other details
								</legend>
								<div class="row" >
									<?php

									$identifier = $this -> session -> userdata('user_indicator');

									if ($identifier=='district') {
									?>

									<div class="col-md-6">
										<div class="form-group">

											<select class="form-control " id="facility_id" required="required">
												<option value=''>Select Facility</option>

												<?php
												foreach ($facilities as $facility) :
													$id = $facility ['facility_code'];
													$facility_name = $facility ['facility_name'];
													echo "<option value='$id'>$facility_name</option>";
												endforeach;
												?>
											</select>

										</div>
									</div>
									<div class="row" style="margin:auto">
										<div class=" col-md-6">
											<div class="form-group">
												<select class="form-control " id="user_type" name="user_type" required="required">
													<option value=''>Select User type</option>
													<?php
													foreach ($user_types as $user_types) :
														$id = $user_types -> id;
														$type_name = $user_types -> level;
														echo "<option value='$id'>$type_name</option>";
													endforeach;
													?>
												</select>
											</div>
										</div>
										<div class="col-md-6">

										</div>
									</div>

									<?php }elseif ($identifier=='county') { ?>
									<div class="col-md-6">
										<div class="form-group">

											<select class="form-control " id="district_name" required="required">
												<option value=''>Select Sub-County</option>

												<?php
												foreach ($district_data as $district_) :
													$district_id = $district_ ['id'];
													$district_name = $district_ ['district'];
													echo "<option value='$district_id'>$district_name</option>";
												endforeach;
												?>
											</select>

										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<select class="form-control " id="facility_id" required="required">
												<option value="">Select Facility</option>
												
											</select>
										</div>
									</div>
								</div>
								<div class="row" >
									<div class=" col-md-6">
										<div class="form-group">
											<select class="form-control " id="user_type" name="user_type" required="required">
											</select>
										</div>
									</div>
									<div class="col-md-6">
									</div>
								</div>
								<?php }elseif ($identifier=='facility_admin') {
											//code if facility admin
											
											
										}
								?>
								<div class="row">
									<div class="col-md-6">
									
										</div>
										<div class="col-md-6">
									
										</div>
								</div>
								<div class="row" style="margin:auto" id="processing">
									<div class=" col-md-12">
										<div class="form-group">
										</div>
									</div>
								</div>
							</fieldset>

						</form>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default" data-dismiss="modal">
					Close
				</button>
				
				<button class="btn btn-primary" id="create_new">
					Save changes
				</button>
			</div>
		</div>
	</div>
</div><!-- end Modal new user -->

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="padding-bottom:2px;background: #27ae60;color: white">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id="myModalLabel" style="text-align: center;line-height: 1">Edit User</h4>
			</div>
			<div class="modal-body" style="padding-top:0">
				<div id="contents">

					<form role="form">

						<h4>User details</h4>
						
							<div class="row" >

								<div class="col-md-6">
									<label> First Name </label>
									<div class="form-group">
										<input type="text" required="required" name="fname_edit" id="fname_edit" class="form-control " placeholder="First Name" >
									</div>
								</div>
								<div class="col-md-6">
									<label> Last Name </label>
									<div class="form-group">
										<input type="text" name="lname_edit" required="required" id="lname_edit" class="form-control " placeholder="Last Name" >
									</div>
								</div>
							</div>

							<div class="row">
								<div class=" col-md-6">
									<label> Phone No </label>
									<div class="form-group">
										<input type="telephone" name="telephone_edit" required="required" id="telephone_edit" class="form-control " placeholder="telephone eg, 254" tabindex="5">
									</div>
								</div>
								<div class="col-md-6">
									<label> Email </label>
									<div class="form-group">
										<input type="email" data-id="" name="email_edit" id="email_edit" required="required" class="form-control " placeholder="email@domain.com" tabindex="6">
									</div>
								</div>
							</div>
							<div class="row">
								<div class=" col-md-6">
									<label> User Name </label>
									<div class="form-group">
										<input type="email" name="username_edit" id="username_edit" required="required" class="form-control " placeholder="email@domain.com" tabindex="5" readonly>
									</div>
								</div>
								<div class="col-md-6">

								</div>
							</div>
						
						<h4>Other details</h4>
								<div class="row" >
									<?php

									$identifier = $this -> session -> userdata('user_indicator');
									if ($identifier=='district') {
									?>

									<div class="col-md-6">
										
										<div class="form-group">

											<select class="form-control " id="facility_id_edit_district" required="required">
												<option value=''>Select Facility</option>

												<?php
												foreach ($facilities as $facility) :
													$id = $facility ['facility_code'];
													$facility_name = $facility ['facility_name'] ;
													echo "<option value='$id'>$facility_name</option>";
												endforeach;
												?>
											</select>

										</div>
									</div>
									<div class="row" style="margin:auto">
										<div class=" col-md-6">
											
											<div class="form-group">
												<select class="form-control " id="user_type_edit_district" name="user_type_edit_district" required="required">
													
													
												</select>
												<input type="hidden" name="district_name_edit" class="" id="district_name_edit" >
											</div>
										</div>
										<div class="col-md-6">
									<div class="onoffswitch">
									    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" checked>
									    <label class="onoffswitch-label" for="myonoffswitch">
									        <div value="1" class="onoffswitch-inner"></div>
									        <div class="onoffswitch-switch"></div>
									    </label>
									</div>
										</div>
									</div>

									<?php }elseif ($identifier=='county') { ?>
										
									<div class="col-md-6">
										
										<div class="form-group">
							
											<select class="form-control " id="district_name_edit" required="required">
												<option value=''>Select Sub-County</option>
												<?php
												foreach ($district_data as $district) :
													$d_id = $district ['id'];
													$d_name = $district ['district'];
													echo "<option value='$d_id'>$d_name</option>";
												endforeach;
												?>
											</select>

										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<select class="form-control " id="facility_id_edit" required="required">
												
											</select>
										</div>
									</div>
								</div>
								<div class="row" >
									<div class=" col-md-6">
										<div class="form-group">
											<select class="form-control " id="user_type_edit_district" name="user_type_edit_district" required="required">
												
											</select>
										</div>
									</div>
									<div class="col-md-6">
									<div class="onoffswitch">
									    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" >
									    <label class="onoffswitch-label" for="myonoffswitch">
									        <div  class="onoffswitch-inner"></div>
									        <div  class="onoffswitch-switch"></div>
									    </label>
									</div>
									</div>
								</div>

								<?php }elseif ($identifier=='facility_admin') {
									//code if facility admin
									}
								?>

								<div class="row" style="margin:auto" id="process">
									<div class=" col-md-12">
										<div class="form-group">

										</div>
									</div>

								</div>

							</form>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">
					Close
				</button>
				<button type="button" class="btn btn-primary edit_user">
					Save changes
				</button>
			</div>
		</div>
	</div>
</div><!-- end Modal edit user -->
<script>
      $(document).ready(function () {
	$('.dataTables_filter label input').addClass('form-control');
	$('.dataTables_length label select').addClass('form-control');
$('#datatable').dataTable( {
     "sDom": "T lfrtip",
       "sScrollY": "320px",   
                    "sPaginationType": "bootstrap",
                    "oLanguage": {
                        "sLengthMenu": "_MENU_ Records per page",
                        "sInfo": "Showing _START_ to _END_ of _TOTAL_ records",
                    },
            "oTableTools": {
                 "aButtons": [
        "copy",
        "print",
        {
          "sExtends":    "collection",
					"sButtonText": 'Save',
					"aButtons":    [ "csv", "xls", "pdf" ]
        }
      ],
      "sSwfPath": "<?php echo base_url(); ?>assets/datatable/media/swf/copy_csv_xls_pdf.swf"
    }
    
  } ); 
  $('div.dataTables_filter input').addClass('form-control search');
  $('div.dataTables_length select').addClass('form-control');
  //populate facilities to drop down depending on district selected

$("#district_name").change(function() {
    var option_value=$(this).val();
    
    if(option_value=='NULL'){
    $("#facility_name").hide('slow'); 
    }
    else{
var drop_down='';
 var hcmp_facility_api = "<?php echo base_url(); ?>reports/get_facility_json_data/"+$("#district_name").val();
  $.getJSON( hcmp_facility_api ,function( json ) {
     $("#facility_id").html('<option value="NULL" selected="selected">Select Facility</option>');
      $.each(json, function( key, val ) {
        drop_down +="<option value='"+json[key]["facility_code"]+"'>"+json[key]["facility_name"]+"</option>"; 
      });
      $("#facility_id").append(drop_down);
    });
    $("#facility_id").show('slow');   
    }
    }); 
    
    
    $("#district_name_edit").change(function() {
    var option_value=$(this).val();
    
    if(option_value=='NULL'){
    $("#facility_name_edit").hide('slow'); 
    }
    else{
var drop_down='';
 var hcmp_facility_api = "<?php echo base_url(); ?>reports/get_facility_json_data/"+$("#district_name_edit").val();
  $.getJSON( hcmp_facility_api ,function( json ) {
     $("#facility_id_edit").html('<option value="NULL" selected="selected">Select Facility</option>');
      $.each(json, function( key, val ) {
        drop_down +="<option value='"+json[key]["facility_code"]+"'>"+json[key]["facility_name"]+"</option>"; 
      });
      $("#facility_id_edit").append(drop_down);
    });
    $("#facility_id_edit").show('slow');   
    }
    }); 
    
    //handle edits
$("#test").on('click','.edit',function() {
	//capture relevant data
var email = $(this).closest('tr').find('.email').html();
var phone = $(this).closest('tr').find('.phone').html();
var district = $(this).closest('tr').find('.district').html();
var fname = $(this).closest('tr').find('.fname').html();
var lname = $(this).closest('tr').find('.lname').html();
//populate dropdown on click and selected current 
var drop_down='';
var facility_id=$(this).closest('tr').find('.facility_name').attr('data-attr');
 var hcmp_facility_api = "<?php echo base_url(); ?>reports/get_facility_json_data/"+$(this).closest('tr').find('.district').attr('data-attr');
  $.getJSON( hcmp_facility_api ,function( json ) {
     $("#facility_id_edit").html('<option value="NULL" selected="selected">Select Facility</option>');
      $.each(json, function( key, val ) {
      	
        drop_down +="<option value='"+json[key]["facility_code"]+"'>"+json[key]["facility_name"]+"</option>"; 
      });
      $("#facility_id_edit").append(drop_down);
      
      $('#facility_id_edit').val(facility_id)
    });
   //fill inputs with relevant data
$('#email_edit').val(email)
$('#email_edit').attr('data-id',$(this).closest('tr').find('.email').attr('data-attr'))
$('#telephone_edit').val(phone)
$('#fname_edit').val(fname)
$('#lname_edit').val(lname)
$('#username_edit').val(email)

$('#user_type_edit').val($(this).closest('tr').find('.level').attr('data-attr'))
$('#district_name_edit').val($(this).closest('tr').find('.district').attr('data-attr'))



var drop_down_user='';
var type_id=$(this).closest('tr').find('.level').attr('data-attr');
 var get_type_json = "<?php echo base_url(); ?>user/get_user_type_json/";
 
  $.getJSON( get_type_json ,function( json ) {
     $("#user_type_edit_district").html('<option value="NULL" >Select User Type</option>');
      $.each(json, function( key, val ) {
      	
      	drop_down_user +="<option value='"+json[key]["id"]+"'>"+json[key]["level"]+"</option>";
      	 
      });
      
      $("#user_type_edit_district").append(drop_down_user);
      
      $('#user_type_edit_district').val(type_id)
    });
    


if($(this).closest('tr').find('.status_item').attr('data-attr')=="false"){
	$('.onoffswitch-checkbox').prop('checked', false) 	
}else if($(this).closest('tr').find('.status_item').attr('data-attr')=="true"){
	$('.onoffswitch-checkbox').prop('checked', true) 
}

if($(this).closest('tr').find('.facility_name').attr('data-attr')==""){
	$("#facility_id_edit").attr("disabled", "disabled"); 
}

$('#facility_id_edit_district').val(facility_id)

  });
  
  //make sure email==username  for edits
  $('#email_edit').keyup(function() {

  var email = $('#email_edit').val()

   $('#username_edit').val(email)

    })
    
   //Handle adding new users 
   $("#add_new").on('click',function() {

  var drop_down_user='';
var type_id=$(this).closest('tr').find('.level').attr('data-attr');
 var get_type_json = "<?php echo base_url(); ?>user/get_user_type_json/";
 
  $.getJSON( get_type_json ,function( json ) {
     $("#user_type").html('<option value="NULL" selected>Select User Type</option>');
      $.each(json, function( key, val ) {
      	
      	drop_down_user +="<option value='"+json[key]["id"]+"'>"+json[key]["level"]+"</option>";
      	 
      });
      
      $("#user_type").append(drop_down_user);
      

    })
    });
    
     //make sure email==username

$('#email').keyup(function() {

  var email = $('#email').val()

   $('#username').val(email)

    })
    
   
$("#create_new").click(function() {

      var first_name = $('#first_name').val()
      var last_name = $('#last_name').val()
      var telephone = $('#telephone').val()
      var email = $('#email').val()
      var username = $('#username').val()
      var facility_id = $('#facility_id').val()
      var district_name = $('#district_name').val()
      var user_type = $('#user_type').val()

       
      
      var div="#processing";
      var url = "<?php echo base_url()."user/addnew_user";?>";
      ajax_post_process (url,div);
           
    });

   function ajax_post_process (url,div){
    var url =url;

     //alert(url);
    // return;
     var loading_icon="<?php echo base_url().'assets/img/Preloader_4.gif' ?>";
     $.ajax({
          type: "POST",
          data:{ 'first_name': $('#first_name').val(),'last_name': $('#last_name').val(),
          'telephone': $('#telephone').val(),'email': $('#email').val(),
          'username': $('#username').val(),'facility_id': $('#facility_id').val(),
          'district_name': $('#district_name').val(),'user_type': $('#user_type').val()},
          url: url,
          beforeSend: function() {
           
            var message = confirm("Are you sure you want to proceed?");
        if (message){
            $('.modal-body').html("<img style='margin:30% 0 20% 42%;' src="+loading_icon+">");
        } else {
            return false;
        }
           
          },
          success: function(msg) {
         
        setTimeout(function () {
          	$('.modal-body').html("<div class='bg-warning' style='height:30px'>"+
							"<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>"+
							"<h3>Success!!! A new user was added to the system. Please Close to continue</h3></div>")
				
        }, 4000);
            
                  
          }
        }); 
}
$(".edit_user").click(function() {

      var div="#process";
      var url = "<?php echo base_url()."user/edit_user";?>";
      ajax_post (url,div);
           
    });

   function ajax_post (url,div){
    var url =url;

     //alert(url);
    // return;
     var loading_icon="<?php echo base_url().'assets/img/Preloader_4.gif' ?>";
     $.ajax({
          type: "POST",
          data:{ 'fname_edit': $('#fname_edit').val(),'lname_edit': $('#lname_edit').val(),
          'telephone_edit': $('#telephone_edit').val(),'email_edit': $('#email_edit').val(),
          'username_edit': $('#username_edit').val(),'facility_id_edit_district': $('#facility_id_edit_district').val(),
          'user_type_edit_district': $('#user_type_edit_district').val(),'district_name_edit': $('#district_name_edit').val(),
			'facility_id_edit': $('#facility_id_edit').val(),'status': $('.onoffswitch-checkbox').prop('checked'),'user_id':$('#email_edit').attr('data-id')},
          url: url,
          beforeSend: function() {
            //$(div).html("");
            var answer = confirm("Are you sure you want to proceed?");
        if (answer){
            $('.modal-body').html("<img style='margin:30% 0 20% 42%;' src="+loading_icon+">");
        } else {
            return false;
        }
             
            
          },
          success: function(msg) {
          //success message
          
          setTimeout(function () {
          	$('.modal-body').html("<div class='bg-warning' style='height:30px'>"+
							"<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>"+
							"<h3>Success Your records were Edited. Please Close to continue</h3></div>")
							$('.modal-footer').html("<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>")
				
        }, 4000);
        
              
          }
           
        }); 
}
			$('#myModal').on('hidden.bs.modal', function () {
				$("#datatable,.modal-content").hide().fadeIn('fast');
				 location.reload();
			})
			
			oTable = $('#datatable').dataTable();
			
			$('#active').click(function () {
				
				oTable.fnFilter('active');
			})
			
			$('#inactive').click(function () {
				
				oTable.fnFilter('deactivated');
			})
			
			
			
			
			});
    </script>