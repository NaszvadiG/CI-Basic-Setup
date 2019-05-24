<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<title><?php echo config_item('site_title');?></title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
        <!-- <link href="<?php //echo site_url("assets/images/mini_logo.png"); ?>" rel="shortcut icon"> -->

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="<?php echo site_url("assets/bootstrap/css/bootstrap.min.css"); ?>" />
		<link rel="stylesheet" href="<?php echo site_url('assets/font-awesome/4.7.0/css/font-awesome.min.css'); ?>" />
		<!-- Ionicons -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
		
		<link rel="stylesheet" href="<?php echo site_url("assets/dist/css/AdminLTE.min.css"); ?>" />
		<link rel="stylesheet" href="<?php echo site_url("assets/plugins/iCheck/all.css"); ?>" />
		<link rel="stylesheet" href="<?php echo site_url("assets/css/pnotify.custom.min.css"); ?>" />
		<link rel="stylesheet" href="<?php echo site_url("assets/css/custom.css"); ?>" />

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>

	<body class="hold-transition login-page">
		<div class="login-box">
	        <div class="login-logo">
	            <a href="<?php echo site_url('admin'); ?>"><?php echo config_item('admin_company'); ?></a>
	        </div>

	        <div class="login-box-body">
	        	<h4 class="header red lighter bigger">
					<i class="ace-icon fa fa-key"></i>
					Retrieve Password
				</h4>

	            <p><small>Enter your email and to receive instructions</small></p>
	            <br>

	            <form id="loginContainer">
	                <div class="form-group has-feedback">
	                    <input type="email" name="email" class="form-control" placeholder="Email">
	                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
	                </div>
	                <div class="row">
	                    <div class="col-sm-12">
	                        <button id="loginBtn" type="button" class="btn btn-danger btn-flat pull-right">
	                        	<i class="fa fa-lightbulb-o"></i> Send Me!
	                        </button>
	                    </div>
	                    <!-- /.col -->
	                </div>
	            </form>
	        </div>
        	<a class="btn btn-lg btn-danger btn-block btn-flat" href="<?php echo site_url('admin'); ?>">
				<small><i class="fa fa-arrow-left"></i> Back to login</small>
        	</a>
	        <!-- /.login-box-body -->
	    </div>

		<!-- basic scripts -->

		<script src="<?php echo site_url("assets/plugins/jQuery/jquery-2.2.3.min.js"); ?>"></script>
		<script src="<?php echo site_url("assets/bootstrap/js/bootstrap.min.js"); ?>"></script>
		<script src="<?php echo site_url("assets/js/pnotify.custom.min.js"); ?>"></script>
		<script src="<?php echo site_url("assets/plugins/iCheck/icheck.min.js"); ?>"></script>

		<script type="text/javascript">
			jQuery(function($) {				
				$('#loginContainer').on('click', '#loginBtn', function (e) {
			        e.preventDefault();
			        var btn = $(this);

			        $.ajax({
			            dataType: 'json',
			            type: 'POST',
			            url: '<?php echo site_url('admin/login/process_forgot_password_request'); ?>',
			            data: $('#loginContainer').serialize(),
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
				
				$('#loginContainer').on('keyup', '[name="username"]', function(e) {
					if(e.which == 13) {
						$('#loginBtn').click();
					}
				});
			});
		</script>
	</body>
</html>