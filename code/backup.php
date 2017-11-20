<html>
	<head>
		<title>Information Gathered</title>
		<link href="bootstrap.css" rel="stylesheet">
		<link href="style.css" rel="stylesheet" />
		<meta charset="utf-8">
		<meta name="viewport" content="user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, minimal-ui">
		<title>UBCourse Visualization</title>
		<script type="text/javascript" src="../js/cytoscape.js"></script>
		<style type="text/css">
			
		</style>
	</head>


	<?php 

	//phpinfo();
	$conn = new mysqli('localhost',"t", "", "UBC_course_vis");
	$sql = "SELECT * FROM `test` WHERE name = 'WIFI_1' Order by time DESC limit 1 ";
	$result = $conn->query($sql);
	// $row = $result->fetch_assoc();
	$mysqli = new mysqli("localhost", "t", "", "UBC_course_vis");

	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}

	/* set autocommit to off */
	$mysqli->autocommit(FALSE);

	echo "<p> Data Processed</p>"; 

	if(isset($_POST['submit'])) {

		$data_missing = false;
		if(empty($_POST['course'])) {
			$data_missing = true;
		}

		if(!$data_missing) {
			$course = $_POST['course'];
			// $streetAddress = $_POST["streetaddress"];
			// $cityAddress = $_POST["cityaddress"];


			$query1 = "SELECT * FROM credit_table WHERE course_name = ?";
			$query2 = "SELECT normal_pre_req FROM normal_list WHERE course_name = ?";
			$query3 = "SELECT either_pre_req FROM either_list WHERE course_name = ?";
			$query4 = "SELECT or_pre_req FROM or_list WHERE course_name = ?";
			$stmt1 = $mysqli->prepare($query1);
			$stmt2 = $mysqli->prepare($query2);
			$stmt3 = $mysqli->prepare($query3);
			$stmt4 = $mysqli->prepare($query4);
			//echo 'stmt1: '.' stmt2: '.$stmt2.'</br>';
			$stmt1->bind_param("s", $course);
			$stmt2->bind_param("s", $course);
			$stmt3->bind_param("s", $course);
			$stmt4->bind_param("s", $course);
			$result = $conn->query($query1);
			$row = $result->fetch_assoc();

			if ($stmt1->execute() == false)
			{
			    echo 'First query failed: ' . $mysqli->error;
			}
			$stmt1->bind_result($course, $credit);

			$stmt1->fetch();

			echo $course.' has '.$credit.' credits'.'</br>';

			$stmt1->close();
			if ($stmt2->execute() == false)
			{
			    echo 'Second query failed: ' . $mysqli->error;
			}
			$stmt2->bind_result($normal_prereq);

			$stmt2->fetch();

			echo $course.' has '.$normal_prereq.'</br>';

			$stmt2->close();
			if ($stmt3->execute() == false)
			{
			    echo 'Second query failed: ' . $mysqli->error;
			}
			$stmt3->bind_result($either_prereq);

			while($stmt3->fetch()) {

				echo $course.' has '.$either_prereq.'as either_prereq'.'</br>';
			}

			$stmt3->close();
			if ($stmt4->execute() == false)
			{
			    echo 'Second query failed: ' . $mysqli->error;
			}
			$stmt4->bind_result($or_prereq);
			while($stmt4->fetch()) {
				echo $course.' has '.$or_prereq.' as or_prereq'.'</br>';

			}

			// $stmt4->close();
			

		}
	}

	// $mysqli->close();

	 ?>

	 <body>
	 <h4>pH value:<?php echo $course?></h4>
	 <div id="cy" class="jumbotron"> 
    
    </div>
	 <script src="code.js"></script>	    
    <script src="../js/jquery-3.1.1.slim.min.js"></script>
    <!-- <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script> -->
    <script src="../js/tether.min.js"></script>
    <script src="../js/bootstrap.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../js/ie10-viewport-bug-workaround.js"></script>
    </body>
</html>