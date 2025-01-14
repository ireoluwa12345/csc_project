<?php
require 'mailer.php';
require 'functions.php';

function checkForSession($sesh_key = "id", $does_exist = true, $rdr = "/home"){
  if($does_exist == true){
    if(isset($_SESSION[$sesh_key])){
        header("location:".$rdr);
    }
  }else{
    if(!isset($_SESSION[$sesh_key])){
      header("location:".$rdr);
    }
  }
}

function phpInputToArray(){
  $json = file_get_contents("php://input");
  $data = json_decode($json, true);

  return $data;
}

function dbBackup($db, $dbUser, $dbPass,$fileDir, $host = "localhost", $port = 3306){


  $res = [];

  try {
    $conn = new PDO('mysql:host='.$host.':'.$port.';',$dbUser,$dbPass);
  } catch (\Exception $e) {
    $res['failed'] = $e->getMessage();
    return $res;
  }

  $stmt = $conn->query("SHOW DATABASES");
  $fetchDB =($stmt->fetchAll(PDO::FETCH_ASSOC));

  $allDatabase = array_column($fetchDB, 'Database');
// var_dump($allDatabase);
  if (!in_array($db, $allDatabase)) {
    // code...
    $res['failed'] = "Database $db does not exist";

  return $res;
  }


  $result=exec('mysqldump '.$db.' --host='.$host.' --port='.$port.' --password='.$dbPass.' --user='.$dbUser.' --single-transaction > '.$fileDir,$output);

  if(empty($output)){
    /* no output is good */
    // echo "empty";
    $filesize = (filesize($fileDir));
    if ($filesize < 0) {
      $res['failed'] = "An error occured please, confirm the database information you passed";
    }elseif(file_exists($fileDir)){
      $res['success'] = "Database backup was successful, your file is $fileDir";
    }else{
      $res['failed'] = "Backup failed";
    }

  }else {
    /* we have something to log the output here*/
    $res['failed'] = "An error occured";
  }

  return $res;
}



  function calculatePercentages($array) {
    $sectionCount = 0;
    $curricullumCount = 0;
    $quizCount = 0;

    foreach ($array as $section) {
      $sectionCount++;
      foreach ($section['curricullums'] as $key => $curricullum) {
        // code...
            $curricullumCount++;
          foreach ($curricullum['quiz'] as $key => $value) {
            $quizCount++;
          }
      }
    }

    $total = $curricullumCount + $quizCount + $sectionCount;

    $sectionPercentage = ($sectionCount / $total) * 100;
    $curricullumPercentage = ($curricullumCount / $total) * 100;
    $quizPercentage = ($quizCount / $total) * 100;

    return [
        'section_percentage' => $sectionPercentage,
        'curricullum_percentage' => $curricullumPercentage,
        'quiz_percentage' => $quizPercentage
    ];
}

function generateAffiliateLink($name) {
    $uniqueIdentifier = uniqid(); // Generate a unique identifier
    $randomString = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5); // Generate a random 5-character alphanumeric string

    // Combine the unique identifier and random string
    $combinedString = $uniqueIdentifier . $randomString;

    // Convert the combined string to a numeric representation
    $numericValue = crc32($combinedString);

    // Convert the numeric value to a base36 representation
    $base36Value = base_convert($numericValue, 10, 36);

    // Create the affiliate link
    $affiliateLink = urlString($name)."-".$base36Value;

    return $affiliateLink;
}

// var_dump(generateAffiliateLink("Afeez Bello"));
// die;

function createDeviceFingerprint()
{
  try {

    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    // $ipAddress = $_SERVER['REMOTE_ADDR'];
    $secChUa = $_SERVER['HTTP_SEC_CH_UA'] ?? "";
    $secChUaPlatform = $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? "";

    $fingerprint = $userAgent . $acceptLanguage . $secChUa . $secChUaPlatform;
    // var_dump($_SERVER); die;

    return md5($fingerprint);
  } catch (\Exception $e) {
    die("Can't Create Device fingerprint");
  }

}

// echo createDeviceFingerprint(); die;
function geoIPInfo($ip)
{
    $response=@file_get_contents('http://www.geoplugin.net/json.gp?ip='.$ip);
    $ipInfo = json_decode($response, true);
    $ipInfo['flag'] ="https://flagsapi.com/".($ipInfo['geoplugin_countryCode'] ?? "NG" )."/flat/64.png";
    // var_dump($response);
    return $ipInfo;
}



function convertCurrencyRate($amount,$rateToConvertTo, $rateToConvertFrom){
  $convertToAlg = $rateToConvertTo/$rateToConvertFrom;
  $rateResult = round(($amount*$convertToAlg), 5);
  // echo $rateToConvertFrom."-".$rateToConvertTo;
  // var_dump($rateToConvertFrom, $rateToConvertTo,$convertToAlg, $rateResult);
  return $rateResult;
}

// convertCurrencyRate(500, 460.78, 1.36);
// die;



// createDeviceFingerprint();die;

function curlGet($url, $headers = []){

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$resp = curl_exec($curl);
curl_close($curl);

var_dump($resp);
$response = json_decode($resp, true);
return $response;
}



function curl_get_contents($url){
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  // curl_setopt ($ch, CURLOPT_PORT , 80);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}



function curlPost($url, $headers = [], $paramData = []){

$paramData = json_encode($paramData);

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);



curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_POSTFIELDS, $paramData);


$resp = curl_exec($curl);
curl_close($curl);

$response = json_decode($resp, true);
return $response;
}


// function array_keys_exists(array $keys, array $arr) {
//    return !array_diff_key(array_flip($keys), $arr);
// }

function array_keys_exists(array $keys, array $arr, $return_value = false) {
  if ($return_value == true) {
    // code...
    $validParams = array_flip(array_diff_key(array_flip($keys), $arr));

    if (count($validParams) > 0) {
      return $validParams;
    }else{
      return true ;
    }
  }else{
    return !array_diff_key(array_flip($keys), $arr);

  }
}

function one_array_key_exists(array $keys, array $arr) {
  $arr = array_keys($arr);
  // var_dump($arr);
  return count(array_intersect($keys, $arr)) > 0;
}

// var_dump(one_array_keys_exists(['name'], ['age'=>1, 'name'=>"Afeez"]));
// die;
function generate_token(int $length = 32): string
{
    // Use cryptographically secure random bytes
    $bytes = random_bytes($length);

    // Convert to base64 for a URL-safe string
    $token = bin2hex($bytes);

    // If necessary, shorten or lengthen the token
    $token = substr($token, 0, $length);

    return $token;
}

