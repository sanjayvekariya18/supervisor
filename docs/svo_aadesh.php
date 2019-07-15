<?php

// WORKING CURL

// TESTING CURL FOR Supervisor Start
$url = 'supervisor07.com:5734/v1/acquire?access_token=SECRET_TOKEN&c_id=CUST001&m_id='.$m_no.'&shift='.$shift.'&stitches='.$stich.'&tb='.$thred_break.'&rpm='.$rpm.'&head='.$head.'&st='.$time_machine;
try {
    ini_set('allow_url_fopen', 1);
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_FAILONERROR => true,
        CURLOPT_URL => $url,
        
    ]);
    
    $result = curl_exec ($ch);
    if($result === FALSE) {
        //die(curl_error($ch));
        //$myfile = file_put_contents('logs.txt', curl_error($ch).PHP_EOL , FILE_APPEND | LOCK_EX);
    }
    

} catch (Exception $e) {
  
}
// TESTING CURL FOR Supervisor End


date_default_timezone_set('Asia/Kolkata');
$no = $_GET['sr'];
$tpe= $_GET['tp'];
$m_no= $_GET['mno'];
$shift=$_GET['sft'];
$stich = $_GET['st'];
$thred_break = $_GET['tb'];
$rpm = $_GET['rpm'];
$time_machine = $_GET['tm'];
$head = $_GET['hd'];


$username = "emb_aadesh_1";
$password = "finex@aadesh";
$dbname = "emb_aadesh";
$servername = "localhost";

$url = 'supervisor07.com:5734/v1/acquire?access_token=SECRET_TOKEN&c_id=CUST001&m_id='.$m_no.'&shift='.$shift.'&stitches='.$stich.'&tb='.$thred_break.'&rpm='.$rpm.'&head='.$head.'&st='.$time_machine;

try {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_URL => $url,
        
    ]);
    curl_exec($ch);

} catch (Exception $e) {
  echo "<pre>";
  print_r($e->getMessage());echo "<br></pre>";
  exit;
}
exit;

if($m_no==1 && ceil($head/28)<25)
$head = 25-ceil($head/28);  

else if($m_no==2 && ceil($head/5.5)<25)
$head = 25-ceil($head/5.7);    

else if($m_no==3 && ceil($head/12.5)<25)
$head = 25-ceil($head/12.5);    

else if($m_no==4 && ceil($head/7.5)<25)
$head = 25-ceil($head/7.5);    

else if($m_no==5 && ceil($head/28)<25)
$head = 25- ceil($head/28);    

else if($m_no==6  && ceil($head/30)<27 )
$head = 27-ceil($head/30);    

else if($m_no==7 && ceil($head/7.5)<27)
$head = 27-ceil($head/7.5);  

else if($m_no==8  && ceil($head/30)<27 )
$head = 27-ceil($head/30);    


else if($m_no==9 && ceil($head/28)<27)
$head = 27-ceil($head/28);  

else if($m_no==10 && ceil($head/33)<27)
$head = 27-ceil($head/33);  

else if($m_no==13 && ceil($head/33)<17)
$head = 17-ceil($head/35);  

else if($m_no==14 && ceil($head/39)<25)
$head = 25-ceil($head/39);  

else if($m_no==15 && ceil($head/39)<27)
$head = 27-ceil($head/39);  

else if($m_no>=18 && ceil($head/39)<25)
$head = 25-ceil($head/39);  
    
if($head >27)
   $head = 25 ;
 

$sql3="SELECT `is_buzzed` FROM `live` WHERE `m_no`='$m_no'";
$result3=mysqli_query($conn,$sql3);
 while($row=mysqli_fetch_array($result3))
 {
     $buzz=$row['is_buzzed'];
 }
 if($buzz==1)
   echo "alert";    


//if($shift==2)
//echo "shift";

$rpm = ceil(($rpm)/5) * 5;
//$time_machine =$time_machine/60 ;
$time_machine = $time_machine*1.015;

$conn = mysqli_connect($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . mysqli_connect_error());
    exit;
}

$sql_time = "SELECT * FROM `live` as ls WHERE `m_no` = '$m_no'";
$result3 =  $conn->query($sql_time);

while($row=mysqli_fetch_array($result3)){
   $ls=$row['last_stich'];
   $st1=$row['st'];
}

$sql_st =     "UPDATE `live` SET `last_sync` = now() WHERE `m_no` = '$m_no' AND '$st1' < '$stich'";
$result3 = $conn->query($sql_st);    


//$time=date('H:i:s');
//$ls1 = $ls;
//$sql_max =     "UPDATE `live` SET `last_stop` = '$ls1' WHERE `m_no` = '$m_no'" ;
//$result3 = $conn->query($sql_max);  

