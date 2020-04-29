<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<?php
    session_start();
    if(isset($_SESSION['last_action']))
    {
        if(time() - $_SESSION['last_action']>1800)
        {
        session_unset();
        session_destroy();  
        }
    }
    $_SESSION['last_action'] = time();
    if(isset($_SESSION['username'])){
        'Username - '.$_SESSION['username']." ";
        'User Level - '.$_SESSION['userlevel'];
    }
    else{
        header("Location: index.php");
    }
    if(!isset($_SERVER['HTTP_REFERER'])){
        // redirect them to your desired location
        header('location:home.php');        
    }
    $con = mysqli_connect("localhost","root");
    mysqli_select_db($con, "bughound_test1");
    if(! $con ) {
        die('Could not connect: ' . mysqli_error());
    }
    
    $bug_id=$_POST['bug_id'];
    $data=array();
    function array_push_assoc($array_a, $key_a, $value_a){
        $array_a[$key_a] = $value_a;
        return $array_a;
        }
    if($_POST['program'] != "") $data = array_push_assoc($data, 'Program', $_POST['program']);
    if($_POST['report-type'] != "") $data =array_push_assoc($data, 'Report_type', $_POST['report-type']);
    if($_POST['severity'] != "") $data =array_push_assoc($data, 'Severity', $_POST['severity']);
    if($_POST['summary'] != "")$data = array_push_assoc($data, 'Problem_Summary', $_POST['summary']);
    if($_POST['reproduce'] != "") $data =array_push_assoc($data, 'Reproducable', $_POST['reproduce']);
    if($_POST['problem'] != "") $data =array_push_assoc($data, 'Problem', $_POST['problem']);
    if($_POST['reported-by'] != "") $data =array_push_assoc($data, 'Reported_By', $_POST['reported-by']);
    if($_POST['reported-date'] != "") $data =array_push_assoc($data, 'Report_Date', $_POST['reported-date']);
    // $program=$_POST['program'];
    // $r_type=$_POST['report-type'];
    // $severity=$_POST['severity'];
    // $summary=$_POST['summary'];
    // $reproduce=$_POST['reproduce'];
    // $problem=$_POST['problem'];
    // $r_by=$_POST['reported-by'];
    // $r_date=$_POST['reported-date'];
    // other fields
    if($_POST['function-area'] != "") $data =array_push_assoc($data, 'Functional_Area', $_POST['function-area']);
    if($_POST['assigned-to'] != "") $data =array_push_assoc($data, 'Assigned_To', $_POST['assigned-to']);
    if($_POST['comments'] != "") $data =array_push_assoc($data, 'Comments', $_POST['comments']);
    if($_POST['status'] != "") $data =array_push_assoc($data, 'Status_bug', $_POST['status']);
    if($_POST['priority'] != "") $data =array_push_assoc($data, 'Priority', $_POST['priority']);
    if($_POST['resolution'] != "") $data =array_push_assoc($data, 'Resolution', $_POST['resolution']);
    if($_POST['resolution-v'] != "") $data =array_push_assoc($data, 'Resolution_Version', $_POST['resolution-v']);
    if($_POST['resolved-by'] != "") $data =array_push_assoc($data, 'Resolved_By', $_POST['resolved-by']);
    if($_POST['resolved-date'] != "") $data =array_push_assoc($data, 'Resolve_Date', $_POST['resolved-date']);
    if($_POST['tested-by'] != "") $data =array_push_assoc($data, 'Tested_By', $_POST['tested-by']);
    if($_POST['tested-date'] != "") $data =array_push_assoc($data, 'Test_Date', $_POST['tested-date']);
    if($_POST['treat-as'] != "") $data =array_push_assoc($data, 'Deferred', $_POST['treat-as']);    
        // $area=$_POST['function-area'];
        // $assigned_to=$_POST['assigned-to'];
        // $comments=$_POST['comments'];
        // $status=$_POST['status'];
        // $priority=$_POST['priority'];
        // $resolution=$_POST['resolution'];
        // $resolution_v=$_POST['resolution-v'];
        // $resolved_by=$_POST['resolved-by'];
        // $resolved_date=$_POST['resolved-date'];
        // $tested_by=$_POST['tested-by'];
        // $tested_date=$_POST['tested-date'];
        // $treat=$_POST['treat-as'];
        // $query="Update bug Set Program='".$program."', Report_type='".$r_type."', Severity='".$severity."', Problem_Summary='".$summary."', Reproducable='".$reproduce."', Problem='".$problem."', Reported_By='".$r_by."', Report_Date='".$r_date."',
        //     Functional_Area='".$area."', Assigned_To='".$assigned_to."', Comments='".$comments."', Status_bug='".$status."', Priority='".$priority."', Resolution='".$resolution."', Resolution_Version='".$resolution_v."', Resolved_By='".$resolved_by."', Resolve_Date='".$resolved_date."',
        //     Tested_By='".$tested_by."', Test_Date='".$tested_date."', Deferred='".$treat."' WHERE bug_id=".$bug_id;
    $query = "UPDATE bug Set ";
    foreach($data as $key=>$val){
        $query.= $key."='".$val."',";
    }
    $query = substr($query,0,-1);
    $query.="WHERE bug_id=".$bug_id.";";
    echo "Query = ".$query;                
    
    $result=mysqli_query($con,$query);
    if($result){
        echo "Bug Updated";
    }
    else{
        echo "Update failed - ".mysqli_error($con);
    }
    

    $num_files=count($_FILES['file1']['tmp_name']);
    echo "count= ".$num_files;
    $query_file="";
    for($i=0;$i<$num_files;$i++){
        $filename=$_FILES['file1']['name'][$i];
        move_uploaded_file($_FILES['file1']['tmp_name'][$i],'uploads/'.$filename);
        // $data=file_get_contents("uploads/".$_FILES['file1']['name'][$i]);            
        $query_file.="INSERT INTO attachment(bug, file_name) VALUES ('".$bug_id."', '".$filename."');";
    }
    if(mysqli_multi_query($con, $query_file)){
        echo "Files inserted";
    }else{
        echo "Error".$query_file."<br>".mysqli_error($con);
    }
    
    
  	?>
<body>
    <?php if(isset($_SESSION['username'])): ?>
        <ul class="nav justify-content-end">
        <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
        </li>
        </ul>
    <?php else: ?>
        <ul class="nav justify-content-end">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Login</a>
            </li>
        </ul>
    <?php endif; ?>
    <button onclick="go_back()">Back</button>
</body>
<script type="text/javascript">
    function go_back(){
        window.location.replace("searchbug.php");
    }            
</script>
</html>