function createDateRangeArray($params = null) {

    $defaultDateFrom = date("Y-m-d");
    $defaultDateTo = date("Y-m-t", strtotime($defaultDateFrom));
    $defaultDateFormat = "Y-m-d";

    $defaultDays = [
      'Sunday',
      'Monday',
      'Tuesday',
      'Wednesday',
      'Thursday',
      'Friday',
      'Saturday',
    ];

    if (!isset($params['date_format'])) {
      $params['date_format'] = $defaultDateFormat;
    }elseif($params['date_format'] == null){
      $params['date_format'] = $defaultDateFormat;
    }

    if (!isset($params['days'])) {
      $params['days'] = $defaultDays;
    }

    $params['days'] = array_map('ucwords', $params['days']);

    if (!isset($params['date_to'])) {
      $params['date_to'] = $defaultDateTo;
    }elseif($params['date_to'] == null){
      $params['date_to'] = $defaultDateTo;
    }else{
      $params['date_to'] = date("Y-m-d",strtotime($params['date_to']));

    }
    if (!isset($params['date_from'])) {
      $params['date_from'] = $defaultDateFrom;
    }elseif($params['date_from'] == null){
      $params['date_from'] = $defaultDateFrom;
    }else{
        $params['date_from'] = date("Y-m-d",strtotime($params['date_from']));
    }

    $strDateFrom = $params['date_from'];
    $strDateTo = $params['date_to'];

    // Input two dates in the following formate -> (Y-m-d) and you will be returned an array of dates between the inputs
    $aryRange=array();
    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

    if ($iDateTo>=$iDateFrom) {
      $dayName = date("l", $iDateFrom);
      $dayName2 = date("D", $iDateFrom);

      if (in_array($dayName, $params['days']) || in_array($dayName2, $params['days'])) {
        array_push($aryRange,date($params['date_format'],$iDateFrom));
      }

        while ($iDateFrom<$iDateTo) {
            $iDateFrom+=86400; // add 24 hours
            $dayName = date("l", $iDateFrom);
            $dayName2 = date("D", $iDateFrom);

            if (in_array($dayName, $params['days']) || in_array($dayName2, $params['days'])) {
              array_push($aryRange,date($params['date_format'],$iDateFrom));
            }
        }
    }
    return $aryRange;
}



function timeDateAgo( $time = "00:00:00", $date){


    if ($time == NULL) {
      $time = "00:00:00";
    }

    $date = strtotime($date);
    $time = strtotime($time);

    $timeDate = $date + $time;

    $time_difference = (time()+ strtotime(date("Y-m-d"))) - $timeDate;

    if( $time_difference < 1 ) { return 'few minute ago'; }
    $condition = array( 12 * 30 * 24 * 60 * 60 =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hour',
                60                      =>  'minute',
                1                       =>  'second'
    );

    foreach( $condition as $secs => $str )
    {
        $d = $time_difference / $secs;

        if( $d >= 1 )
        {
            $t = round( $d );
            return $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
        }
    }
}


function loadADMCIcons(){
  echo '

  <link rel="stylesheet" href="/da/assets/fonts/material/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="/da/assets/fonts/fontawesome/css/fontawesome-all.min.css">
  <link rel="stylesheet" href="/da/assets/fonts/flag/css/flag-icon.min.css">
  <link rel="stylesheet" href="/da/assets/fonts/feather/css/feather.css">
  <link rel="stylesheet" href="/da/assets/fonts/datta/datta-icon.css">
  <link rel="stylesheet" href="/da/assets/fonts/simple-line-icons/simple-line-icon.css">
  <link rel="stylesheet" href="/da/assets/fonts/themify/themify.css">
  ';
}


function getLastWord($string){
  return strrchr(trim($string),' ');
}

function returnJSONResponse(array $data, $die = true){
  // var_dump($data);
  $response = json_encode($data);
  echo $response;
  if ($die === true) {
    die;
  }
}


function groupBy($items, $func)
{
    $group = [];
    foreach ($items as $item) {
        if ((!is_string($func) && is_callable($func)) || function_exists($func)) {
            $key = call_user_func($func, $item);
            $group[$key][] = $item;
        } elseif (is_object($item)) {
            $group[$item->{$func}][] = $item;
        } elseif (isset($item[$func])) {
            $group[$item[$func]][] = $item;
        }
    }

    return $group;
}

function cors() {

    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }

}

function ajaxValidate($method = "POST", $acceptedHeaders = ACCEPTED_HEADERS){
    cors();

    if ($acceptedHeaders !== ACCEPTED_HEADERS) {
      $acceptedHeaders = array_merge($acceptedHeaders, ACCEPTED_HEADERS);
    }

    // var_dump($acceptedHeaders);

    $request_headers = getallheaders();
    // die(var_dump($request_headers));

    $explodeHeader = explode(".", $request_headers['Host']);

    if($_SERVER['REQUEST_METHOD'] !== strtoupper($method)){

       http_response_code(405);
       die("Cannot ".$_SERVER['REQUEST_METHOD']." ".$_SERVER['REQUEST_URI']);
       die;
    }

    if(isset($_SERVER['HTTP_ORIGIN'])){

      $cleanOrigin = explode("://", $_SERVER['HTTP_ORIGIN']);
      $cleanOrigin = $cleanOrigin[1];
      // returnJSONResponse(['URL'=>$cleanOrigin]);
      // die;
      if(!in_array($cleanOrigin,$acceptedHeaders) ){
        // die("error 002");
        http_response_code(503);
        die;
      }
    }
}
// End Of AjaxValidate Function

function getInitials($names) {
  $initials = '';
  
  // Explode the string into an array of names
  $nameArray = explode(' ', $names);
  
  foreach ($nameArray as $name) {
      $name = trim($name);
      
      $initials .= substr($name, 0, 1);
  }
  
  return $initials;
}

function removeLastWord($string){
  $position = strrpos(trim($string), ' ');
  $newString = substr(trim($string), 0, $position);

  // var_dump($position);
  return $newString;
}