$date=date('Y-m-d H:i:s');

//-----------------------------------------------------------------------------------------------------------------
//------------------------------------ live portion----------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------
if($shift==1)
    $shh="Day";
else
    $shh="Night"; 

$sql="SELECT `w_no` FROM `active_worker_detail` WHERE `m_no`='$m_no' AND `shift`='$shh'";
$result=mysqli_query($conn,$sql);
 while($row=mysqli_fetch_array($result)){
   $worker_no=$row['w_no'];
  
 }

//$thred_break = 10;
$dt=(date('H'));
if($rpm>500)
$sql2="UPDATE `live` SET `sr_no`='$no',`type`='$dt',`shift`='$shift',`st`='$stich',`rpm`='$rpm',`tb`='$thred_break',`head`='$head',`tm`='$time_machine',`w_no`='$worker_no',`date`='$date' WHERE `m_no`='$m_no' ";
else
$sql2="UPDATE `live` SET `sr_no`='$no',`type`='$dt',`shift`='$shift',`st`='$stich',`rpm`='$rpm',`tb`='$thred_break',`tm`='$time_machine',`w_no`='$worker_no',`date`='$date' WHERE `m_no`='$m_no' ";
$result2 = $conn->query($sql2);
if (!$result2) {
    echo "DB Error, could not query the database\n";
    exit;
  } else 
  {
    echo "Done";
    }
    
$sql_max =     "UPDATE `live` SET `max_rpm` = '$rpm' WHERE `m_no` = '$m_no' AND `max_rpm` < '$rpm'";
$result3 = $conn->query($sql_max);
    
    
//-----------------------------------------------------------------------------------------------------------------
//------------------------------------ 5 Min. Report --------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------
    $time_machine = $time_machine /60 ;
if ((date('i')%5)==0)
{
   
  //  $datee=$date.AddDays(-3);
  //  $sql4="delete  from `live_data` WHERE `m_no`='$m_no' and `date`<='$datee'";
  //  $result4 = $conn->query($sql4);
 
 $dateminit=date('Y-m-d H:i');
 $sql03="SELECT * FROM `live_data` WHERE `m_no`='$m_no' and DATE_FORMAT(`date`,'%Y-%m-%d %H:%i')='$dateminit'";
 $result03=mysqli_query($conn,$sql03);

if ($result03->num_rows > 0){
    }
else{
    
$sql3="SELECT * FROM `live` WHERE `m_no`='$m_no'";
$result3=mysqli_query($conn,$sql3);
 while($row=mysqli_fetch_array($result3)){
     $sr1=$row['sr_no'];
     $tp1=$row['type'];
     $m1=$row['m_no'];
     $sft1=$row['shift'];
     $st1=$row['st'];
     $max_rpm1=$row['max_rpm'];
     $rpm1 = $row['rpm'];
     $tm1=$row['tm'];
     $wno1=$row['w_no'];
  $sql4="INSERT INTO `live_data`(`sr_no`,`type`,`m_no`,`shift`,`st`,`max_rpm`,`rpm`,`tm`,`w_no`,`date`) VALUES ('$sr1','$tp1','$m1','$sft1','$st1','$max_rpm1','$rpm1','$tm1','$wno1','$date')";
  $result4 = $conn->query($sql4);
  

 }
}

}

if((intval(date('i'))==31) || (intval(date('i'))==1))
{
$sql_max =     "UPDATE `live` SET `max_rpm` = 0 WHERE `m_no` = '$m_no' ";
$result13 = $conn->query($sql_max);
}

//-----------------------------------------------------------------------------------------------------------------
//------------------------------------ 6 HOUR REPORT --------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------


if ((intval(date('H'))==15))
{
     $datec=date('Y-m-d');
        $sql031="SELECT * FROM `tbl_hourse_report` WHERE `m_no`='$m_no' and `shift`='1'  and  DATE_FORMAT(`date`,'%Y-%m-%d')='$datec'";
$result031=mysqli_query($conn,$sql031);
if ($result031->num_rows > 0){
}
    else{
              $shh="Day";
    $sql="SELECT `w_no` FROM `active_worker_detail` WHERE `m_no`='$m_no' and `shift`='$shh'";
$result=mysqli_query($conn,$sql);
 while($row=mysqli_fetch_array($result)){
   $worker_no=$row['w_no'];
 }
   $sql4= "INSERT INTO `tbl_hourse_report`(`sr_no`, `type`, `m_no`, `shift`, `st`, `tb`, `tm`, `w_no`, `date`) VALUES ('2','1','$m_no','1','$stich','$thred_break','$time_machine','$worker_no','$date')";
     $result4 = $conn->query($sql4);
    }

}


