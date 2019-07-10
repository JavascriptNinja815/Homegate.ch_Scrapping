			
$(window).on('load',function() {
    $('.se-pre-con').fadeOut('slow');
});
$('.se-pre-con').fadeOut('slow');

jQuery(document).ready(function(e) {
    'use strict';
    if($('#myTab').length > 0){
        $('#myTab').tabCollapse();
    }

    $(document).on('click','.new-query-btn',function(e){
        window.location.reload();
        $('.search-table-main').addClass('hide');
        $('.login-form').removeClass('hide');
        $('#search_form').find('input').val('');
        $('#search_form').find('select').val('');
    });
    $(document).on('click','.clear_logs',function(e){
        e.preventDefault();
        var allData = new FormData();
        var url = baseUrl + '/homeGate/function.php';
        // var url = baseUrl+'/parser.hom-gate.ch/homeGate_scrap/function.php';    
        allData.append('clear_logs',true);
        jQuery.ajax({
            url: url,
            data: allData,
            type: "post",
            dataType: "json",
            processData: false,
            contentType: false
        }).done(function(data) {
            swal('Success Message','Successfully cleared the log file!', 'success');
            window.location.reload();
            return false;
        });
    });

    $(document).on('click','.search_btn',function(e){
        e.preventDefault();
        var $this = $(this);
        var oldHtml = $this.html();
        $this.attr('disabled',true);
        $this.html('<i class="fa fa-spinner fa-spin"></i>');
        var criteria = $('#search_form').find('#criteria').val();
        var minPrice = $('#search_form').find('#minPrice').val();
        var maxPrice = $('#search_form').find('#maxPrice').val();
        if(
            (typeof criteria === 'undefined' || criteria == '') || 
            (typeof minPrice === 'undefined' || minPrice == '') || 
            (typeof maxPrice === 'undefined' || maxPrice == '')
        ){
            swal('Oops!','Please fill all fields!', 'error');
            $this.html(oldHtml);
            $this.attr('disabled',false);
            $('.logDiv').addClass('hide');
            return false;
        }
        var logFileName = 'logs'+new Date().getTime()+'.txt';
        var jsonFileName = 'Data'+new Date().getTime()+'.json';
        var form = $('#search_form')[0];
        var allData = new FormData(form);
        var url = baseUrl + '/homeGate/function.php';
        // var url = baseUrl+'/parser.hom-gate.ch/homeGate_scrap/function.php';        
        allData.append('log_file_name', logFileName);
        allData.append('json_file_name', jsonFileName);
        allData.append('search_form',true);

        setInterval(function(){
            jQuery.get({ url: 'logData/'+logFileName, cache: false }, function(data) {
                var shouldScroll = false;
                if($('.logDiv').find('.line').length == 0) {
                    shouldScroll = true;
                }
                if ($('.logDiv')[0].scrollTop >= ($('.logDiv')[0].scrollHeight - $('.logDiv')[0].offsetHeight)) {
                    shouldScroll = true;
                }
                var numLinesToFetch   = 101;
                var lines = data.split(/\n/g);
                lines = lines.slice(numLinesToFetch * -1);
                $('.logDiv').html(lines.join("\n"));
                if(shouldScroll) $('.logDiv').animate({scrollTop: $('.logDiv')[0].scrollHeight}, 100);
            });
        }, 1000);
        jQuery.ajax({
            url: url,
            data: allData,
            type: "post",
            dataType: "json",
            processData: false,
            contentType: false
        }).done(function(data) {
            if(data.status == 0){
                swal('Error Message',data.msg, 'error');
                $this.html(oldHtml);
                return false;
            }else{
                if(data.nextPage <= data.lastPage){
                    getNextPage(data.nextPage,data.lastPage,data.nPageUrl,data.log_file_name,data.json_file_name);
                }else{
                    if (!$.fn.DataTable.isDataTable('#tableUsers')){
                        $('#tableUsers').dataTable({
                            data: data.data,
                            dom: 'Bfrtip',
                            buttons: [
                                // 'copyHtml5',
                                'excelHtml5',
                                // 'csvHtml5',
                                // 'pdfHtml5'
                            ],
                            "aaSorting": []
                        });
                    }else{
                        $('#tableUsers').dataTable().fnClearTable();
                        $('#tableUsers').dataTable({"aaSorting": []}).fnAddData(data.data); 
                    }
                    $('.search-table-main').removeClass('hide');
                    $('.login-form').addClass('hide');
                }
            }
            $this.attr('disabled',false);
            $this.html(oldHtml);
        });
    });
      
});

function getNextPage(nextPage,lastPage,nPageUrl,log_file_name,json_file_name){
    var flag = true;
    if(flag){
        flag = false;
        var url = baseUrl + '/homeGate/function.php';
        // var url = baseUrl+'/parser.hom-gate.ch/homeGate_scrap/function.php';
        jQuery.ajax({
            url: url,
            data: { 
                nextPageNo    : nextPage,
                lastPageNo    : lastPage,
                nPageUrl      : nPageUrl,
                log_file_name : log_file_name,
                json_file_name: json_file_name,
                nextPage      : true,
            },
            type: "post",
            dataType: "json",
        }).done(function(data) {
            flag = true;
            if(data.nextPage <= data.lastPage){
                getNextPage(data.nextPage,data.lastPage,data.nPageUrl,data.log_file_name,data.json_file_name);
            }else{
                if (!$.fn.DataTable.isDataTable('#tableUsers')){
                    $('#tableUsers').dataTable({
                        data: data.data,
                        dom: 'Bfrtip',
                        buttons: [
                            // 'copyHtml5',
                            'excelHtml5',
                            // 'csvHtml5',
                            // 'pdfHtml5'
                        ],
                        "aaSorting": []
                    });
                }else{
                    $('#tableUsers').dataTable().fnClearTable();
                    $('#tableUsers').dataTable({"aaSorting": []}).fnAddData(data.data); 
                }
                $('.search-table-main').removeClass('hide');
                $('.login-form').addClass('hide');
            }
        });
    }
}