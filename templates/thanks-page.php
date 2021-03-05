<?php 

global $woocommerce;

?>		
<!DOCTYPE html>
<html>
	<head>
		<?php get_header(); ?>		
	</head>
	<body>
		<section id="login">
            <article class="container">
                <div class="row">
                    <div class="col-lg-24 col-md-24 col-sm-24 col-xs-24">
                        <div class="login-form">
                            <p class="notification">
                                <span><?= $desc ?></span>
                                <br/><br/>
                                <?= $status_desc ?>
								<br>
								<?php 
									
									echo "Customer Name : " .$customer.'<br>';
									echo "Bill Number : ". $order_id .'<br>';
									echo "Bill Total : Rp ".number_format($bill_total).'.-<br>';
									echo "Payment Method : ".$payment_method .'<br>';

								?>
                            </p>
                        </div>
                    </div>
                </div>
            </article>
        </section>
        <?php get_footer(); ?>
		<footer>
				
		</footer>
	</body>
</html>

<style type="text/css">

#login {
    margin: 7% 0 0;
    padding: 0 0 50px;
    text-align: center;
}
.section {
    display: table;
    width: 100%;
}

#login .login-form {
    background: #e7e7e7 none repeat scroll 0 0;
    border-radius: 5px;
    display: inline-block;
    height: 442px;
    padding: 40px 35px;
    text-align: center;
    width: 400px;
}
#login .login-form p.notification {
    background: white none repeat scroll 0 0;
    border-radius: 5px;
    color: #393939;
    margin: 25px 0 12px;
    padding: 20px 20px 30px;
    text-align: center;
}

</style>