if ((intval(date('H'))==3))

{
     $datec =date('Y-m-d', strtotime('-1 day', strtotime($date)));
   
        $sql031="SELECT * FROM `tbl_hourse_report` WHERE `m_no`='$m_no' and `shift`='2'  and  DATE_FORMAT(`date`,'%Y-%m-%d')='$datec'";
$result031=mysqli_query($conn,$sql031);
if ($result031->num_rows > 0){
}
else
{
       
      $shh="Night";
    $sql="SELECT `w_no` FROM `active_worker_detail` WHERE `m_no`='$m_no' and `shift`='$shh'";
$result=mysqli_query($conn,$sql);
 while($row=mysqli_fetch_array($result)){
   $worker_no=$row['w_no'];
 }
  $datee =date('Y-m-d H:i:s', strtotime('-1 day', strtotime($date)));
   $sql4= "INSERT INTO `tbl_hourse_report`(`sr_no`, `type`, `m_no`, `shift`, `st`, `tb`, `tm`, `w_no`, `date`) VALUES ('2','1','$m_no','2','$stich','$thred_break','$time_machine','$worker_no','$datee')";
     $result4 = $conn->query($sql4);
}
 
}

//-----------------------------------------------------------------------------------------------------------------
//------------------------------------ DAILY REPORT  --------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------


