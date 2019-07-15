<?php

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


$username = "finexs07_akshar2";
$password = "emb@aksharfeb$";
$dbname = "finexs07_aksharfeb";
$servername = "localhost";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . mysqli_connect_error());
    exit;
}


//-------------------MANUAL COMMAND--------
//if($shift==2)
  //echo "shift";
if($m_no==11)
 echo "alert";
//-------------------------------------

//----------------Limitations--------


if($rpm >1100)
  $rpm = 0 ;
//-------------------------------------


if($m_no==1 && ceil($head/9.2)<36)
{
    $head = 36-ceil($head/9.2);
    if($head>36)
      $head = 36 ;
}
else if($m_no==2  && ceil($head/8.5)<25 )
{
    $head = 25-ceil($head/8.5);
    if($head>25)
      $head = 25 ;
}
else if($m_no==3 && ceil($head/10)<25) 
{
    $head = 25-ceil($head/10);    
    if($head>25)
      $head = 25 ;
}
else if($m_no==4 &&  ceil($head/25)<27) 
{
    $head =27-ceil($head/25) ;
    if($head>27)
      $head = 27 ;
}
else if($m_no==5  && ceil($head/30)<27  ) 
{
    $head = 27-ceil($head/30);    
    if($head>27)
      $head = 27 ;
}
else if($m_no==6 && ceil($head/25)<27 )  
{
    $head = 27-ceil($head/25);    
    if($head>27)
      $head = 27 ;
}

else if($m_no==7 && ceil($head/10.5)<25) 
{
    $head = 25-ceil($head/10.5);    
    if($head>25)
      $head = 25 ;
}
else if($m_no==8 && ceil($head/33)<27) 
{
 $head = 27-ceil($head/33);    
 if($head>27)
  $head = 27 ;
}
else if($m_no==9 && ceil($head/33)<27) 
{
 $head = 27-ceil($head/33);    
 if($head>27)
  $head = 27 ;
}

else if($m_no==10 && ceil($head/31)<27) 
{
    $head = 27-ceil($head/31);    
    if($head>27)
      $head = 27 ;
}



$sql3="SELECT * FROM `live` WHERE `m_no`='$m_no'";
$result3=mysqli_query($conn,$sql3);
while($row=mysqli_fetch_array($result3))
{
   $buzz=$row['is_buzzed'];
}
if($buzz==1)
 echo "alert";    

$rpm = ceil(($rpm)/5) * 5;



$sql_time = "SELECT * FROM `live` as ls WHERE `m_no` = '$m_no'";
$result3 =  $conn->query($sql_time);

while($row=mysqli_fetch_array($result3)){
  $ls=$row['last_stich'];
  $st1=$row['st'];
}

$sql_st =     "UPDATE `live` SET `last_sync` = now() WHERE `m_no` = '$m_no' AND '$st1' < '$stich'";
$result3 = $conn->query($sql_st);    


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
       $tb1=$row['tb'];
       $rpm1 = $row['rpm'];
       $max_rpm1=$row['max_rpm'];
       $tm1=$row['tm']/60;
       $wno1=$row['w_no'];
       $date1 = $row['date'];
       $head1 = $row['head'];
       $sql4="INSERT INTO `live_data`(`sr_no`,`type`,`m_no`,`shift`,`st`,`tb`,`rpm`,`max_rpm`,`tm`,`w_no`,`date`,`head`) VALUES ('$sr1','$tp1','$m1','$sft1','$st1','$tb1','$rpm1','$max_rpm1','$tm1','$wno1','$date1','$head')";
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
          if ($shift==2 || ( (intval(date('i'))<=10 && ($stich>10000 || $time_machine>600))))
          {        
           echo "shift";
       //	 $sql2="INSERT INTO `tbl_report_data` (`date`,`m_no`,`st`,`shift`) VALUES ('$date','$m_no','$stich','$shift')";
    //     $result2 = $conn->query($sql2);
       }
   }
   else
   {
    $sql_st =     "UPDATE `live` SET `last_sync` = now() WHERE `m_no` = '$m_no'";
    $result3 = $conn->query($sql_st);    

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
//	 $sr=$row['sr_no'];
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
	    //	if ($shift==1){
 
   $datec=date('Y-m-d');
   $sql003="SELECT * FROM `report` WHERE `m_no`='$m_no' and `shift`='1' and  DATE_FORMAT(`date`,'%Y-%m-%d')='$datec'";
   $result003=mysqli_query($conn,$sql003);
   if ($result003->num_rows > 0)
   {    
    if ($shift==1 || ( (intval(date('i'))<=10 && ($stich>10000 || $time_machine>600))))
    {
        echo "shift";
     //  $sql2="INSERT INTO `tbl_report_data` (`date`,`m_no`,`st`,`shift`) VALUES ('$date','$m_no','$stich','$shift')";
    //$result2 = $conn->query($sql2); 
    }
    
}
else
{
    $sql_st =     "UPDATE `live` SET `last_sync` = now() WHERE `m_no` = '$m_no'";
    $result3 = $conn->query($sql_st);    
    
    $shh="Day";
    $sql="SELECT `w_no` FROM `active_worker_detail` WHERE `m_no`='$m_no' and `shift`='$shh'";
    $result=mysqli_query($conn,$sql);
    while($row=mysqli_fetch_array($result))
    {
      $worker_no=$row['w_no'];
  }
  
  $sql1="INSERT INTO `report` (`sr_no`,`type`,`m_no`,`shift`,`st`,`tb`,`tm`,`w_no`,`date`) VALUES 		        ('$no','$tpe','$m_no','1','$stich','$thred_break','$time_machine','$worker_no','$date')";
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