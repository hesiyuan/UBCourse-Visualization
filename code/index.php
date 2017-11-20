<!DOCTYPE html>
<?php
$conn = new mysqli('localhost',"t", "", "UBC_course_vis");
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
//DO NOT CONSIDER MULTIPLE OR RELATIONSHIP!!!!
// $sql = "SELECT * FROM `test` WHERE name = 'WIFI_1' Order by time DESC limit 1 ";//for 
// $result = $conn->query($sql);
// $row = $result->fetch_assoc();
//#1
function process_allof_str($normal_pre_req)
{
    // echo "Example function.\n";
    $normal_pre_req = str_replace(';', '', $normal_pre_req);
    $oneof_flag = strstr($normal_pre_req, 'ONE OF');
	$allof_flag = strstr($normal_pre_req, 'ALL OF');
	$allof_array = [];
	$oneof_array=[];
	$count_one_of = 0;
	if (strlen($oneof_flag) == 0 && strlen($allof_flag) == 0){
		// $normal_pre_req_courses = str_replace(' ', '', $normal_pre_req);;
		// $domain = strpos($normal_pre_req, 'are') == false;
		// echo '===='.strpos($normal_pre_req, 'ALL OF') == false.'===';
		$includeall = str_replace(' ', '', $normal_pre_req);
		if (sizeof($includeall)>0){# to debug
			$includeall_temps = explode("~", $includeall);
			$allof_array = array_merge($allof_array, $includeall_temps);
		}
		echo 'NO ONE OF AND ALL OF!!!';
	}
	else{
		$oneofs = explode("ONE OF", $normal_pre_req);
		foreach ($oneofs as $key => $value) {
		    $allofs = explode("ALL OF", $value);
			# 0 index must be a one of relationship
			$only_oneof_temp = str_replace(' ', '', $allofs[0]);
			if (strlen($only_oneof_temp)>0){ # in case that there is a all of relationship in the beginning
				$only_oneof_temps = explode("~", $only_oneof_temp);
				// print_r($only_oneof_temps);
				$oneof_array[$count_one_of][] = $only_oneof_temps;
			}
			if (sizeof($allofs)>1){ # to debug
				foreach ($allofs as $key_all => $value_all) {
					if ($key_all ==0){
						continue;
					}
					$allof_tempstring = str_replace(' ', '', $value_all);
					$allof_tempstrings = explode("~", $allof_tempstring);
					// print_r($allof_tempstrings);
					$allof_array = array_merge($allof_array, $allof_tempstrings);
				}
			}
		}
		echo 'YES!!';
	}
	// print_r($oneof_array);
	$pre_req_all['oneof'] = $oneof_array;
	$pre_req_all['allof'] = $allof_array;
	
    return $pre_req_all;
}
$conn->autocommit(FALSE);
echo "<p> Data Processed</p>";
// if(isset($_POST['submit'])) {
// 	$data_missing = false;
// 	if(empty($_POST['course'])) {
// 		$data_missing = true;
// 	}
// 	if(!$data_missing) {
// 		$course = $_POST['course'];
// 	}
	$course = $_POST['course'];
	// echo $course;
	$query = "SELECT * FROM credit_table WHERE course_name = ?";
	$result_credit_table = $conn->prepare($query);
	if($conn->prepare($query)){
	    $result_credit_table->bind_param("s", $course);
	    $result_credit_table->execute();
	    //rest of code here
	}else{
	   //error !! don't go further
	   echo 'First query failed: ' . $mysqli->error;
	}
	$result_credit_table->bind_result($course_name, $credits);
	$result_credit_table->fetch();
	echo $course_name.' has '.$credits.' credits'.'</br>';
	$result_credit_table->close();

	// normalist
	$query = "SELECT normal_pre_req FROM normal_list WHERE course_name = ?";
	$result = $conn->prepare($query);
	if($conn->prepare($query)){
	    $result->bind_param("s", $course);
	    $result->execute();
	    //rest of code here
	}else{
	   //error !! don't go further
	   echo 'Second query failed: ' . $mysqli->error;
	}
	$result->bind_result($normal_pre_req);
	$result->fetch();
	echo $course_name.' has '.$normal_pre_req.' as normal pre-requisites'.'</br>';
	$result->close();

	// either list
	$query = "SELECT either_pre_req FROM either_list WHERE course_name = ?";
	$result = $conn->prepare($query);
	if($conn->prepare($query)){
	    $result->bind_param("s", $course);
	    $result->execute();
	    //rest of code here
	}else{
	   //error !! don't go further
	   echo 'Third query failed: ' . $mysqli->error;
	}
	$result->bind_result($either_pre_req);
	$result->fetch();
	echo $course_name.' has '.$either_pre_req.' as either pre-requisites'.'</br>';
	$result->close();
	
	// or list
	$query = "SELECT or_pre_req FROM or_list WHERE course_name = ?";
	$result = $conn->prepare($query);
	if($conn->prepare($query)){
	    $result->bind_param("s", $course);
	    $result->execute();
	    //rest of code here
	}else{
	   //error !! don't go further
	   echo 'Third query failed: ' . $mysqli->error;
	}
	$result->bind_result($or_pre_req);
	$result->fetch();
	echo $course_name.' has '.$or_pre_req.' as or pre-requisites'.'</br>';
	$result->close();
	// process readed data in normal_re_req list
	$normal_pre_req_all = [];
	$either_pre_req_all = [];
	$or_pre_req_all = [];
	if (strlen($normal_pre_req)>0){
		$normal_pre_req_all = process_allof_str($normal_pre_req);
		// print_r($normal_pre_req_all);
	}
	if (strlen($either_pre_req)>0){
		$either_pre_req_all = process_allof_str($either_pre_req);
		// print_r($either_pre_req_all);
	}
	if (strlen($or_pre_req)>0){
		$or_pre_req_all = process_allof_str($or_pre_req);
		// print_r($or_pre_req_all);
	}
	// $allof_all = $normal_pre_req_all['allof'];
	// $allof_all = array_merge($allof_all, $either_pre_req_all['allof']);
	// $allof_all = array_merge($allof_all, $or_pre_req_all['allof']);
	// $oneof_all = $normal_pre_req_all['oneof'];
	// $oneof_all = array_merge($oneof_all, $either_pre_req_all['oneof']);
	// $oneof_all = array_merge($oneof_all, $or_pre_req_all['oneof']);
	
	// print_r($allof_all);