if ((intval(date('H'))!=00) and intval((date('H'))!=1) and intval((date('H'))!=2) and intval((date('H'))!=3) and intval((date('H'))!=4) and intval((date('H'))!=5) and intval((date('H'))!=6) and intval((date('H'))!=7)and intval((date('H'))!=8))
{
     
    if ((intval(date('H'))>=9) and (intval(date('H'))<10))
    {

    $datec =date('Y-m-d', strtotime('-1 day', strtotime($date)));
  $sql031="SELECT * FROM `report` WHERE `m_no`='$m_no' and `shift`='2' and  DATE_FORMAT(`date`,'%Y-%m-%d')='$datec'";
    $result031=mysqli_query($conn,$sql031);
     
     if ($result031->num_rows > 0)
     {
      if ($shift==2 || ( (intval(date('i'))<=10 && ($stich>10000 || $time_machine>10))))
        {        
         echo "shift";
       //  $sql2="INSERT INTO `tbl_report_data` (`date`,`m_no`,`st`,`shift`) VALUES ('$date','$m_no','$stich','$shift')";
    //     $result2 = $conn->query($sql2);
        }
     }
    else
    {
     $shh="Night";
     $sql="SELECT `w_no` FROM `active_worker_detail` WHERE `m_no`='$m_no' and `shift`='$shh'";
     $result=mysqli_query($conn,$sql);
     while($row=mysqli_fetch_array($result)){
      $worker_no=$row['w_no'];
        }
    $dater =date('Y-m-d H:i:s', strtotime('-1 day', strtotime($date)));
    $sql1="INSERT INTO `report` (`sr_no`,`type`,`m_no`,`shift`,`st`,`tb`,`tm`,`w_no`,`date`) VALUES ('$no','$tpe','$m_no','2','$stich','$thred_break','$time_machine','$worker_no','$dater')";
    $result1 = $conn->query($sql1);
  
  $sql031="SELECT * FROM `tbl_bonus` WHERE `m_no`='$m_no' and `shift`='2' and  DATE_FORMAT(`date`,'%Y-%m-%d')='$datec'";
    $result031=mysqli_query($conn,$sql031);
    if ($result031->num_rows > 0){
        }
  else{
      $sql001="SELECT * FROM `tbl_worker_salary` WHERE `w_no`='$worker_no'";
        $result001=mysqli_query($conn,$sql001);
    while($row001=mysqli_fetch_array($result001)){
         if ($stich<>0){
             $sl=$row001['day_salary'];
         }
         else{
             $sl=0;
         }
     }
     $sql11="SELECT * FROM `bonus_ratio` WHERE `is_current`='1'";
   
$result11=mysqli_query($conn,$sql11);

     while($row11=mysqli_fetch_array($result11)){
          
          if ($row11['bonus_type']=='1'){
              
              $min_st=$row11['min_stitch'];
              if ($stich>=$min_st){
                  $bn=$row11['bams'];
              }
                  else
                  {
                      $bn=0;
              
              }
          } elseif ($row11['bonus_type']=='3'){
             $sql12="SELECT * FROM `bonus_ratio` WHERE `is_current`='1' AND `m_no`='$m_no'";
            
             $result12=mysqli_query($conn,$sql12);
            
            while($row12=mysqli_fetch_array($result12)){
                  $min_st=$row12['min_stitch'];
                
                   if ($stich>=$min_st){
                      
                  $bn=$row12['bams'];
              }
                  else
                  {
                      $bn=0;
               }
       }
               }
          }
          
      $sql10="INSERT INTO `tbl_bonus` (`m_no`,`shift`,`sr_no`,`st`,`w_no`,`sl`,`bs`,`date`) VALUES ('$m_no','2','$no','$stich','$worker_no','$sl','$bn','$dater')";
    $result10 = $conn->query($sql10);
      
  }
  
  
    //  $sql2 = "SELECT * FROM `report` where `m_no`='$m_no' ORDER BY `sr_no` DESC";

//$result=mysqli_query($conn,$sql2);
// $row=mysqli_fetch_array($result);
//   $sr=$row['sr_no'];
  // echo "done-$sr";
  if ($shift==2 || $stich>0 || $time_machine>0)
        {        
         echo "shift";
        }
        else
        {
            
  echo "done2";
  }
    }

}

if ((intval(date('H'))>=21) and (intval(date('H'))<22)){
      //  if ($shift==1){
      
      $datec=date('Y-m-d');
 $sql003="SELECT * FROM `report` WHERE `m_no`='$m_no' and `shift`='1' and  DATE_FORMAT(`date`,'%Y-%m-%d')='$datec'";
$result003=mysqli_query($conn,$sql003);
if ($result003->num_rows > 0)
{    
    if ($shift==1 || ( (intval(date('i'))<=10 && ($stich>10000 || $time_machine>10))))
    {
        echo "shift";
     //  $sql2="INSERT INTO `tbl_report_data` (`date`,`m_no`,`st`,`shift`) VALUES ('$date','$m_no','$stich','$shift')";
    //$result2 = $conn->query($sql2); 
    }
     
}
    else
    {
      $shh="Day";
    $sql="SELECT `w_no` FROM `active_worker_detail` WHERE `m_no`='$m_no' and `shift`='$shh'";
  $result=mysqli_query($conn,$sql);
  while($row=mysqli_fetch_array($result))
  {
   $worker_no=$row['w_no'];
  }
 
  $sql1="INSERT INTO `report` (`sr_no`,`type`,`m_no`,`shift`,`st`,`tb`,`tm`,`w_no`,`date`) VALUES             ('$no','$tpe','$m_no','1','$stich','$thred_break','$time_machine','$worker_no','$date')";
      $result1 = $conn->query($sql1);
      $sql003="SELECT * FROM `tbl_bonus` WHERE `m_no`='$m_no' and `shift`='1' and  DATE_FORMAT(`date`,'%Y-%m-%d')='$datec'";
  $result003=mysqli_query($conn,$sql003);
  if ($result003->num_rows > 0)
  {
  }
    else{
        $sql001="SELECT * FROM `tbl_worker_salary` WHERE `w_no`='$worker_no'";
  $result001=mysqli_query($conn,$sql001);
     while($row001=mysqli_fetch_array($result001)){
         if ($stich<>0){
             $sl=$row001['day_salary'];
         }
         else{
             $sl=0;
         }
     }
     $sql11="SELECT * FROM `bonus_ratio` WHERE `is_current`='1'";
   
$result11=mysqli_query($conn,$sql11);

     while($row11=mysqli_fetch_array($result11)){
          
          if ($row11['bonus_type']=='1'){
              
              $min_st=$row11['min_stitch'];
              if ($stich>=$min_st){
                  $bn=$row11['bams'];
              }
                  else
                  {
                      $bn=0;
              
              }
          } elseif ($row11['bonus_type']=='3'){
             $sql12="SELECT * FROM `bonus_ratio` WHERE `is_current`='1' AND `m_no`='$m_no'";
            
             $result12=mysqli_query($conn,$sql12);
            
            while($row12=mysqli_fetch_array($result12)){
                  $min_st=$row12['min_stitch'];
                
                   if ($stich>=$min_st){
                      
                  $bn=$row12['bams'];
              }
                  else
                  {
                      $bn=0;
               }
       }
               }
          }
          
      $sql10="INSERT INTO `tbl_bonus` (`m_no`,`shift`,`sr_no`,`st`,`w_no`,`sl`,`bs`,`date`) VALUES ('$m_no','1','$no','$stich','$worker_no','$sl','$bn','$date')";
    $result10 = $conn->query($sql10);

      if ($shift==1 || $stich>0 || $time_machine>0)
        {        
         echo "shift";
        }
        else
        {
      echo "done1";
    }
    }
    }
}
}
mysqli_close($conn);
?>