if (!function_exists('getallheaders')) {
    function getallheaders() {
    $headers = [];
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers;
    }
}



  function fetchData($dbconn = null, string $table = null, array $param = null, array $order = null, $count = false, $fetchBoth = false){
    //Do not add offset if you want to get count only


    $param1 = $dbconn;
    // $param2 = $table;
    // $param3 = $param;
    // $param4 = $order;
    // $param5 = $count;



    $db_error = false;
    $table_error = false;
    $use_default_db = false;
    $use_default_table = false;
    $use_default_param = false;
    $use_default_order = false;
    $use_default_count = false;
    $use_external_query = false;


    if (is_array($param1)) {

      if (isset($param1['db_connection'])) {
        if (is_object($param1['db_connection'])) {
          // code...
        $dbconn = $param1['db_connection'];
        }else{
          $db_error = true;
        }
      }else{
        $use_default_db = true;
      }

      if (isset($param1['external_query'])) {
        $external_query = $param1['external_query'];
      }

      if (isset($param1['table'])) {
        $table = $param1['table'];
      }else{
        $use_default_table = true;
      }

      if (isset($param1['table'])) {
        $table = $param1['table'];
      }else{
        $use_default_table = true;
      }

      if (isset($param1['param'])) {
        $param = $param1['param'];
      }else{
        $use_default_param = true;
      }

      if (isset($param1['order'])) {
        $order = $param1['order'];
      }else{
        $use_default_order = true;
      }

      if (isset($param1['count'])) {
        $count = $param1['count'];
      }else{
        $use_default_count = true;
      }

      if (isset($param1['fetchBoth'])) {
        $fetchBoth = true;
      }


    }else{
        $use_default_db = true;
    }

    if ($db_error) {
      die("Invalid Database Connection");
    }

    if ($use_default_db) {
        $dbconn = db_connection();
    }

    if ($use_default_table) {
        $table = $table;
    }

    if ($use_default_param) {
        $param = $param;
    }

    if ($use_default_order) {
        $order = $order;
    }

    if ($use_default_count) {
        $count = $count;
    }

    // var_dump($dbconn, $param1);
    // die();


    // $table = $table;
    $orderColumnArg = $order['order_column'] ?? false;
    $limitArg = $order['limit'] ?? false;

    $offsetArg = isset($order['offset']) ? $order['offset']: false;

    if ($count == false) {
      $offsetArg = isset($order['offset']) ? $order['offset']: false;
    }

    if (!$limitArg && $offsetArg) {
        $limitCount = $dbconn->prepare("SELECT COUNT(*) AS count FROM $table");
        $limitCount->execute();
        $limitArg = $limitCount->fetch()['count'];
    }

    if (isset($order['type'])) {
        $orderTypeArg = strtoupper($order['type']);
    }else{

      $orderTypeArg = false;
    }
  // var_dump($order, $offsetArg);

    $orderQueryString = "";

    if ($count == true) {
      $query = "SELECT COUNT(*) AS count FROM `$table` ";

    }else{
      $query = "SELECT * FROM `$table` ";
    }

        $bindData = [];


        if ($orderColumnArg) {
            if (is_array($orderColumnArg)) {
                $orderCol = implode(", ", $orderColumnArg);
            }else{
                $orderCol = $orderColumnArg;
            }

            $orderQueryString .= " ORDER BY ".$orderCol;
        }

        if ($orderTypeArg) {
            $orderQueryString .= " ".strtoupper($orderTypeArg);
        }

        if ($limitArg && !is_bool($limitArg)) {
            // $orderQueryString .= " LIMIT $limitArg";
            $orderQueryString .= " LIMIT :arg_limit";
            $bindData['arg_limit'] = $limitArg;

        }

        if ($offsetArg && !is_bool($offsetArg)) {
            // $orderQueryString .= " OFFSET $offsetArg";
            $orderQueryString .= " OFFSET :arg_offset";
            $bindData['arg_offset'] = $offsetArg;

        }

        if ($param != null) {
          if (count($param) > 0) {
          $query.="WHERE ";

              if (isset($param['query_and']) || isset($param['query_or']) || isset($param['query_and_not']) || isset($param['query_not_or'])) {
              $andCols = [];
              $andNotCols= [];
              $orCols = [];
              $orNotCols = [];

              if ($param['query_and'] ?? false) {
                  // code...
                  foreach ($param['query_and'] as $andColKey => $andColValue) {
                      $andCols []= $andColKey." = :".$andColKey."_and";
                      $bindData[$andColKey."_and"] = $andColValue;
                  }
              }

              if ($param['query_and_not'] ?? false) {
                  // code...
                  foreach ($param['query_and_not'] as $andNotColKey => $andNotColValue) {
                      $andNotCols []= $andNotColKey." = :".$andNotColKey."_and_not";
                      $bindData[$andNotColKey."_and_not"] = $andNotColValue;
                  }
              }

              if ($param['query_or'] ?? false) {

                  foreach ($param['query_or'] as $orColKey => $orColValue) {
                      $orCols []= $orColKey." = :".$orColKey."_or";
                      $bindData[$orColKey."_or"] = $orColValue;

                  }
              }

              if ($param['query_or_not'] ?? false) {

                  foreach ($param['query_or_not'] as $orNotColKey => $orNotColValue) {
                      $orNotCols []= $orNotColKey." = :".$orColKey."_or_not";
                      $bindData[$orNotColKey."_or_not"] = $orNotColValue;

                  }
              }



              $andColsString = implode(" AND ", $andCols);
              $andNotColsString = ((count($andNotCols) > 0) ? " NOT " : "" ).implode(" AND NOT ", $andNotCols);
              $orColsString = implode(" OR ", $orCols);
              $orNotColsString = ((count($orNotCols) > 0) ? " NOT " : "" ).implode(" OR NOT ", $orNotCols);

              if (count($andCols) > 0  && count($andNotCols) > 0 && count($orCols) > 0  && count($orNotCols) > 0) {
                  $allColString =  $andColsString." AND ( NOT ".$andNotColsString.")"." AND (".$orColsString.")"." AND ( ".$orNotColsString.")";
              }elseif(count($andCols) > 0  && count($andNotCols) > 0 && count($orCols) > 0){
                  $allColString =  $andColsString." AND (".$andNotColsString.")"." AND (".$orColsString.")";

              }elseif(count($andCols) > 0  && count($andNotCols) > 0){
                  $allColString =  $andColsString." AND (".$andNotColsString.")";

              }elseif(count($andCols) > 0  && count($orCols) > 0){
                  $allColString =  $andColsString." AND (".$orColsString.")";

              }else{
                  $allColString = $andColsString.$andNotColsString.$orColsString.$orNotColsString;
              }


              $query .= $allColString;

          }else{
              $cols = [];

              foreach ($param as $colKey => $colValue) {
                      $cols []= $colKey." = :".$colKey;
                      $bindData[$colKey] = $colValue;

                  }

              $colsString = implode(" AND ", $cols);

              $query .= $colsString;


          }

      }

    }
    $query.= $orderQueryString;

    if (isset($external_query)) {
      $query = $external_query;
    }

    // var_dump($query, $bindData, $orderQueryString);
    // var_dump($query, $bindData);


    $queryStmt = $dbconn->prepare($query);

    foreach ($bindData as $key => $value) {
        if ($key == "arg_limit" || $key == "arg_offset") {
            $value = intval($value);
            $queryStmt->bindValue($key, $value, PDO::PARAM_INT);
        }else{
            // var_dump($value, $key);
            $queryStmt->bindValue($key, $value);
        }

    }
    try {

      $queryStmt->execute();

      if ($count == true) {
        // var_dump($queryStmt->fetch());
        $dataResult = $queryStmt->fetch()['count'];
      }elseif($fetchBoth){
        $dataResult = $queryStmt->fetchAll(PDO::FETCH_BOTH);
      }else{
        $dataResult = $queryStmt->fetchAll(PDO::FETCH_ASSOC);
      }

    $queryStmt->closeCursor();

    } catch (Exception $e) {
        if ($PRODUCTION_MODE ?? true) {
            // echo $queryStmt->queryString;
            die($e->getMessage());
        }else{
            die("An error occured check passed data.");
        }
      // $dataResult = ["error"=>"An error occured"];
    }

     // $queryStmt->debugDumpParams();
     // echo $queryStmt->queryString;
     // var_dump( $queryStmt->errorInfo());
    // var_dump($query, $queryStmt, $bindData);
    // var_dump($dataResult);
    return $dataResult;

  }


  function updateData($dbconn, $table, array $set , array $where = null){
    //Do not add offset if you want to get count only

      // $dbconn = connection();
      if (empty($set)) {
        $dataResult = ["error"=>"No Parameter To Update"];

      }else{

        $bindData = [];

        $setArr = [];

        foreach ($set as $setKey => $setValue) {
          $setArr []= $setKey." = :".$setKey;
          $bindData[$setKey] = $setValue;
        }

        $setDataSting = implode(", ", $setArr);

        $query = "UPDATE `$table` SET ".$setDataSting;




        if ($where != null) {
          if (count($where) > 0) {
            $query.=" WHERE ";

            if (isset($where['query_and']) || isset($where['query_or']) || isset($where['query_and_not']) || isset($where['query_not_or'])) {
              $andCols = [];
              $andNotCols= [];
              $orCols = [];
              $orNotCols = [];

              if ($where['query_and'] ?? false) {
                // code...
                foreach ($where['query_and'] as $andColKey => $andColValue) {
                  $andCols []= $andColKey." = :".$andColKey."_and";
                  $bindData[$andColKey."_and"] = $andColValue;
                }
              }

              if ($where['query_and_not'] ?? false) {
                // code...
                foreach ($where['query_and_not'] as $andNotColKey => $andNotColValue) {
                  $andNotCols []= $andNotColKey." = :".$andNotColKey."_and_not";
                  $bindData[$andNotColKey."_and_not"] = $andNotColValue;
                }
              }

              if ($where['query_or'] ?? false) {

                foreach ($where['query_or'] as $orColKey => $orColValue) {
                  $orCols []= $orColKey." = :".$orColKey."_or";
                  $bindData[$orColKey."_or"] = $orColValue;

                }
              }

              if ($where['query_or_not'] ?? false) {

                foreach ($where['query_or_not'] as $orNotColKey => $orNotColValue) {
                  $orNotCols []= $orNotColKey." = :".$orColKey."_or_not";
                  $bindData[$orNotColKey."_or_not"] = $orNotColValue;

                }
              }



              $andColsString = implode(" AND ", $andCols);
              $andNotColsString = ((count($andNotCols) > 0) ? " NOT " : "" ).implode(" AND NOT ", $andNotCols);
              $orColsString = implode(" OR ", $orCols);
              $orNotColsString = ((count($orNotCols) > 0) ? " NOT " : "" ).implode(" OR NOT ", $orNotCols);

              if (count($andCols) > 0  && count($andNotCols) > 0 && count($orCols) > 0  && count($orNotCols) > 0) {
                $allColString =  $andColsString." AND ( NOT ".$andNotColsString.")"." AND (".$orColsString.")"." AND ( ".$orNotColsString.")";
              }elseif(count($andCols) > 0  && count($andNotCols) > 0 && count($orCols) > 0){
                $allColString =  $andColsString." AND (".$andNotColsString.")"." AND (".$orColsString.")";

              }elseif(count($andCols) > 0  && count($andNotCols) > 0){
                $allColString =  $andColsString." AND (".$andNotColsString.")";

              }elseif(count($andCols) > 0  && count($orCols) > 0){
                $allColString =  $andColsString." AND (".$orColsString.")";

              }else{
                $allColString = $andColsString.$andNotColsString.$orColsString.$orNotColsString;
              }


              $query .= $allColString;

            }else{
              $cols = [];

              foreach ($where as $colKey => $colValue) {
                $cols []= $colKey." = :".$colKey;
                $bindData[$colKey] = $colValue;

              }

              $colsString = implode(" AND ", $cols);

              $query .= $colsString;


            }

          }

        }else{
          return false;
        }

        // var_dump($query, $bindData, $orderQueryString);
        // var_dump($query, $bindData); die;


        $queryStmt = $dbconn->prepare($query);

        foreach ($bindData as $key => $value) {
          if ($key == "arg_limit" || $key == "arg_offset") {
            $value = intval($value);
            $queryStmt->bindValue($key, $value, PDO::PARAM_INT);
          }else{
            // var_dump($value, $key);
            $queryStmt->bindValue($key, $value);
          }

        }
        try {

          if($queryStmt->execute()){
            return true;
          }else{
            return false;
          }

        } catch (Exception $e) {
          if ($PRODUCTION_MODE ?? true) {
            // echo $queryStmt->queryString;
            die($e->getMessage());
          }else{
            die("An error occured check passed data.");
          }
          // $dataResult = ["error"=>"An error occured"];
        }
      }


}