?>
<html lang="en">

<head>
	<title>Information Gathered</title>
	<link href="bootstrap.css" rel="stylesheet">
	<link href="style.css" rel="stylesheet" />
	<meta charset="utf-8">
	<meta name="viewport" content="user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, minimal-ui">
	<title>UBCourse Visualization</title>
	<script type="text/javascript" src="../js/cytoscape.js"></script>
	<style type="text/css"></style>

</head>

<body>
	<div id="cy" class="jumbotron"> 
    </div>
    <p id="demo"></p>
	<script>
		var w = window.innerWidth;
		var h = window.innerHeight;
		var cy =  cytoscape({
			container: document.getElementById('cy'),
	 		boxSelectionEnabled: false,
	 		autounselectify: true,

			style: [
			{
				selector: 'node',
	    		css: {
		        'content': 'data(id)',
		        'text-valign': 'center',
		        'text-halign': 'center',
		        'shape': 'roundrectangle',
		        'width': '80px',
		        'background-color': 'orange' 
		      }
		    },
		    {
		    selector: '$node > node', //container
		    css: {
		        'padding-top': '10px',
		        'padding-left': '10px',
		        'padding-bottom': '10px',
		        'padding-right': '10px',
		        'text-valign': 'top',
		        'text-halign': 'center',
		        'background-color': 'blue'
		      }
		    },
		    {
		    selector: 'edge',
		    css: {
		        'target-arrow-shape': 'triangle'
		      }
		    },
		    {
		    selector: ':selected',
		    css: {
		        'background-color': 'black',
		        'line-color': 'black',
		        'target-arrow-color': 'black',
		        'source-arrow-color': 'black'
			    }
			}
			],
			layout: {
			    name: 'preset',
			    padding: 5
			}
		});
		var start_w = w/6;
		var start_h = h/6;
		var vertical_gap = 30;
		var horizontal_gap = 80;
		var main_course = "<?php echo $course_name ?>";
		var either_pre_req = "<?php echo $either_pre_req ?>";
		var normal_pre_req_all = <?php echo json_encode($normal_pre_req_all, JSON_PRETTY_PRINT) ?>;
		var either_pre_req_all = <?php echo json_encode($either_pre_req_all, JSON_PRETTY_PRINT) ?>;
		var or_pre_req_all = <?php echo json_encode($or_pre_req_all, JSON_PRETTY_PRINT) ?>;
		// alert(book.oneof);
		var normal_list_oneof_iter = normal_pre_req_all['oneof'][0];
		for (var i = 0; i < normal_list_oneof_iter.length; i++){
		    var normal_list_oneof_iter_inner = normal_list_oneof_iter[i];
		    for (var j = 0; j < normal_list_oneof_iter_inner.length; j++){
		    	cy.add({ data: { id: normal_list_oneof_iter_inner[j]}, position: { x: start_w+horizontal_gap*i, y: start_h+vertical_gap*j } });
		    }
		}
		// document.getElementById("demo").innerHTML = either_pre_req_all['allof'][0]; 
		cy.add({ data: { id: main_course}, position: { x: w/2, y: start_h-60 } });
		// cy.add({ data: { id: normal_pre_req_all['oneof'][0][0][0], credit: 4 }, position: { x: start_h+horizontal_gap, y: start_w+vertical_gap} });
		// cy.add({ data: { id: 'cs', source: main_course, target: either_pre_req } });
	</script>
    <script src="../js/jquery-3.1.1.slim.min.js"></script>
    <script src="../js/tether.min.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script src="../js/ie10-viewport-bug-workaround.js"></script>


</body>

</html>
<?php
//if ($result->num_rows >= 0) {
    // output data of each row
 //   while($row = $result->fetch_assoc()) {

       // echo "Item: " . $row["name"]. "| time: " . $row["time"]. "| PHvalue: " . $row["PHvalue"]. "| PHEstimated: " . $row["PHEstimated"]. "| Temperature: " . $row["Temperature"]. "| ORP: " . $row["ORP"]. "| DO: " . $row["DO"]. "| DOPercentage: " . $row["DOPercentage"]. "| Conductivity: " . $row["Conductivity"]. "| ConductivitySolution: " . $row["ConductivitySolution"]. "<br>";
//    }
//} else {
 //   echo "0 results";
//}
?>
<?php
$conn->close();
?>
