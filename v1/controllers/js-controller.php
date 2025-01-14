<?php
header('Content-Type: text/javascript');
?>
<?php if (false): ?>
<script type="text/javascript">
<?php endif; ?>
function ajaxPost(url,param,cb){

var param = JSON.stringify(param);

var xhr = new XMLHttpRequest();
xhr.onreadystatechange = function(){
  if(xhr.readyState == 4 && xhr.status == 200){
return cb("NULL",xhr.responseText);
  }
}

xhr.open("POST",url,true);
xhr.setRequestHeader("Content-Type","application/json");
xhr.send(param);

}


function ajaxGet(url,cb){
    
var xhr = new XMLHttpRequest();
xhr.onreadystatechange = function(){
  if(xhr.readyState == 4 && xhr.status == 200){
return cb("NULL",xhr.responseText);
// console.log(result);
  }
}

xhr.open("GET",url,true);
xhr.setRequestHeader("Content-Type","application/json");
xhr.send();
}
<?php if (false): ?>
</script>
<?php endif; ?>