function deleteData($dbconn, $table, array $where = null, $deleteOnEmptyParam = false){
  //Do not add offset if you want to get count only

    // $dbconn = connection();

    if (empty($where) && !$deleteOnEmptyParam) {
      return ["error"=>"No Parameter To Delete From"];

    }else{

      $bindData = [];


      $query = "DELETE FROM `$table` ";




      if ($where != null) {
        if (count($where) > 0) {
          $query.=" WHERE ";

          if (isset($where['query_and']) || isset($where['query_or']) || isset($where['query_and_not']) || isset($where['query_not_or'])) {
            $andCols = [];
            $andNotCols= [];
            $orCols = [];
            $orNotCols = [];

            if ($where['query_and'] ?? false) {
              // code...
              foreach ($where['query_and'] as $andColKey => $andColValue) {
                $andCols []= $andColKey." = :".$andColKey."_and";
                $bindData[$andColKey."_and"] = $andColValue;
              }
            }

            if ($where['query_and_not'] ?? false) {
              // code...
              foreach ($where['query_and_not'] as $andNotColKey => $andNotColValue) {
                $andNotCols []= $andNotColKey." = :".$andNotColKey."_and_not";
                $bindData[$andNotColKey."_and_not"] = $andNotColValue;
              }
            }

            if ($where['query_or'] ?? false) {

              foreach ($where['query_or'] as $orColKey => $orColValue) {
                $orCols []= $orColKey." = :".$orColKey."_or";
                $bindData[$orColKey."_or"] = $orColValue;

              }
            }

            if ($where['query_or_not'] ?? false) {

              foreach ($where['query_or_not'] as $orNotColKey => $orNotColValue) {
                $orNotCols []= $orNotColKey." = :".$orColKey."_or_not";
                $bindData[$orNotColKey."_or_not"] = $orNotColValue;

              }
            }



            $andColsString = implode(" AND ", $andCols);
            $andNotColsString = ((count($andNotCols) > 0) ? " NOT " : "" ).implode(" AND NOT ", $andNotCols);
            $orColsString = implode(" OR ", $orCols);
            $orNotColsString = ((count($orNotCols) > 0) ? " NOT " : "" ).implode(" OR NOT ", $orNotCols);

            if (count($andCols) > 0  && count($andNotCols) > 0 && count($orCols) > 0  && count($orNotCols) > 0) {
              $allColString =  $andColsString." AND ( NOT ".$andNotColsString.")"." AND (".$orColsString.")"." AND ( ".$orNotColsString.")";
            }elseif(count($andCols) > 0  && count($andNotCols) > 0 && count($orCols) > 0){
              $allColString =  $andColsString." AND (".$andNotColsString.")"." AND (".$orColsString.")";

            }elseif(count($andCols) > 0  && count($andNotCols) > 0){
              $allColString =  $andColsString." AND (".$andNotColsString.")";

            }elseif(count($andCols) > 0  && count($orCols) > 0){
              $allColString =  $andColsString." AND (".$orColsString.")";

            }else{
              $allColString = $andColsString.$andNotColsString.$orColsString.$orNotColsString;
            }


            $query .= $allColString;

          }else{
            $cols = [];

            foreach ($where as $colKey => $colValue) {
              $cols []= $colKey." = :".$colKey;
              $bindData[$colKey] = $colValue;

            }

            $colsString = implode(" AND ", $cols);

            $query .= $colsString;


          }

        }

      }

      // var_dump($query, $bindData, $orderQueryString);
      // var_dump($query, $bindData); die;


      $queryStmt = $dbconn->prepare($query);

      foreach ($bindData as $key => $value) {
        if ($key == "arg_limit" || $key == "arg_offset") {
          $value = intval($value);
          $queryStmt->bindValue($key, $value, PDO::PARAM_INT);
        }else{
          // var_dump($value, $key);
          $queryStmt->bindValue($key, $value);
        }

      }
      try {

        if($queryStmt->execute()){
          return true;
        }else{
          return false;
        }

      } catch (Exception $e) {
        if ($PRODUCTION_MODE ?? true) {
          // echo $queryStmt->queryString;
          die($e->getMessage());
        }else{
          die("An error occured check passed data.");
        }
        // $dataResult = ["error"=>"An error occured"];
      }
    }


}

