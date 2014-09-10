<div id="tabs">
	<a href="<?php echo base_url().'rtk_management/rtk_manager'; ?>" data-tab="1" class="tab">National Trend</a>
	<a href="<?php echo base_url().'rtk_management/rtk_manager_users'; ?>" data-tab="2" class="tab">Users</a>
	<a href="<?php echo base_url().'rtk_management/rtk_manager_messages'; ?>" data-tab="1" class="tab">Messages</a>
	<a href="<?php echo base_url().'rtk_management/rtk_manager_settings'; ?>" data-tab="2" class="tab">Settings</a>
</div>

<?php

?>
<style type="text/css">
	.tab {
		float: left;
		display: block;
		padding: 10px 20px;
		text-decoration: none;
		border-radius: 5px 5px 0 0;
		background: #F9F9F9;
		color: #777;
	}
	#tabs a{		
		text-decoration: none;
		font-style: normal;		
	}
	#tabs a:hover{
		border-radius: 5px 5px 0 0;
		background: #CCCCCC;
	}

</style>
