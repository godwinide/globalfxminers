<html class="no-js" lang="en"><script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<?php require_once("../../Connections/db.php"); ?>

<?php
$gre = mysql_query("SELECT * FROM setting") or die(mysql_error());
$dat = mysql_fetch_array($gre);

$sit = $dat['sitename'];

 define("sitename",$sit);?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "verified,unverified";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../account";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$currentPage = $_SERVER["PHP_SELF"];

$colname_access = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_access = $_SESSION['MM_Username'];
}
mysql_select_db($database_dbconnect, $dbconnect);
$query_access = sprintf("SELECT * FROM users WHERE email = %s", GetSQLValueString($colname_access, "text"));
$access = mysql_query($query_access, $dbconnect) or die(mysql_error());
$row_access = mysql_fetch_assoc($access);
$totalRows_access = mysql_num_rows($access);

$maxRows_products = 10;
$pageNum_products = 0;
if (isset($_GET['pageNum_products'])) {
  $pageNum_products = $_GET['pageNum_products'];
}
$startRow_products = $pageNum_products * $maxRows_products;

mysql_select_db($database_dbconnect, $dbconnect);
$query_products = "SELECT * FROM products ORDER BY serial DESC";
$query_limit_products = sprintf("%s LIMIT %d, %d", $query_products, $startRow_products, $maxRows_products);
$products = mysql_query($query_limit_products, $dbconnect) or die(mysql_error());
$row_products = mysql_fetch_assoc($products);

if (isset($_GET['totalRows_products'])) {
  $totalRows_products = $_GET['totalRows_products'];
} else {
  $all_products = mysql_query($query_products);
  $totalRows_products = mysql_num_rows($all_products);
}
$totalPages_products = ceil($totalRows_products/$maxRows_products)-1;


$queryString_products = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_products") == false && 
        stristr($param, "totalRows_products") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_products = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_products = sprintf("&totalRows_products=%d%s", $totalRows_products, $queryString_products);
?>
<!doctype html>
<html class="no-js" lang="en">


<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo sitename ?>| Forex, Stock, Crypto, Cfd, Indices</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="../profile/assets/images/icon/favicon.ico">
    <link rel="stylesheet" href="../profile/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../profile/assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../profile/assets/css/themify-icons.css">
    <link rel="stylesheet" href="../profile/assets/css/metisMenu.css">
    <link rel="stylesheet" href="../profile/assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="../profile/assets/css/slicknav.min.css">
    <!-- amchart css -->
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <!-- others css -->
    <link rel="stylesheet" href="../profile/assets/css/typography.css">
    <link rel="stylesheet" href="../profile/assets/css/default-css.css">
    <link rel="stylesheet" href="../profile/assets/css/styles.css">
    <link rel="stylesheet" href="../profile/assets/css/responsive.css">
    <!-- modernizr css -->
    <script src="../profile/assets/js/vendor/modernizr-2.8.3.min.js"></script>
   <link rel="stylesheet" href="../../assets/css/stockmenu.css"> 
      
</head>