//
// var_dump(deleteData($conn, 'aws_files', [], true));
// die;

// updateData($conn, 'read_users', ['visibility'=>"show"], ['id'=>2]);
// die;

function ADMC_FONT_PLUGIN(){
echo'
  <link rel="stylesheet"  href="/da/assets/fonts/material/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="/da/assets/fonts/fontawesome/css/fontawesome-all.min.css">
  <link rel="stylesheet" href="/da/assets/fonts/flag/css/flag-icon.min.css">
  <link rel="stylesheet" href="/da/assets/fonts/feather/css/feather.css">
  <link rel="stylesheet" href="/da/assets/fonts/datta/datta-icon.css">
  <link rel="stylesheet" href="/da/assets/fonts/simple-line-icons/simple-line-icon.css">
  <link rel="stylesheet" href="/da/assets/fonts/themify/themify.css">
';

}

function insertData( $dbconn, string $table, array $param){
  if (count($param) < 1) {
    return false;
  }else{
    $paramKeys = array_keys($param);
    $columns = "`".implode("`,`", $paramKeys)."`";
    $columnsToken = ":".implode(", :", $paramKeys);

    $query = "INSERT INTO `$table`($columns) VALUES($columnsToken)";

    $stmt = $dbconn->prepare($query);
    if($stmt->execute($param)){
      return true;
    }else{
      return false;
    }
    // var_dump($columns, $columnsToken, $query);
  }

}



function generateRandomString($length = 8) {
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
    $string = "";

    for ($p = 0; $p < $length; $p++) {
        @$string .= $characters[mt_rand(0, strlen($characters))];
    }

    return $string;
}

function cleanDecodeDate ($date = null, $type = null) {
  if ($date == null) {
    $date = date("Y-m-d");
  }

  if ($type != null) {
    $type = strtoupper($type);
  }

  if (is_numeric($date)) {
    $dateToInteger = $date;
  }else{

    $dateToInteger = strtotime($date);
  }

  $returnDate = [];

  $returnDate['hour'] =  date("h", $dateToInteger);
  $returnDate['minutes'] =  date("i", $dateToInteger);
  $returnDate['seconds'] =  date("s", $dateToInteger);
  $returnDate['meridiem'] =  date("A", $dateToInteger);

  $returnDate['day_abbr'] = date("D", $dateToInteger);
  $returnDate['day'] = date("l", $dateToInteger);
  $returnDate['day_int'] = date("d", $dateToInteger);
  $returnDate['day_int_suffix'] = date("jS", $dateToInteger);
  $returnDate['day_in_year'] = date("z", $dateToInteger);

  $returnDate['day_in_week'] = date("N", $dateToInteger);

  $returnDate['month_abbr'] = date("M", $dateToInteger);
  $returnDate['month_int'] = date("m", $dateToInteger);
  $returnDate['month'] = date("F", $dateToInteger);
  $returnDate['month_count'] = date("t", $dateToInteger);

  $returnDate['year'] = date("Y", $dateToInteger);
  $returnDate['year_short'] = date("y", $dateToInteger);


  if ($type == "FULL") {
    return $returnDate;
  }elseif($type == "FULL_TIME"){
    return $returnDate['hour'].":".$returnDate['minutes'].":".$returnDate['seconds']." ".$returnDate['meridiem'];
  }elseif($type == "TIME"){
    return $returnDate['hour'].":".$returnDate['minutes']." ".$returnDate['meridiem'];
  }else{
    return $returnDate['day_abbr'].", ".$returnDate['day_int']." ".$returnDate['month_abbr']." ".$returnDate['year'];
  }


}


function urlString($urlToString){
  $urlString = (str_replace([' ', '+'], '-', strtolower(urlencode($urlToString))));
  return $urlString;
}



function stringStartWith($string, $stringToSearch){
    if(strpos($string, $stringToSearch) === 0){
      return true;
    }else{
      return false;
    }

  }

function calculateReadingTime($wordCount, $readingSpeed = 200) {
  $minutes = $wordCount / $readingSpeed;
  return ceil($minutes);
}

function wordCount($string){
  $words = preg_split("/\s+/", $string);
  $wordCount = count($words);
  return $wordCount;
}

