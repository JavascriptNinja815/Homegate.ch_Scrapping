<?php
header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors','on');
error_reporting(0);
ini_set('memory_limit','-1');
ini_set('error_log','errors.txt');
ini_set('max_execution_time', 0);
ini_set('max_input_vars','20000' );
set_time_limit(0);
// echo phpinfo();die;

//--------------- search  -------------------------------
if(isset($_POST['search_form']) && $_POST['search_form']){
    ini_set('display_errors','on');
    error_reporting(0);

    ini_set('upload_max_filesize', '10M');
    
    // log
    $log_file_name = $_POST['log_file_name'];
    $txt = ' Scrapping started ';
    setLogData($txt,$log_file_name);

    include_once 'simple_html_dom/simple_html_dom.php';
    $fPageHtml = new simple_html_dom();
    $urlGet = 'https://www.homegate.ch/en';
    $cookie_file = 'cookiess.txt';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlGet);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER , true);
    curl_setopt($ch, CURLINFO_HEADER_OUT , false);
    curl_setopt($ch, CURLOPT_HEADER , false);
    curl_setopt($ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_2_0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    $result = curl_exec($ch);
    curl_close($ch);
    $fPageHtml->load($result);
    $form = $fPageHtml->find('form[id=searchForm]',0);
    if($form){
        $action = $form->action;
    }

    $category = $_POST['searchObjectCategory'];
    $criteria = $_POST['search-criteria-ghost'];
    $minPrice = $_POST['priceRangeField:minField'];
    $maxPrice = $_POST['priceRangeField:maxField'];

    $post = 'searchForm_hf_0=&offerType=radio0&search-criteria-ghost=&searchIn='.urlencode($criteria).'&searchObjectCategory='.$category.'&priceRangeField%3AminField='.$minPrice.'&priceRangeField%3AmaxField='.$maxPrice.'&roomRangeField%3AminField=&roomRangeField%3AmaxField=&searchButtonUpper=&peripheryField%3Aperiphery=&surfaceLivingRangeField%3AminField=&surfaceLivingRangeField%3AmaxField=&yearBuiltRangeField%3AminField=&yearBuiltRangeField%3AmaxField=&floorField%3Afloor=&availableFromField%3AavailableFrom=';
    
    $url = 'https://www.homegate.ch/'.$action;
    // headers 
    $useragent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.67 Safari/537.36';
    $headerOptions = array(
        ':method: POST',
        ':authority: www.homegate.ch',
        ':scheme: https',
        ':path: /en?1-1.IFormSubmitListener-search-searchForm',
        'cache-control: max-age=0',
        'origin: https://www.homegate.ch',
        'upgrade-insecure-requests: 1',
        'content-type: application/x-www-form-urlencoded',
        'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.67 Safari/537.36',
        'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
        // 'accept-encoding: gzip, deflate, br',
        'accept-language: en-US,en;q=0.9,la;q=0.8,und;q=0.7',
        'referer: https://www.homegate.ch/en',
        'Content-Length: '.strlen($post)
    );
    // curl opt array
    $curl_setopt_array = array(
        CURLOPT_URL => $url,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_AUTOREFERER => true,
        CURLINFO_HEADER_OUT => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_POST => true,
        CURLOPT_USERAGENT => $useragent,
        CURLOPT_HTTPHEADER => $headerOptions,
        CURLOPT_POSTFIELDS => $post,
        CURLOPT_HEADER => true,
        // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0
    );

    // log
    $txt = ' Fetching the first page now..';
    setLogData($txt,$log_file_name);

    $ch = curl_init();
    curl_setopt_array($ch, $curl_setopt_array);
    curl_setopt($ch, CURLOPT_COOKIESESSION, false);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);

    $result = curl_exec($ch);
    
    curl_close ($ch);
    // get Headers
    list($headers, $content) = explode("\r\n\r\n",$result,2);
    $headers = explode("\r\n",$headers);
    $resp_headers=array();
    foreach ($headers as $hKey=>$hdr){
        $hdr = explode(': ', $hdr, 2);
        $resp_headers[$hdr[0]] = $hdr[1];
    }
    
    if(!empty($resp_headers) && isset($resp_headers['Location']) && $resp_headers['Location'] != ''){
        $location = getPage($resp_headers['Location'],$log_file_name);
        $html = new simple_html_dom();
        $html->load($location);

        if($html){
            // pagination
            $firstPage = 1;$lastPage=1;
            $paginator = $html->find('p[class=paginator-counter]',0);
            if($paginator){
                $pageSpan = $paginator->find('span');
                if($pageSpan){
                    foreach($pageSpan as $k=>$span){
                        if($k == 0){ $firstPage = $span->plaintext;
                        }else{ $lastPage = $span->plaintext;}
                    }
                }
            }
            // log
            $txt = 'Found total '.$lastPage.' pages to fetch, 20 Real estate in a page';
            setLogData($txt,$log_file_name);

            // log
            $txt = 'Fetching '.$firstPage.' of '.$lastPage.' page';
            setLogData($txt,$log_file_name);

            $i = 0;
            $resData = getData($i,$html,$log_file_name,$firstPage);
            $nextPage = 2;
            $resData['nextPage'] = (int)$nextPage;
            $resData['lastPage'] = (int)$lastPage;
            $nextPageUrl = $resp_headers['Location'];//.'&ep='.$nextPage;
            $resData['nPageUrl'] = $nextPageUrl;

            $finalData = array();
            $finalData = $resData['data'];

            // log
            $txt = $firstPage.' page successfully fetched! HTTP Code: 200';
            setLogData($txt,$log_file_name);

            // if(!empty($resData) && $nextPage <= $lastPage){
            //     while($nextPage <= $lastPage){
            //         // log
            //         $txt = 'Fetching '.$nextPage.' of '.$lastPage.' page';
            //         setLogData($txt,$log_file_name);

            //         $i = 0;
            //         $nextPageUrl = $resp_headers['Location'].'&ep='.$nextPage;
            //         $nextPage++;
            //         $nPageDetails = getPage($nextPageUrl,$log_file_name);
            //         $nPageHtml = new simple_html_dom();
            //         $nPageHtml = $nPageHtml->load($nPageDetails);
            //         if($nPageHtml){
            //             $resDataNextPage = getData($i,$nPageHtml,$log_file_name,($nextPage-1));
            //             $finalData = array_merge($finalData,$resDataNextPage['data']);
            //         }
            //         // log
            //         $txt = ($nextPage-1).' page successfully fetched! HTTP Code: 200';
            //         setLogData($txt,$log_file_name);
            //     }
            // }
            $responseData = array();
            if(!empty($finalData)){
                foreach($finalData as $key=>$data){
                    $dataArray = array();
                    $title   = $data['itemTitle'];
                    $type    = $data['Type '];
                    $Address = $data['Address '];
                    $Price   = $data['Price']['priceCurrency'].' '.$data['Price']['price'];
                    $cName   = 'Not Set';
                    $cNumber = 'Not Set';
                    if(isset($data['Contact']) && !empty($data['Contact'])){
                        $contactDetails = $data['Contact'];
                        foreach($contactDetails as $cKey=>$cValue){
                            $cName = $cValue[0];
                            $cNumber = $cValue[1];
                            if($cKey == 'Contact: '){
                                $cName = $cValue[0];
                                $cNumber = $cValue[1];
                            }
                        }
                    }
                    $Available = $data['Available '];

                    $dataArray[] = $title;
                    $dataArray[] = $type;
                    $dataArray[] = $Address;
                    $dataArray[] = $Price;
                    $dataArray[] = $cName;
                    $dataArray[] = $cNumber;
                    $dataArray[] = $Available;
                    $responseData[$key] = $dataArray;
                }
            }
            $response['nextPage'] = (int)$nextPage;
            $response['lastPage'] = (int)$lastPage;
            $response['nPageUrl'] = $nextPageUrl;
            $response['log_file_name'] = $log_file_name;
            $response['json_file_name'] = $_POST['json_file_name'];
            $response['status'] = 1;
            $response['data'] = $responseData;
            
            // setTableData
            $json_file_name = $_POST['json_file_name'];
            $jsonData = setTableData($responseData,$json_file_name);

            echo json_encode($response);
            die;
        }else{
            // log
            $txt = 'Failed to get url! Something wrong!';
            setLogData($txt,$log_file_name);

            $res['msg'] = 'Failed to get data! Something wrong!';
            $res['status'] = 0;
            echo json_encode($res);
            die;
        }
    }else{
        // log
        $txt = 'Failed to get url! Something wrong!';
        setLogData($txt,$log_file_name);

        $res['msg'] = 'Failed to get data! Something wrong!';
        $res['status'] = 0;
        echo json_encode($res);
        die;
    }
    
    echo json_encode($result);
    die();
}

