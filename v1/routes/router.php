<?php
$uri = explode("/",$_SERVER['REQUEST_URI']);

if (count($uri) > 2) {


  if (!empty($_GET)) {
  $query_string = explode("?",$uri[2])[1];
}else{
  $query_string = "";
}

  switch ($uri[1]."/".$uri[2]) {
    // case 'instructor/'.'signup':
    //   include APP_PATH."/views/instructors/signup.php";
    //   die;
    //   break;
  }



}else{
  if (!empty($_GET)) {
  $query_string = explode("?",$uri[1])[1];
}else{
  $query_string = "";
}
  switch ($uri[1]) {
    case 'test':
    include APP_PATH."/views/test.php";
    break;
    case 'test?'.$query_string:
    include APP_PATH."/views/test.php";
    break;

  case '':
    include APP_PATH."/views/home.php";
    die;
    break;

  case 'infer':
    include APP_PATH."/views/infer.php";
    die;
    break;
}


}

?>