function previewBodyWithElipsces($string, $count = 50, $strip = true, $url_param = ['link'=>'', 'color'=>"white"]){
  if ($strip == true) {
    $string = strip_tags($string);
  }

  $original_string = $string;

  // var_dump($original_string);

  $words = explode(' ', $original_string);

  if (isset($url_param['color'])) {
    $url_color = "style='color:".$url_param['color']." !important;'";
  }else{
    $url_color = "";
  }

  if (isset($url_param['text'])) {
    // code...
    $url_text = $url_param['text'];
  }else{
    $url_text = "read more";
  }

  if (isset($url_param['link'])) {
    if (!empty($url_param['link'])) {
      // code...
      $url = " <a href='".$url_param['link']."'".$url_color.">".$url_text."</a>";
    }else{
      $url = "";
    }
  }else{
    $url = "";
  }


// var_dump(count($words), $count);
  if(count($words) > $count){
    $words = array_slice($words, 0, $count);
    $string = implode(' ', $words)."...";
  }
    return $string.$url;

}






function decodeDate($date){
  $split = explode('-',$date);
  $month = $split[1];
  $day = $split[2];
  $year = $split[0];
  if($month == 1 ){
    $month = "January";
  }
  if($month == 2 ){
    $month = "February";
  }
  if($month == 3 ){
    $month = "March";
  }
  if($month == 4){
    $month = "April";
  }
  if($month == 5){
    $month = "May";
  }
  if($month == 6 ){
    $month = "June";
  }
  if($month == 7 ){
    $month = "July";
  }
  if($month == 8 ){
    $month = "August";
  }
  if($month == 9 ){
    $month = "September";
  }
  if($month == 10 ){
    $month = "October";
  }
  if($month == 11 ){
    $month = "November";
  }
  if($month == 12 ){
    $month = "December";
  }
  $newDate = $month.' '.$day.', '.$year;
  return $newDate;
}

function convertColor($color, $opacity){
  // if (count($color) > 4) {
  $splitt = str_split($color, 2);

$r = hexdec($splitt[0])+40;
$g = hexdec($splitt[1])+40;
$b = hexdec($splitt[2])+17;
  // die(var_dump($g));

$converted = "rgb(" . $r . ", " . $g . ", " . $b . ",". $opacity.")";
return $converted;
}




function ForumInfo($dbconn,$sess){
  $stmt = $dbconn->prepare("SELECT * FROM users WHERE hash_id = :sid");
  $data = [
    ':sid' => $sess
  ];
  $stmt->execute($data);
  $row = $stmt->fetch(PDO::FETCH_BOTH);
  return $row;
}
function insert($conn, $table, $parameters){

  array_pop($parameters);
  // var_dump($parameters);
  $sql = sprintf('INSERT INTO %s (%s) VALUES(%s)',
  $table,
  implode(', ',array_keys($parameters)), ':'.implode(',:',array_keys($parameters))
);
// //die(var_dump($sql));
$stmt =  $conn->prepare($sql);
$stmt->execute($parameters);
}
// function displayErrors($error, $field)
// {
//   $result= "";
//   if (isset($error[$field]))
//   {
//     $result = '<span style="color:red">'.$error[$field].'</span>';
//   }
//   return $result;
// }

function columnSummation($conn,$column,$table){
  $stmt = $conn->prepare("SELECT $column FROM $table");
  $stmt->execute();
  $plus = 0;
  // die(var_dump($row = $stmt->fetch(PDO::FETCH_BOTH)));
  while($row = $stmt->fetch(PDO::FETCH_BOTH)){
    $plus += $row[$column];
  }
  // die(var_dump($plus));
  return $plus;
}
function columnPrySummation($conn,$column,$table){
  $pry = "primary";
  $stmt = $conn->prepare("SELECT $column FROM $table WHERE category=:pry");

  $stmt->bindParam(":pry",$pry);
  $stmt->execute();
  $plus = 0;
  // die(var_dump($row = $stmt->fetch(PDO::FETCH_BOTH)));
  while($row = $stmt->fetch(PDO::FETCH_BOTH)){
    $plus += $row[$column];
  }
  // die(var_dump($plus));
  return $plus;
}

function totalCount($conn,$table){
  $stmt = $conn->prepare("SELECT * FROM $table");
  $stmt->execute();
  return $stmt->rowCount();
}
function totalPryCount($conn,$table){
  $pry = "primary";
  $stmt = $conn->prepare("SELECT * FROM $table WHERE category=:pry");
  $stmt->bindParam(":pry",$pry);
  $stmt->execute();
  return $stmt->rowCount();
}

function totalCategoryCount($conn,$table,$column,$value){
  $stmt = $conn->prepare("SELECT * FROM $table WHERE $column=:value");
  $stmt->bindParam(':value',$value);
  $stmt->execute();
  return $stmt->rowCount();
}

function cleans($string){
  $string = str_replace(array('[\', \']'), '', $string);
  $string = preg_replace('/\[.*\]/U', '', $string);
  $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
  $string = htmlentities($string, ENT_COMPAT, 'utf-8');
  $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
  $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
  return strtolower(trim($string, '-'));
}









function cleanTime($time){
  $timestamp = strtotime($time) + 60*60;

  $time = date('H:i:s', $timestamp);
  return $time;

}
function getForumCategory($dbconn){
  $stmt = $dbconn->prepare("SELECT * FROM category");
  $stmt->execute();
  while($row = $stmt->fetch(PDO::FETCH_BOTH)){
    extract($row);

    echo '<li><a href="/category/'.$hash_id.'">'.$category_name.' &nbsp;<span class="badge pull-right">'.categoryCount($dbconn,$row['hash_id']).'</span></a></li>';
  }
}
function getMyForum($dbconn,$id){
  $stmt = $dbconn->prepare("SELECT * FROM topic WHERE user_id=:hid");
  $stmt->bindParam(":hid",$id);
  $stmt->execute();
  while($row = $stmt->fetch(PDO::FETCH_BOTH)){
    extract($row);
    $post = cleans($title);
    echo '<div class="divline"></div>
    <div class="blocktxt">
    <a href="/topic/'.$post.'-'.$row['hash_id'].'">'.strtoupper(strtolower($row['title'])).'</a>
    </div>';
  }
}

function previewBody($string, $count){
  $original_string = $string;
  $words = explode(' ', $original_string);
  if(count($words) > $count){
    $words = array_slice($words, 0, $count);
    $string = implode(' ', $words)."...";
  }
  return strip_tags($string);
}


