<?php
// ini_set('display_errors','on');
// error_reporting(E_ALL);
global $configData;
include_once 'simple_html_dom/simple_html_dom.php';

session_start();
$_SESSION['type'] = 'sanctions';
// print_r($_SESSION);die;
include_once 'function.php';
if(!isset($_SESSION['userKey'])){
  header("location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="assets/images/favicon.png" rel="icon">
    <title>SCRAPING TOOL</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/swalert.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/fonts.css" rel="stylesheet">
    <script>
      var baseUrl = '<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>';
    </script>
  </head>
  <body id="page-bg">
    <div class="top-profile">
      <div class="user-name">
        <h5>Welcome, <?php echo $_SESSION['uname']; ?></h5>
      </div>
      <!-- <span><?php //echo (isset($_SESSION['NEXTBILLINGDATE']) && $_SESSION['NEXTBILLINGDATE'] != '' ? 'Next Billing Date:  '.$_SESSION['NEXTBILLINGDATE'] : '');?> </span> -->
      <div class="user-log">
        <!-- <a href="home.php" class="brdRght">Application List</a>
        <a href="course.php" class="brdRght">Course List</a> -->
        <a href="#" class="logoutBtn">Log out</a>
      </div>
    </div>
    <div class="main-div">
      <div class="container">
        <div class="title-logo">
          <a href="#">
            <h1>Scraping
              <span>Tool</span>
            </h1>
            <!-- <small>Protect your company before it is too late !</small> -->
          </a>
        </div>

        <!-- Custom fonts for this template -->
        <div class="login-form">
          <div class="row">
            <div class="col-md-10 col-md-offset-1">
              <div class="search-web">
                <form method="post" action="search-table.php" id="search_form">
                  <div class="tab-form">
                    <ul id="myTab" class="nav nav-tabs title-tab" style="margin-bottom: 15px;">
                      <li class="active">
                        <a href="#Parameters" data-toggle="tab">Scraping Inputs</a>
                      </li>
                      <!-- <li>
                        <a href="#Search" data-toggle="tab">Web Search</a>
                      </li>
                      <li>
                        <a href="#Output" data-toggle="tab">Formatting & Output</a>
                      </li> -->
                    </ul>
                    <div id="myTabContent" class="tab-content">
                      <div class="tab-pane fade in active" id="Parameters">
                        <div class="row m-t">
                          <div class="col-xs-6 col-sm-6 col-md-6">
                            <label for="" class="name-lab">Category</label>
                            <select data-gtm-id="search-searchpanel-select-1416325534901" id="category" name="searchObjectCategory" class="select-form hasCustomSelect">
                              <option selected="selected" value="APARTMENT_AND_HOUSE">Apartment &amp; house</option>
                              <option value="APARTMENT">Apartment</option>
                              <option value="HOUSE">House, chalet, rustico</option>
                              <option value="FURNISHED_DWELLING">Furnished dwelling</option>
                              <option value="PARKING_PLACE">Parking space, garage</option>
                              <option value="OFFICE">Office</option>
                              <option value="COMMERCIAL">Commercial &amp; industry</option>
                              <option value="STORAGE_ROOM">Storage room</option>
                              <option value="PARTNER_HOLIDAY_RENTALS">Holiday rentals</option>
                            </select>
                          </div>
                          <div class="col-xs-6 col-sm-6 col-md-6">
                            <label for="" class="name-lab">Location</label>
                            <input data-gtm-id="search-searchpanel-input-text-1414597193336" type="text" placeholder="" name="search-criteria-ghost" placeholder="Cities, regions, ZIP, country" class="input-form" autocapitalize="none" autocorrect="off" autocomplete="off" maxlength="50">
                          </div>
                        </div>
                        <div class="row m-t">
                          <div class="col-xs-12 col-sm-12 col-md-12">
                            <label for="" class="name-lab">Price range</label>
                          </div>
                          <div class="col-xs-6 col-sm-6 col-md-6">
                            <label for="" class="name-lab">From</label>
                            <input type="text" placeholder="" name="priceRangeField:minField" class="input-form">
                          </div>
                          <div class="col-xs-6 col-sm-6 col-md-6">
                            <label for="" class="name-lab">To</label>
                            <input type="text" placeholder="" name="priceRangeField:maxField" class="input-form">
                          </div>
                        </div>
                        
                        <div class="row">
                          <div class="col-md-12">
                            <button type="" class="login-btn search_btn">SUBMIT REQUEST</button>
                            <a href="?clear_logs=true" class="login-btn">Clear Logs</a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 logDiv" style=" max-height: 200px; overflow-y: scroll; color: #fff;">
                  <div>
                </form> 
                
              </div>
            </div>
          </div>
        </div>
        <!-- Custom fonts for this template -->
      </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/bootstrap-tabcollapse.js"></script>
    <script src="assets/js/swalert.js"></script>
    <script src="assets/js/script.js"></script>
  </body>
</html>