function setTableData($responseData,$fileName){
    $jsonData = json_encode($responseData);
    $fileName = 'json'.$fileName;
    file_put_contents('logData/'.$fileName, $jsonData);
    $getJsonData = file_get_contents('logData/'.$fileName);
    return json_decode($getJsonData,true);
}

function setLogData($msg,$fileName){
    $txt = '<div class="line"><span class="datetime">'.date('jS F').' at '.date('H:i:s').'</span> <span class="message message-success"> '.$msg.' </span> </div>';
    $myfile = file_put_contents('logData/'.$fileName, $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
}

function getData($i,$html,$log_file_name,$Page){
    ini_set('display_errors','on');
    error_reporting(0);
    // get data
    $itemList = $html->find('div[class=result-item-list]',0);
    $res = array();
    if($itemList){
        foreach($itemList->find('div[class=anchored]') as $key=>$div){
            // log
            $txt = 'Fetching '.($i+1).' of 20 Real estate on page '.$Page;
            setLogData($txt,$log_file_name);

            $detailDiv = $div->find('a[class=detail-page-link]',0);
            
            // item Title
            $itemTitle = $detailDiv->find('h2[class=item-title]',0)->plaintext;
            $res['data'][$i]['itemTitle'] = $itemTitle;
            // attribute Value
            foreach($detailDiv->find('li') as $lk=>$li){
                $attributeKey = $li->find('span[class=key]',0)->plaintext;
                $attributeValue = $li->find('span[class=value]',0)->plaintext;
                if($attributeKey != '' && $attributeValue != ''){
                    $res['data'][$i][$attributeKey] = $attributeValue;
                }
            }

            // get single page
            $singlePageUrl = 'https://www.homegate.ch'.$detailDiv->href;
            $singlePageDetail = getPage($singlePageUrl,$log_file_name);
            if($singlePageDetail){
                // Price
                $detailPrice = $singlePageDetail->find('div[class=detail-price]',0);
                $price = array();
                foreach($detailPrice->find('span') as $dpK=>$dpSpan){
                    if($dpSpan->itemprop){
                        $price[$dpSpan->itemprop] = $dpSpan->plaintext;
                    }
                }
                $res['data'][$i]['Price'] = $price;

                // single page attribute Value
                foreach($singlePageDetail->find('li') as $slk=>$sli){
                    $attributeKey = $sli->find('span[class=text--small]',0)->plaintext;
                    $attributeValue = $sli->find('span[class=text--dark]',0)->plaintext;
                    if($attributeKey != '' && $attributeValue != ''){
                        $res['data'][$i][$attributeKey] = $attributeValue;
                    }
                }

                // Contact Details
                $numberContainer = $singlePageDetail->find('div[class=show-number-container]',0);
                if($numberContainer){
                    $cNum = $numberContainer->find('span',0)->plaintext;
                    $res['data'][$i]['Contact_Phone'] = $cNum;
                }
                $detailContact = $singlePageDetail->find('div[class=detail-contact]');
                $contact = array();
                if($detailContact){
                    foreach($detailContact as $dKey=>$divValue){
                        $conSpan = $divValue->find('span');
                        if($conSpan){
                            $cNumArray = array();
                            $contactKey = 'Viewing:';
                            foreach($conSpan as $spanKey=>$spanValue){
                                if($spanKey == 0){
                                    $contactKey = $spanValue->plaintext;
                                }else{
                                    $cNumArray[] = $spanValue->plaintext;
                                }
                            }
                            $contact[$contactKey] = $cNumArray;
                        }
                    }
                }
                $res['data'][$i]['Contact'] = $contact;
            }
            $res['i'] = $i;
            $i++;
        }
    }
    return $res;
}

function getPage($singlePageUrl,$log_file_name){
    ini_set('display_errors','on');
    error_reporting(0);
    $singlePage = new simple_html_dom();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $singlePageUrl);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER , true);
    curl_setopt($ch, CURLINFO_HEADER_OUT , false);
    curl_setopt($ch, CURLOPT_HEADER , false);
    curl_setopt($ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_2_0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIESESSION, false);
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie_file.txt');
    $singleDetails = curl_exec($ch);

    $info = curl_getinfo($ch);
    // log
    $txt = 'Took ' . round($info['total_time'],2) . ' seconds to transfer a request to ' . $info['url'];
    setLogData($txt,$log_file_name);

    curl_close($ch);
    $singlePage = $singlePage->load($singleDetails);
    return $singlePage;
}