<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- preloader area start -->
    <div id="preloaderr">
        <div class="loaderr"></div>
    </div>
    <!-- preloader area end -->
    <!-- page container area start -->
    <div class="page-container">
        <!-- sidebar menu area start -->
        <div class="sidebar-menu">
            <div class="sidebar-header">
                <div class="logo">
                  
                    
				<a href="../"><img src="../assets/images/bg/logo.png" alt="logo"></a>	
                </div>
            </div>
            <div class="main-menu">
                <div class="menu-inner">
                    <nav>
                    
                        <ul class="metismenu" id="menu">
                        
                        <li> 
                                <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-dashboard"></i><span>Dashboard
                                    </span></a>
                                <ul class="collapse">
                                    <li><a href="../../dashboard">Dashboard</a></li>
                                   
                                </ul>
                            </li>
                           
                            <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-user-plus"></i><span>My Account
                                    </span></a>
                                <ul class="collapse">
                                    <li><a href="../profile">Edit Profile</a></li>
                                    <li><a href="../profile/security">Security</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-history"></i><span>Transactions</span></a>
                                <ul class="collapse">
                                    <li><a href="../history">Trade History</a></li>
                                    
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-money"></i><span>Deposit</span></a>
                                <ul class="collapse">
                                    <li><a href="#" data-toggle="modal" data-target="#exampleModalCenter">New Request</a></li>
                                      <li><a href="../deposit">History</a></li>
                                  
                                </ul>
                            </li>
                            
                            <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-bank"></i><span>Withdrawal</span></a>
                                <ul class="collapse">
                                    <li><a href="../withdrawal">New Request</a></li>
                                      <li><a href="history">History</a></li>
                                  
                                </ul>
                            </li>
                           
                           
                            <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-power-off"></i> <span>Logout</span></a>
                                <ul class="collapse">
                                    <li><a href="../../logout">Logout</a></li>
                                    
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <!-- sidebar menu area end -->
        <!-- main content area start -->
        <div class="main-content">
            <!-- header area start -->
            <div class="header-area">
                <div class="row align-items-center">
                    <!-- nav and search button -->
                    <div class="col-md-6 col-sm-8 clearfix">
                        <div class="nav-btn pull-left">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <div class="search-box pull-left">
                          <?php 
											$email = $row_access['email'];
											$user = $row_access['firstname'];
											$id  = rand(58754,95782);
											$date = date("d/m/Y");
											$stat = "Pending";
											
											$min = "250";
											$action = "Add Fund";
											
											if(isset($_POST['depo'])){
											
											$amount = $_POST['amount'];
											$method = $_POST['method'];
											
											if($amount >= $min){
											
										$inst = mysql_query("INSERT INTO deposit(orderno,user,email,amount,method,status,date,action) VALUE('$id','$user','$email','$amount','$method','$stat','$date','$action')") or die(mysql_error());	
										
										if($inst){
											
											echo '<div class="alert alert-success">
											<strong>Order Received</strong> Our representative will get in touch with you shortly with necessary information regarding your deposit. 
											</div>';
											}
											}else{ echo '<div class="alert alert-danger">
											<strong>Amount Too Low</strong>. Minimum deposit is $100.00
											</div>';}
											
											}
											
											?>
                        </div>
                    </div>
                    <!-- profile info & task notification -->
                    
                        </div>
                        <div class="user-profile pull-right">
                           <h5><?php echo $row_access['currency'];?> <?php echo number_format($row_access['bal'],2,'.',',');?></h5>
                            <div class="dropdown-menu">
                                
                                
                          </div>
                </div>
            </div>
          <!-- header area end -->
            <!-- page title area start -->
           <div class="top">
  
  <ul class="menu">
    
    <li><a href="../" >Trade</a></li>
    <li><a href="../history" >Trade History</a></li>
    <li><a href="../withdrawal" class="selected">Withdrawal</a></li>
    <li><a href="#" data-toggle="modal" data-target="#exampleModalCenter">Deposit</a></li>
  </ul>
</div>
            <!-- page title area end -->
            <div class="main-content-inner">
                <div class="row">
                    <!-- seo fact area start -->
                               
                    <!-- seo fact area end -->
                    <!-- Social Campain area start -->
                    <div class="col-lg-4 mt-5">
                        <div class="card">
                            <div class="card-body pb-0">
                                <h4 class="header-title">Account Statement</h4>
                                
                
                                <ul class="list-group">
                                    <li class="list-group-item">Balance: <strong><?php echo $row_access['currency'];?> <?php echo number_format($row_access['bal'],2,'.',',');?></strong></li>
                                    <li class="list-group-item">Status :  <span class="badge badge-success"> <?php echo $row_access['level'];?></span> </li>
                                    <li class="list-group-item">IP :  <span class="badge badge-light"> <?php echo $row_access['IPaddress'];?></span> </li>
                                    <li class="list-group-item">Currency  :  <span class="badge badge-light"> <?php echo $row_access['currency'];?></span></li>
                                    <li class="list-group-item">Country  :  <span class="badge badge-light"> <?php echo $row_access['country'];?></span></li> 
                                </ul>
                            
                       
                  
                        
                    <!-- Active Items end -->  
                            </div>
                        </div>
                    </div>
                    <!-- Social Campain area end -->
                    <!-- Statistics area start -->
                    <div class="col-lg-8 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <h4><span class="fa fa-bank"> Withdrawal</span></h4>
                                
                                <p>
                                <hr>
                                 <p> 
								 
								
                                
                                <p>
                                
								 <?php 
								 
								 if(isset($_POST['btn'])){
									 $email = $row_access['email'];
									 $user = " ".$row_access['firstname']." ".$row_access['lastname']." ";
									$method = $_POST['method']; 
									$amount = $_POST['amount'];
									$accno = $_POST['accno'];
									$stat = "Pending";
									$cur = $row_access['currency'];
									$date = date("d/m/Y");
									$min = 250;
									$id = rand(88474,99849);
									$action = "Pay";
									
									
									
									if(!empty($amount)){
										
										if($amount >= $min == true){
									
								if($amount <= $row_access['bal'] !== true){ 
								
									echo '<div class="alert alert-danger"><strong>LOW BALANCE</strong></div>';
								
								} else{
									
									
								$upd = mysql_query("INSERT INTO withdrawal(orderid,user,email,amount,currency,status,method,accno,date,action) VALUES('$id','$user','$email','$amount','$cur','$stat','$method','$accno','$date','$action')") or die(mysql_error());	
								
								echo '<div class="alert alert-success"><strong>Request Successful</strong>. Your account manager will contact you shortly regarding your payment. Thank you!</div>'; 
								
								$from = "contact@anchorlimited.com"; //the email address from which this is sent
$to = "$email"; //the email address you're sending the message to
$subject = "Withdrawal Request"; //the subject of the message
$message = "

Dear $email,

Request Successful and  your account manager will contact you shortly regarding your payment. All our Withdrawals requests are addressed and handled as quickly as possible. Thank you!
";
//now mail
$SMTPMail = mail($to,$subject,$message,$headers);

								
								}
										}else{echo '<div class="alert alert-danger"><strong>Amount Below Minimum</strong>. Minimum withdrawal '.$cur.''.$min.'</div>';}
										
									}else{echo '<div class="alert alert-danger"><strong>Enter Amount</strong></div>';}
									 }
								 
								 
								 
								 ?></p>
                                <div >
                                
                                
                              
                                
                                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post">
                                <p align="right">
                                <?php
										
							   $arr = array("WireTransfer","creditcard","PerfectMoney","Bitcoin","Skrill","Western Union","MoneyGram","Neteller");
							    $thisMethod = $row_access['paymethod'];
								
								
								
								$showBtc = '
								
								<p><img src="../assets/images/bitcoin.jpg"></p>
								
								<script>
								
								window.location.href="btc";
								</script>';
								
							
								
								$showWire = '
								
								<p><img src="../assets/images/wire.png" width="150" height="40"></p>
								<script>
								
								window.location.href="wire";
								</script>
								
								
								';
								
								
								
								$showPM = '
							
			                        <p>
								
								<p><img src="../assets/images/pmhov.jpg"></p>
								
								<script>
								
								window.location.href="pm";
								</script>
								
								';
									if($thisMethod === 'Bitcoin'){
												
												$showForm = $showBtc;
												}
												elseif($thisMethod === 'WireTransfer'                                                  ){
													
												$showForm = $showWire;	
													
 													 }elseif($thisMethod ===                                                    'PerfectMoney'){
														 
													$showForm = $showPM;	 
														 }
														 else{
															 
														$showForm = $btn = '
								
								<p>Select Withdrawal Method</p>
					
					<p>
								
								
								<button class="btn btn-info" onClick="wire()  ">
                <i class="fa fa-bank" ></i> Bank Transfer
              </button>
			  
			  <button  class="btn btn-warning" onClick=" btc()">
                <i class="fa fa-btc" ></i> Bitcoin
              </button>
			  
			  <button class="btn btn-danger" onClick="pm() ">
                <i class="fa fa-money"></i> Perfect Money
              </button>
			  		
			  
			
								';	 
															 }
															 
														 
											
								 ?>
                           
                        
                           
                               <script>
                                 function wire(){
								
								 window.location.href="wire";
								 }
                                 
								 function btc(){
									
									 window.location.href="btc";
									 }
									 
									 function pm(){
									
								 window.location.href="pm";
										 
										 }
										 
										 function relo(){
											 window.location.reload()
											 }
										 
									
                                 </script>  
                                </p>
                                
                                
                               <!-- form region -->
                               
                               <?php echo $showForm;?>
                                
                            
                                
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Statistics area end -->
                    <!-- Advertising area start -->
                   						<script>
function myFunction() {
  var copyText = document.getElementById("myInput");
  copyText.select();
  copyText.setSelectionRange(0, 99999)
  document.execCommand("copy");
  alert("Copied the text: " + copyText.value);
}
</script>
<script>
function myFunction2() {
  var copyText = document.getElementById("myInput2");
  copyText.select();
  copyText.setSelectionRange(0, 99999)
  document.execCommand("copy");
  alert("Copied the text: " + copyText.value);
}
</script>
                                <div class="modal fade" id="exampleModalCenter">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Deposit Fund</h5>
                                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                            
                                                <p>                                               
                                                <div class="form-group">
						
                                                    <label for="example-text-input" class="col-form-label">Please Copy the Address </label>
                                        <label for="example-text-input" class"col-form-label">And send the amount you want to invest.</label></label>            
                                            <h7><br><p style="color:#FF0000";>Bitcoin </p><input class="float-center" type="text" value="1GqP6k3RncL8UvhpVb8wjeCniSS42SoWxJ"  size="40" id="myInput" readonly>
<button class="btn btn-danger" onclick="return myFunction()"> Copy Wallet </button></h7>
											<center><img class="img-responsive" style="margin: auto" src="https://chart.googleapis.com/chart?chs=200x200&amp;cht=qr&amp;chl=1GqP6k3RncL8UvhpVb8wjeCniSS42SoWxJ"></center>
											
											<h7><br> <p style="color:#FF0000";>Ethereum </p><input class="float-center" type="text" value="0xA2C0B6eC090d6312F23b5a3F4Ee68F470eAC2F9a"  size="40"  id="myInput2" readonly>
<button class="btn btn-danger" onclick="myFunction2()"> Copy Wallet </button></h7>
<center><img class="img-responsive" style="margin: auto" src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=Ethereum:0xA2C0B6eC090d6312F23b5a3F4Ee68F470eAC2F9a"></center>
                                        </div>
										 <label for="example-text-input" class"col-form-label">Investment range from 150 USD to 50000 USD</label></label>  
                                         
                                                 </p>
                                            </div>
                                            <div class="modal-footer" id="response">
																	<script>
            $(document).ready(function(){
                $("#clkMe").click(function(){
                    var dataString={};
                    $.ajax({                                      
                        url:"../read-deposits.php",
                        type: 'POST',
                        cache:false,
                        data: dataString,
                        beforeSend: function() {},
                        timeout:10000,
                        error: function() { },     
                        success: function(response) {
                           $("#response").html(response);
                           alert("Analyzing ... If your deposit has been made, it will be credited to your account.");
                        } 
                    });
                });
            });
        </script>
                                         <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                              <button type="button" id="clkMe"  name="depo" class="btn btn-flat btn-success btn-lg btn-block" >Deposited</button>    									 
                                           </div>
                                        </div>
                                    </div>
                                    
                                </div>
                           
                                          
                                         
                                                 </p>
                                            </div>
                                            <div class="modal-footer">
                                        
                                           </div></form>
                                        </div>
                                    </div>
                                    
                                </div>
                           
                    <!-- map area end -->
                    <!-- testimonial area start -->
                   
                    <!-- testimonial area end -->
                </div>
            </div>
        </div>
        <!-- main content area end -->
        <!-- footer area start-->
        <footer>
            <div class="footer-area">
                <p>© Copyright 2019 <?php echo sitename?>. All right reserved. </p>
            </div>
        </footer>
        <!-- footer area end-->
    </div>
    <!-- page container area end -->
    <!-- offset area start -->
    <div class="offset-area">
        <div class="offset-close"><i class="ti-close"></i></div>
        <ul class="nav offset-menu-tab">
            <li><a class="active" data-toggle="tab" href="#activity">Activity</a></li>
            <li><a data-toggle="tab" href="#settings">Settings</a></li>
        </ul>
        <div class="offset-content tab-content">
            <div id="activity" class="tab-pane fade in show active">
                <div class="recent-activity">
                    <div class="timeline-task">
                        <div class="icon bg1">
                            <i class="fa fa-envelope"></i>
                        </div>
                        <div class="tm-title">
                            <h4>Rashed sent you an email</h4>
                            <span class="time"><i class="ti-time"></i>09:35</span>
                        </div>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse distinctio itaque at.
                        </p>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg2">
                            <i class="fa fa-check"></i>
                        </div>
                        <div class="tm-title">
                            <h4>Added</h4>
                            <span class="time"><i class="ti-time"></i>7 Minutes Ago</span>
                        </div>
                        <p>Lorem ipsum dolor sit amet consectetur.
                        </p>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg2">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                        <div class="tm-title">
                            <h4>You missed you Password!</h4>
                            <span class="time"><i class="ti-time"></i>09:20 Am</span>
                        </div>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg3">
                            <i class="fa fa-bomb"></i>
                        </div>
                        <div class="tm-title">
                            <h4>Member waiting for you Attention</h4>
                            <span class="time"><i class="ti-time"></i>09:35</span>
                        </div>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse distinctio itaque at.
                        </p>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg3">
                            <i class="ti-signal"></i>
                        </div>
                        <div class="tm-title">
                            <h4>You Added Kaji Patha few minutes ago</h4>
                            <span class="time"><i class="ti-time"></i>01 minutes ago</span>
                        </div>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse distinctio itaque at.
                        </p>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg1">
                            <i class="fa fa-envelope"></i>
                        </div>
                        <div class="tm-title">
                            <h4>Ratul Hamba sent you an email</h4>
                            <span class="time"><i class="ti-time"></i>09:35</span>
                        </div>
                        <p>Hello sir , where are you, i am egerly waiting for you.
                        </p>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg2">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                        <div class="tm-title">
                            <h4>Rashed sent you an email</h4>
                            <span class="time"><i class="ti-time"></i>09:35</span>
                        </div>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse distinctio itaque at.
                        </p>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg2">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                        <div class="tm-title">
                            <h4>Rashed sent you an email</h4>
                            <span class="time"><i class="ti-time"></i>09:35</span>
                        </div>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg3">
                            <i class="fa fa-bomb"></i>
                        </div>
                        <div class="tm-title">
                            <h4>Rashed sent you an email</h4>
                            <span class="time"><i class="ti-time"></i>09:35</span>
                        </div>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse distinctio itaque at.
                        </p>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg3">
                            <i class="ti-signal"></i>
                        </div>
                        <div class="tm-title">
                            <h4>Rashed sent you an email</h4>
                            <span class="time"><i class="ti-time"></i>09:35</span>
                        </div>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse distinctio itaque at.
                        </p>
                    </div>
                </div>
            </div>
            <div id="settings" class="tab-pane fade">
                <div class="offset-settings">
                    <h4>General Settings</h4>
                    <div class="settings-list">
                        <div class="s-settings">
                            <div class="s-sw-title">
                                <h5>Notifications</h5>
                                <div class="s-swtich">
                                    <input type="checkbox" id="switch1" />
                                    <label for="switch1">Toggle</label>
                                </div>
                            </div>
                            <p>Keep it 'On' When you want to get all the notification.</p>
                        </div>
                        <div class="s-settings">
                            <div class="s-sw-title">
                                <h5>Show recent activity</h5>
                                <div class="s-swtich">
                                    <input type="checkbox" id="switch2" />
                                    <label for="switch2">Toggle</label>
                                </div>
                            </div>
                            <p>The for attribute is necessary to bind our custom checkbox with the input.</p>
                        </div>
                        <div class="s-settings">
                            <div class="s-sw-title">
                                <h5>Show your emails</h5>
                                <div class="s-swtich">
                                    <input type="checkbox" id="switch3" />
                                    <label for="switch3">Toggle</label>
                                </div>
                            </div>
                            <p>Show email so that easily find you.</p>
                        </div>
                        <div class="s-settings">
                            <div class="s-sw-title">
                                <h5>Show Task statistics</h5>
                                <div class="s-swtich">
                                    <input type="checkbox" id="switch4" />
                                    <label for="switch4">Toggle</label>
                                </div>
                            </div>
                            <p>The for attribute is necessary to bind our custom checkbox with the input.</p>
                        </div>
                        <div class="s-settings">
                            <div class="s-sw-title">
                                <h5>Notifications</h5>
                                <div class="s-swtich">
                                    <input type="checkbox" id="switch5" />
                                    <label for="switch5">Toggle</label>
                                </div>
                            </div>
                            <p>Use checkboxes when looking for yes or no answers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- offset area end -->
    <!-- jquery latest version -->
    <script src="../profile/assets/js/vendor/jquery-2.2.4.min.js"></script>
    <!-- bootstrap 4 js -->
    <script src="../profile/assets/js/popper.min.js"></script>
    <script src="../profile/assets/js/bootstrap.min.js"></script>
    <script src="../profile/assets/js/owl.carousel.min.js"></script>
    <script src="../profile/assets/js/metisMenu.min.js"></script>
    <script src="../profile/assets/js/jquery.slimscroll.min.js"></script>
    <script src="../profile/assets/js/jquery.slicknav.min.js"></script>

    <!-- start chart js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
    <!-- start highcharts js -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <!-- start amcharts -->
    <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
    <script src="https://www.amcharts.com/lib/3/ammap.js"></script>
    <script src="https://www.amcharts.com/lib/3/maps/js/worldLow.js"></script>
    <script src="https://www.amcharts.com/lib/3/serial.js"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
    <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
    <!-- all line chart activation -->
    <script src="../profile/assets/js/line-chart.js"></script>
    <!-- all pie chart -->
    <script src="../profile/assets/js/pie-chart.js"></script>
    <!-- all bar chart -->
    <script src="../profile/assets/js/bar-chart.js"></script>
    <!-- all map chart -->
    <script src="../profile/assets/js/maps.js"></script>
    <!-- others plugins -->
    <script src="../profile/assets/js/plugins.js"></script>
    <script src="../profile/assets/js/scripts.js"></script>
    
 
</body>

</html>
