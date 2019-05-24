<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<title><?php echo config_item('site_title');?></title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		
		<!-- bootstrap -->
		<link rel="stylesheet" href="<?php echo site_url('assets/bootstrap/css/bootstrap.min.css'); ?>" />
		
		<!-- Font Awesome -->
		<link rel="stylesheet" href="<?php echo site_url('assets/font-awesome/4.7.0/css/font-awesome.min.css'); ?>">

		<!-- Ionicons -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

		<!-- DataTables -->
		<link rel="stylesheet" href="<?php echo site_url('assets/plugins/datatables/dataTables.bootstrap.css'); ?>">

		<!-- all plugin styles -->
		<link href="<?php echo site_url('assets/dist/css/AdminLTE.min.css'); ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo site_url('assets/dist/css/skins/_all-skins.min.css'); ?>" type="text/css" rel="stylesheet" />
		<link rel="stylesheet" href="<?php echo site_url("assets/plugins/iCheck/all.css"); ?>" />

        <link href="<?php echo site_url('assets/css/pnotify.custom.min.css');?>" type="text/css" rel="stylesheet" />


		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->
		<!--[if lte IE 8]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<script src="<?php echo site_url('assets/plugins/jQuery/jquery-2.2.3.min.js'); ?>"></script>

		<script src="<?php echo site_url('assets/bootstrap/js/bootstrap.min.js'); ?>"></script>		
		<script src="<?php echo site_url('assets/plugins/fastclick/fastclick.js');?>"></script>

		<!-- AdminLTE App -->
		<script src="<?php echo site_url('assets/dist/js/app.min.js'); ?>"></script>
		<script src="<?php echo site_url("assets/plugins/iCheck/icheck.min.js"); ?>"></script>
		
		<!-- all plugin scripts -->
		<script type="text/javascript" src="<?php echo site_url('assets/js/pnotify.custom.min.js');?>"></script>		
			
		<!-- DatePicer plugins -->
		<link rel="stylesheet" href="<?php echo site_url("assets/plugins/datepicker/datepicker3.css"); ?>" />
		<script src="<?php echo site_url("assets/plugins/datepicker/bootstrap-datepicker.js"); ?>"></script>	
			
		<!-- TimePicer plugins -->
		<link rel="stylesheet" href="<?php echo site_url("assets/plugins/timepicker/bootstrap-timepicker.min.css"); ?>" />
		<script src="<?php echo site_url("assets/plugins/timepicker/bootstrap-timepicker.min.js"); ?>"></script>	

		<!-- DataTables -->
		<script src="<?php echo site_url('assets/plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
		<script src="<?php echo site_url('assets/plugins/datatables/dataTables.bootstrap.min.js'); ?>"></script>
		
		<!-- Select2 plugins -->
		<link rel="stylesheet" href="<?php echo site_url("assets/plugins/select2/select2.min.css"); ?>" />
		<script src="<?php echo site_url('assets/plugins/select2/select2.min.js'); ?>"></script>
		
		<!-- Chosen plugins -->
		<link rel="stylesheet" href="<?php echo site_url("assets/css/chosen.min.css"); ?>" />
		<script src="<?php echo site_url('assets/js/chosen.jquery.min.js'); ?>"></script>
		
		<!-- inline styles related to this page -->
		<link rel="stylesheet" href="<?php echo site_url('assets/css/custom.css'); ?>" />
		<script src="<?php echo site_url('assets/js/custom.js'); ?>"></script>
	</head>

	<body class="hold-transition login-page">
		<div class="login-box">
	        <div class="login-logo">
	            <a href="<?php echo site_url('admin'); ?>"><?php echo config_item('admin_company'); ?></a>
	        </div>

	        <div class="login-box-body">
	        	<h4 class="header blue lighter bigger">
					<i class="fa fa-key green"></i> Change Password 
				</h4>
				<br>

	            <form id="changePasswordForm">
	                <div class="form-group has-feedback">
	                    <input type="password" name="password" class="form-control" placeholder="New Password">
	                    <span class="fa fa-key form-control-feedback"></span>
	                </div>
	                <div class="form-group has-feedback">
	                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password">
	                    <span class="fa fa-key form-control-feedback"></span>
	                </div>
	                <div class="row">
	                    <div class="col-sm-12">
	                        <button id="chagePasswordBtn" type="button" class="btn btn-primary btn-flat pull-right">
	                        	<i class="fa fa-key"></i> Change
	                        </button>
	                    </div>
	                </div>
	            </form>
	        </div>
        	<a class="btn btn-lg btn-primary btn-block btn-flat" href="<?php echo site_url('admin'); ?>">
				<small><i class="fa fa-arrow-left"></i> Go to Login</small>
        	</a>
	    </div>

		<!-- basic scripts -->

		<script src="<?php echo site_url("assets/plugins/jQuery/jquery-2.2.3.min.js"); ?>"></script>
		<script src="<?php echo site_url("assets/bootstrap/js/bootstrap.min.js"); ?>"></script>
		<script src="<?php echo site_url("assets/js/pnotify.custom.min.js"); ?>"></script>
		<script src="<?php echo site_url("assets/plugins/iCheck/icheck.min.js"); ?>"></script>

		<script type="text/javascript">
			jQuery(function($) {		
                <?php $user_id = $this->uri->segment(3); ?>		
				$('#changePasswordForm').on('click', '#chagePasswordBtn', function (e) {
			        e.preventDefault();          
                    var btn = $(this);
                    
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url("admin/login/reset_password/{$user_id}"); ?>",
                        data: $("#changePasswordForm").serialize(),
                        beforeSend: function() {
                            PNotify.removeAll();
                            new PNotify({
                                type: 'info',
                                text: '<?php echo lang('loader_message'); ?>',
                                icon:false,
                                hide:false,
                            });
                            btn.attr('disabled',true);
                        },
                        error: function() {
                            PNotify.removeAll();
                            new PNotify({
                                type: 'error',
                                text: '<?php echo lang('connection_timeout'); ?>',
                                hide:false
                            });
                            btn.attr('disabled',false);
                        },
                        success: function(response) {
                            btn.attr('disabled',false);
                            PNotify.removeAll();
                            if(response.err == 0) {
                                btn.html('Redirecting..');
                                location.replace(response.retval);
                            }
                            else {
                                new PNotify({
                                    type: 'error',
                                    title: 'Error Occured!',
                                    text: response.msg
                                });                 
                            }
                        }
                    });
			    });
			});
		</script>
	</body>
</html>