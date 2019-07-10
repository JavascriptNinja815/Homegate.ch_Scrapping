<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="assets/images/favicon.png" rel="icon">
    <title>SCRAPING TOOL</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- <link href="assets/css/datatable.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link href="assets/css/swalert.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/fonts.css" rel="stylesheet">
    <script>
      var baseUrl = '<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>';
    </script>
  </head>
  <body id="page-bg">
    <div class="main-div">
      <div class="container">
        <!-- logo -->
        <div class="title-logo">
          <a href="#">
            <h1>Scraping
              <span>Tool</span>
            </h1>
          </a>
        </div>

        <!-- Search Form -->
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
                            <input id="criteria" type="text" name="search-criteria-ghost" placeholder="Cities, regions, ZIP, country" class="input-form" autocapitalize="none" autocorrect="off" autocomplete="off" maxlength="50">
                          </div>
                        </div>
                        <div class="row m-t">
                          <div class="col-xs-12 col-sm-12 col-md-12">
                            <label for="" class="name-lab">Price range</label>
                          </div>
                          <div class="col-xs-6 col-sm-6 col-md-6">
                            <label for="" class="name-lab">From</label>
                            <input id="minPrice" value="0" min="0" type="number" placeholder="" name="priceRangeField:minField" class="input-form">
                          </div>
                          <div class="col-xs-6 col-sm-6 col-md-6">
                            <label for="" class="name-lab">To</label>
                            <input id="maxPrice" type="number" placeholder="" name="priceRangeField:maxField" class="input-form">
                          </div>
                        </div>
                        
                        <div class="row">
                          <div class="col-md-12">
                            <button type="" class="login-btn search_btn">SUBMIT REQUEST</button>
                            <!-- <a href="" class="login-btn clear_logs">Clear Logs</a> -->
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 logDiv" style=" max-height: 200px; overflow-y: scroll; color: #fff;">
                  </div>
                </form> 
              </div>
            </div>
          </div>
        </div>
        <!-- Search Form -->

        <!-- Data Table -->
        <div class="search-table-main hide">
            <div class="row">
                <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="print-div">
                                    <button href="<?php echo 'websearch.php'; ?>" class="new-query-btn">Search Data</button>
                                </div>
                            </div>
                        </div>
                    <!-- Search-table -->
                    <div class="search-web clearfix">
                        <div class="table-lable">
                            <label for=""> <strong>Search Result </strong></label>
                        </div>
                        <div class="table-bg table-responsive table-p-20">
                            <table class="table table-condensed EnType-table" id="tableUsers">
                                <thead>
                                    <tr>
                                        <!-- <th>Image </th> -->
                                        <th>Title </th>
                                        <th>Type </th>
                                        <th>Address </th>
                                        <th>Price </th>
                                        <th>Contact Name </th>
                                        <th>Contact Phone </th>
                                        <th>Available date </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <!-- Search-table -->
                </div>
            </div>
        </div>
        <!-- Data Table -->

      </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/bootstrap-tabcollapse.js"></script>
    <!-- <script src="assets/js/datatable.js"></script> -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <!-- <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script> -->
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <!-- <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script> -->
    <script src="assets/js/swalert.js"></script>
    <script src="assets/js/script.js"></script>
  </body>
</html>