if($_POST['nextPage'] && isset($_POST['nextPage'])){
    ini_set('display_errors','on');
    error_reporting(0);
    include_once 'simple_html_dom/simple_html_dom.php';
    $nextPage = (int)$_POST['nextPageNo'];
    $lastPage = (int)$_POST['lastPageNo'];
    $nextPageUrl = $_POST['nPageUrl'];
    $log_file_name = $_POST['log_file_name'];
    $json_file_name = $_POST['json_file_name'];
    
    $finalData = array();
    // print_r($_POST);die;
    if($nextPage <= $lastPage){
        // while($nextPage <= $lastPage){
            // log
            $txt = 'Fetching '.$nextPage.' of '.$lastPage.' page';
            setLogData($txt,$log_file_name);

            $i = 0;
            $nextPageUrl = $nextPageUrl.'&ep='.$nextPage;
            $nextPage++;
            $nPageDetails = getPage($nextPageUrl,$log_file_name);
            $nPageHtml = new simple_html_dom();
            $nPageHtml = $nPageHtml->load($nPageDetails);
            if($nPageHtml){
                $resDataNextPage = getData($i,$nPageHtml,$log_file_name,($nextPage-1));
                // print_r($resDataNextPage);
                $finalData = $resDataNextPage['data'];
                // print_r($finalData);
            }
            // log
            $txt = ($nextPage-1).' page successfully fetched! HTTP Code: 200';
            setLogData($txt,$log_file_name);
        // }
    }
    $responseData = array();
    if(!empty($finalData)){
        foreach($finalData as $key=>$data){
            $dataArray = array();
            $title   = $data['itemTitle'];
            $type    = $data['Type '];
            $Address = $data['Address '];
            $Price   = $data['Price']['priceCurrency'].' '.$data['Price']['price'];
            $cName   = 'Not Set';
            $cNumber = 'Not Set';
            if(isset($data['Contact']) && !empty($data['Contact'])){
                $contactDetails = $data['Contact'];
                foreach($contactDetails as $cKey=>$cValue){
                    $cName = $cValue[0];
                    $cNumber = $cValue[1];
                    if($cKey == 'Contact: '){
                        $cName = $cValue[0];
                        $cNumber = $cValue[1];
                    }
                }
            }
            $Available = $data['Available '];

            $dataArray[] = $title;
            $dataArray[] = $type;
            $dataArray[] = $Address;
            $dataArray[] = $Price;
            $dataArray[] = $cName;
            $dataArray[] = $cNumber;
            $dataArray[] = $Available;
            $responseData[$key] = $dataArray;
        }
    }
    $fileName = 'json'.$json_file_name;
    $getJsonData = file_get_contents('logData/'.$fileName);
    $getJsonData = json_decode($getJsonData,true);
    $newData = array_merge($getJsonData,$responseData);
    $resData = setTableData($newData,$json_file_name);
    $response['status'] = 1;
    $response['nextPage'] = (int)$nextPage;
    $response['lastPage'] = (int)$lastPage;
    $response['nPageUrl'] = $_POST['nPageUrl'];
    $response['log_file_name'] = $log_file_name;
    $response['json_file_name'] = $json_file_name;
    $response['data'] = $newData;
    
    echo json_encode($response);
    die;
}

// ------------- clear log file ---------------------
if(isset($_POST['clear_logs'])) {
    rename('logData/logs.txt', 'logData/logs_'.time().'.txt') ; 
    // $_SESSION['message'] = 'Successfully cleared the log file!';
    // header('Location: form.php');
    echo 1;die;
    exit;
}

if(isset($_POST['getLog']) && $_POST['getLog']){
    $configData = file_get_contents('logData/'.$log_file_name);
    echo json_encode(array('html'=>$configData));die;
}
?>