function insertSafe($conn, $table, $parameters){
  try {
    // array_pop($parameters);
    // var_dump($parameters);
    $sql = sprintf('INSERT INTO %s (%s) VALUES(%s)',
    $table,
    implode(', ',array_keys($parameters)), ':'.implode(',:',array_keys($parameters))
  );
  // //die(var_dump($sql));
  $stmt =  $conn->prepare($sql);
  $stmt->execute($parameters);
} catch (PDOException $e) {
  die($e);
  die("Error: Try again After Some Times");
}
}
function insertContent($conn, $table, $parameters){
  try {

    // var_dump($parameters);
    $sql = sprintf('INSERT INTO %s (%s) VALUES(%s)',
    $table,
    implode(', ',array_keys($parameters)), ':'.implode(',:',array_keys($parameters))
  );
  // //die(var_dump($sql));
  $stmt =  $conn->prepare($sql);
  $stmt->execute($parameters);
} catch (PDOException $e) {
  die($e);
  // die("Error: Try again After Some Times");
}
}

// function update($dbconn, $table, $parameters,$column,$value,$locat){
//
//
// try {
//   function getVal($param){
//   $result = [];
//   foreach($param as $col => $val){
//       $result[] = "$col = :$col";
//     }
//     $new = implode(', ', $result);
//     return $new;
// }
//   function getVal2($param){
//   $result = [];
//   foreach($param as $col => $val){
//       $result[] = "$col = :$col";
//     }
//     $new = implode(' AND ', $result);
//     return $new;
// }
//
//
// array_pop($parameters);
// $what = getVal($parameters);
// $vall = getVal2($value);
//
//   // var_dump($parameters);
//   $sql = sprintf('UPDATE %s SET %s',
//       $table, $what
//   );
//   $sql .= " WHERE ".$vall;
//   // //die(var_dump($sql));
// $stmt =  $dbconn->prepare($sql);
// $newt = $parameters + $value;
// // die(var_dump($newt));
// $stmt->execute($newt);
// } catch (PDOException $e) {
//   die("Error Occured");
// }
//
// if($table == "admin"){
//   $success = "Profile Successfully Edited";
//   $succ = preg_replace('/\s+/', '_', $success);
//   header("Location:/$locat");
// }else {
//   $success = "Edited";
//   $succ = preg_replace('/\s+/', '_', $success);
//   header("Location:/$locat?success=$succ");
// }
//
//
//
// }


// function compressImage($files, $name, $quality, $upDIR ) {
//   // die(var_dump($files[$name]['type']));
//   $rnd = rand(0000000, 9999999);
//   $strip_name = preg_replace("/[^.a-zA-Z0-9]/", "_",$_FILES[$name]['name'] );
//   $filename = time()."mail".$strip_name;
//   $destination_url = $upDIR.$filename;
//   $info = getimagesize($files[$name]['tmp_name']);
//   if ($info['mime'] == 'image/jpeg')
//   $image = imagecreatefromjpeg($files[$name]['tmp_name']);
//   elseif ($info['mime'] == 'image/gif')
//   $image = imagecreatefromgif($files[$name]['tmp_name']);
//   elseif ($info['mime'] == 'image/png')
//   $image = imagecreatefrompng($files[$name]['tmp_name']);
//   imagejpeg($image, $destination_url, $quality);
//   $img['upload'] = $destination_url;
//   return $img;
// }
function compressImage2($files, $name, $quality, $upDIR ) {
  // die(var_dump($files[$name]['type']));

  $rnd = rand(0000000, 9999999);
  $strip_name = preg_replace("/[^.a-zA-Z0-9]/", "_",$_FILES[$name]['name'] );
  $filename = time()."mail".$strip_name;
  $destination_url = $upDIR.$filename;
  $thumb_url = 'thumbs/'.$filename;
  $info = getimagesize($files[$name]['tmp_name']);
  if ($info['mime'] == 'image/jpeg')
  $image = imagecreatefromjpeg($files[$name]['tmp_name']);
  elseif ($info['mime'] == 'image/gif')
  $image = imagecreatefromgif($files[$name]['tmp_name']);
  elseif ($info['mime'] == 'image/png')
  $image = imagecreatefrompng($files[$name]['tmp_name']);


  imagejpeg($image, $destination_url, 90);
  imagejpeg($image, $thumb_url,30);
  $img['upload'] = $destination_url;
  $img['thumb'] = $thumb_url;
  return $img;
}

function selectTableContent2($dbconn,$table,$column,$columnWhere){
  $vall = formatWhere($columnWhere);
  $column = implode(',',$column);
  try{

    // $what = getVal($parameters);

    // var_dump($parameters);
    $sql = sprintf('SELECT %s FROM %s',
    $column,$table
  );

  if(count($columnWhere) > 0){
    $sql .= " WHERE ".$vall;
  }

  // die(var_dump($sql));
  $stmt =  $dbconn->prepare($sql);
  $newt = $columnWhere;
  // die(var_dump($newt));
  if(count($columnWhere) > 0){
    $stmt->execute($newt);
  }else{
    $stmt->execute();
  }

  // $result = [];
  $row = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);




  return $row;
} catch (PDOException $e) {
  die("Error Occured");
}
}

function deleteContent($dbconn,$table,$columnWhere){

  // die($columnWhere);
  try {

    // $what = getVal($parameters);
    $vall = formatWhere($columnWhere);

    // var_dump($parameters);
    $sql = sprintf('DELETE FROM %s',
    $table
  );
  $sql .= " WHERE ".$vall;


  //die(var_dump($sql));
  $stmt =  $dbconn->prepare($sql);
  $newt = $columnWhere;
  // die(var_dump($newt));

  $stmt->execute($newt);

} catch (PDOException $e) {
  die("Error Occured");
}

}

function selectContent($dbconn,$table,$columnWhere){
  $vall = formatWhere($columnWhere);
  try{

    // $what = getVal($parameters);

    // var_dump($parameters);
    $sql = sprintf('SELECT * FROM %s',
    $table
  );

  if(count($columnWhere) > 0){
    $sql .= " WHERE ".$vall;
  }

  //die(var_dump($sql));
  $stmt =  $dbconn->prepare($sql);
  $newt = $columnWhere;
  // die(var_dump($newt));
  if(count($columnWhere) > 0){
    $stmt->execute($newt);
  }else{
    $stmt->execute();
  }

  $result = [];
  while($row = $stmt->fetch(PDO::FETCH_BOTH)){
    $result[] = $row;
  }

  return $result;
} catch (PDOException $e) {
  die($e);
  die("Error Occured");
}
}
function selectContentDesc($dbconn,$table,$columnWhere,$order,$limit){
  $vall = formatWhere($columnWhere);
  try{

    // $what = getVal($parameters);

    // var_dump($parameters);
    $sql = sprintf('SELECT * FROM %s',
    $table
  );

  if(count($columnWhere) > 0){
    $sql .= " WHERE ".$vall;
  }
  $sql.= " ORDER BY ".$order." DESC LIMIT ".$limit;

  //die(var_dump($sql));
  $stmt =  $dbconn->prepare($sql);
  $newt = $columnWhere;
  // die(var_dump($newt));
  if(count($columnWhere) > 0){
    $stmt->execute($newt);
  }else{
    $stmt->execute();
  }

  $result = [];
  while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $result[] = $row;
  }

  return $result;
} catch (PDOException $e) {
  die($e);
  die("Error Occured");
}
}
function selectContentAsc($dbconn,$table,$columnWhere,$order,$limit){
  $vall = formatWhere($columnWhere);
  try{

    // $what = getVal($parameters);

    // var_dump($parameters);
    $sql = sprintf('SELECT * FROM %s',
    $table
  );

  if(count($columnWhere) > 0){
    $sql .= " WHERE ".$vall;
  }
  $sql.= " ORDER BY ".$order." ASC LIMIT ".$limit;

  //die(var_dump($sql));
  $stmt =  $dbconn->prepare($sql);
  $newt = $columnWhere;
  // die(var_dump($newt));
  if(count($columnWhere) > 0){
    $stmt->execute($newt);
  }else{
    $stmt->execute();
  }

  $result = [];
  while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $result[] = $row;
  }

  return $result;
} catch (PDOException $e) {
  // die($e);
  die("Error Occured");
}
}
function selectTableContent($dbconn,$table,$column,$columnWhere){
  $vall = formatWhere($columnWhere);
  $column = implode(',',$column);
  try{

    // $what = getVal($parameters);

    // var_dump($parameters);
    $sql = sprintf('SELECT %s FROM %s',
    $column,$table
  );

  if(count($columnWhere) > 0){
    $sql .= " WHERE ".$vall;
  }

  // die(var_dump($sql));
  $stmt =  $dbconn->prepare($sql);
  $newt = $columnWhere;
  // die(var_dump($newt));
  if(count($columnWhere) > 0){
    $stmt->execute($newt);
  }else{
    $stmt->execute();
  }

  $result = [];
  while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $result[] = $row;
  }

  return $result;
} catch (PDOException $e) {
  die("Error Occured");
}
}


function formatParam($param){
  $result = [];
  foreach($param as $col => $val){
    $result[] = "$col = :$col";
  }
  $new = implode(', ', $result);
  return $new;
}
function formatWhereParam($param){
  $result = [];
  foreach($param as $col => $val){
    $cola = $col."a";
    $result[$cola] = $val;
  }
  // $new = implode(', ', $result);
  return $result;
}
function formatWhere($param){
  $result = [];
  foreach($param as $col => $val){
    $result[] = "$col = :$col";
  }
  $new = implode(' AND ', $result);
  return $new;
}
function formatPutWhere($param){
  $result = [];
  foreach($param as $col => $val){
    $result[] = "$col = :$col"."a";
  }
  $new = implode(' AND ', $result);
  return $new;
}

function updateContent($dbconn, $table, $parameters,$columnWhere){
  try {



  // array_pop($parameters);
  $what = formatParam($parameters);
  $columnWhere2 = formatWhereParam($columnWhere);
  $vall = formatPutWhere($columnWhere);

    // var_dump($parameters);
    $sql = sprintf('UPDATE %s SET %s',
        $table, $what
    );
    $sql .= " WHERE ".$vall;
    // var_dump($sql);
  $stmt =  $dbconn->prepare($sql);
  $newt = $parameters + $columnWhere2;
  // die(var_dump($newt));
  $stmt->execute($newt);
  } catch (PDOException $e) {
    if (isset($_SESSION['debug'])) {
    die($e);
  }else{
      die("Error: Try again After Some Times");
  }
  }
}

function numberToRomanRepresentation($number) {
    $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
    $returnValue = '';
    while ($number > 0) {
        foreach ($map as $roman => $int) {
            if($number >= $int) {
                $number -= $int;
                $returnValue .= $roman;
                break;
            }
        }
    }
    return $returnValue;
}



function say($value){
  echo "<p style='color:red'>*".$value."</p>";
}
function commentCount($dbconn,$hid){
  $stmt = $dbconn->prepare("SELECT * FROM reply WHERE topic_id=:ti");
  $stmt->bindParam(":ti",$hid);
  $stmt->execute();
  return $stmt->rowCount();
}
function categoryCount($dbconn,$hid){
  $stmt = $dbconn->prepare("SELECT * FROM topic WHERE category=:ti");
  $stmt->bindParam(":ti",$hid);
  $stmt->execute();
  return $stmt->rowCount();
}



function base64url_encode($s) {
  return str_replace(array('+', '/'), array('-', '_'), base64_encode($s));
}

function base64url_decode($s) {
  return base64_decode(str_replace(array('-', '_'), array('+', '/'), $s));
}

function authenticate($session, $url){
  if(!isset ($session)){
    header("Location: /$url?err=You_have_not_logged_in");
  }
}

function shortContent($content){
 $body = $content;
 $string = strip_tags($body);
 if (strlen($string) > 50){
   $stringCut = substr($string, 0, 50);
   $endPoint = strrpos($stringCut, ' ');
   $string = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
   $string .= '...';
 }
 return $string;
}
function selectContentDescPagination($dbconn,$table,$columnWhere,$order,$offset,$limit){
$vall = formatWhere($columnWhere);
try{
  // $what = getVal($parameters);
  // var_dump($parameters);
  $sql = sprintf('SELECT * FROM %s',
  $table
);
if(count($columnWhere) > 0){
  $sql .= " WHERE ".$vall;
}
$sql.= " ORDER BY ".$order." DESC LIMIT ".$offset.", ".$limit;
//die(var_dump($sql));
$stmt =  $dbconn->prepare($sql);
$newt = $columnWhere;
// die(var_dump($newt));
if(count($columnWhere) > 0){
  $stmt->execute($newt);
}else{
  $stmt->execute();
}
$result = [];
while($row = $stmt->fetch(PDO::FETCH_BOTH)){
  $result[] = $row;
}
return $result;
} catch (PDOException $e) {
die($e);
die("Error Occured");
}
}


function includeFile($filePath){
  $includedFIles = get_included_files();
  $fullFilePath = APP_PATH."/".$filePath;

  if (!in_array($fullFilePath, $includedFIles)) {
    if (file_exists($fullFilePath)) {
      global $GLOBALS;
      extract($GLOBALS);
      include $fullFilePath;
    }else{
      die("Invalid File Path: ". $fullFilePath);
    }
  }
}

function getFileNameFromUrl($url){
  $txt = explode("/", $url);
  $file_name = explode("_", $txt[4]);

  $arrayToRemove = [$file_name[0]];

  $remainingArrays = array_diff($file_name, $arrayToRemove);
  // var_dump($remainingArrays);
  // die;

  $resultString = implode('_', $remainingArrays);

  return $resultString;


}
?>

