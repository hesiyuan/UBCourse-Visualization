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

	//either_or_list
	$query = "SELECT either_pre_req, or_pre_req FROM either_or_list WHERE course_name = ?";
	$result = $conn->prepare($query);
	if($conn->prepare($query)){
	    $result->bind_param("s", $course);
	    $result->execute();
	    //rest of code here
	}else{
	   //error !! don't go further
	   echo 'Fourth query failed: ' . $mysqli->error;
	}
	$result->bind_result($either_pre_req_pair, $or_pre_req_pair);
	$result->fetch();
	echo $course_name.' has '.$either_pre_req_pair.$or_pre_req_pair.' as either pre-requisites pair'.'</br>';
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
	if (strlen($either_pre_req_pair)>0){
		$either_pre_req_pair_all = process_allof_str($either_pre_req_pair);
		$or_pre_req_pair_all = process_allof_str($or_pre_req_pair);
		// print_r($either_pre_req_all);
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
		var global_normal = [];
		var global_either = [];
		var global_or = [];
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
		        // 'target-arrow-shape': 'triangle'
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
		cy.on('tap', 'node', function(){
			var nodes = this;
			var node2 = this;
			var tapped = nodes;
			var food = [];
			var selected_course = this.id();
			if (this.id()=='orlist'){
				global_either = [];
				for(;;){
				    var connectedEdges = nodes.connectedEdges(function(el){
				      return !el.target().anySame( nodes );
				    });
				    var connectedNodes = connectedEdges.targets();
				    Array.prototype.push.apply( food, connectedNodes );
				    nodes = connectedNodes;

				    if( nodes.empty() ){ 
				    	break; 
				    }
				    
				}
				console.log(food);
				for( var i = food.length - 1; i >= 0; i-- ){ (function(){
				    var thisFood = food[i];
				    var eater = thisFood.connectedEdges(function(el){
				    	return el.target().same(thisFood);
				    }).source();

				    thisFood.delay( delay, function(){
				    	
				    } ).animate({
				    	css: {
					        'width': 10,
					        'height': 10,
					        'border-width': 0,
					        'opacity': 0
				      }
				    }, {
				    	duration: duration,
				    	complete: function(){
				    		thisFood.remove();
				      }
				    });
				    delay += duration;
				})(); }
			}else if (this.id()=='eitherlist'){
				global_or = [];
				for(;;){
				    var connectedEdges = node2.connectedEdges(function(el){
				      return !el.source().anySame( node2 );
				    });
				    var connectedNodes = connectedEdges.sources();
				    Array.prototype.unshift.apply( food, connectedNodes );
				    node2 = connectedNodes;
				    // console.log(node2.id());
				    if( node2.empty() ){ 
				    	break; 
				    }
				    else{
				    	lastnode = node2;
				    }
				}
				//find leaves of child nodes in the 'orlist', and add to the end of the food array
				var child_of_or_list = cy.elements('edge[target="eitherlist"]');
      			var or_list_to_remove = child_of_or_list.source();
      			var or_list_childs_to_remove = or_list_to_remove.descendants();
      			or_list_childs_to_remove.forEach(function( ele ){
      				console.log( ele.id() );
      				// get sources/targets for these ele
					// var temp_pre_reqs_to_remove = ele.children();
					// temp_pre_reqs_to_remove.forEach(function( eles ){
					// 	console.log( eles.id() );
					// });
				});
      			// console.log(or_list_childs_to_remove.id());
      			// var connectedNodes = or_list_childs_to_remove.targets();
      			// console.log(or_list_childs_to_remove.id());
				// Array.prototype.push.apply( food, or_list_childs_to_remove );
      			// then, the same as in orlist
				for( var i = food.length - 1; i >= 0; i-- ){ (function(){
				    var thisFood = food[i];
				    var eater = thisFood.connectedEdges(function(el){
				    	return el.target().same(thisFood);
				    }).source();

				    thisFood.delay( delay, function(){
				    	
				    } ).animate({
				    	css: {
					        'width': 10,
					        'height': 10,
					        'border-width': 0,
					        'opacity': 0
				      }
				    }, {
				    	duration: duration,
				    	complete: function(){
				    		thisFood.remove();
				      }
				    });
				    delay += duration;
				})(); }
			}else{
				var lastnode = nodes;
				for(;;){
				    var connectedEdges = nodes.connectedEdges(function(el){
				      return !el.target().anySame( nodes );
				    });
				    var connectedNodes = connectedEdges.targets();
				    Array.prototype.push.apply( food, connectedNodes );
				    nodes = connectedNodes;

				    if( nodes.empty() ){ 
				    	break; 
				    }
				    
				}
				Array.prototype.unshift.apply( food, node2 );
				//find the root
				for(;;){
				    var connectedEdges = node2.connectedEdges(function(el){
				      return !el.source().anySame( node2 );
				    });
				    var connectedNodes = connectedEdges.sources();
				    Array.prototype.unshift.apply( food, connectedNodes );
				    node2 = connectedNodes;
				    // console.log(node2.id());
				    if( node2.empty() ){ 
				    	break; 
				    }
				    else{
				    	lastnode = node2;
				    }
				}
				food.shift();
				// remian the top box
				if (lastnode.id()!='eitherlist' && lastnode.id()!='orlist'){
					lastnode.css({label: selected_course});
					if (lastnode.id().includes('Group_n')){
						global_normal=global_normal.concat(selected_course);
						console.log(global_normal);
					} else if(lastnode.id().includes('Group_e')){
						global_either=global_either.concat(selected_course);
						console.log(global_either);
					}else if(lastnode.id().includes('Group_o')){
						global_or=global_or.concat(selected_course);
						console.log(global_or);
					}
				}
				console.log(lastnode.id());
				// console.log(lastnode.label());
				var delay = 0;
				var duration = 500;
				for( var i = food.length - 1; i >= 0; i-- ){ (function(){
				    var thisFood = food[i];
				    var eater = thisFood.connectedEdges(function(el){
				    	return el.target().same(thisFood);
				    }).source();

				    thisFood.delay( delay, function(){
				    	
				    } ).animate({
				    	css: {
					        'width': 10,
					        'height': 10,
					        'border-width': 0,
					        'opacity': 0
				      }
				    }, {
				    	duration: duration,
				    	complete: function(){
				    		thisFood.remove();
				      }
				    });
				    delay += duration;
				})(); } // for
			}
		}); // on tap
		var start_w = w/6;
		var start_h = h/6;
		var vertical_gap = 30;
		var horizontal_gap = 80;
		var main_course = "<?php echo $course_name ?>";
		var either_pre_req = "<?php echo $either_pre_req ?>";
		var normal_pre_req_all = <?php echo json_encode($normal_pre_req_all, JSON_PRETTY_PRINT) ?>;
		var either_pre_req_all = <?php echo json_encode($either_pre_req_all, JSON_PRETTY_PRINT) ?>;
		var or_pre_req_all = <?php echo json_encode($or_pre_req_all, JSON_PRETTY_PRINT) ?>;
		var either_pre_req_pair_all = <?php echo json_encode($either_pre_req_pair_all, JSON_PRETTY_PRINT) ?>;
		var either_pre_req_pair_all = <?php echo json_encode($either_pre_req_pair_all, JSON_PRETTY_PRINT) ?>;
		// alert(book.oneof);
		var normal_list_oneof_iter = normal_pre_req_all['oneof'][0];
		var normal_list_allof_iter = normal_pre_req_all['allof'];
		cy.add({ data: { id: 'normalist' } });
		var additional_width = 0;
		if (normal_pre_req_all['oneof'].length >0){
			for (var i = 0; i < normal_list_oneof_iter.length; i++){
			    var normal_list_oneof_iter_inner = normal_list_oneof_iter[i];
			    var gourp_name_normal_list = (i+1).toString();
			    gourp_name_normal_list = 'Group_n '.concat(gourp_name_normal_list);
			    cy.add({ data: { id: gourp_name_normal_list, parent: 'normalist'}, position: { x: start_w+horizontal_gap*i, y: start_h-vertical_gap-20 } });
			    for (var j = 0; j < normal_list_oneof_iter_inner.length; j++){
			    	// cy.add({ data: { id: normal_list_oneof_iter_inner[j], parent: 'normalist'}, position: { x: start_w+horizontal_gap*i, y: start_h+vertical_gap*j } });
			    	cy.add({ data: { id: normal_list_oneof_iter_inner[j],}, position: { x: start_w+horizontal_gap*i, y: start_h+vertical_gap*j } });
			    	// cy.add({ data: { id: normal_list_oneof_iter_inner[j]}, position: { x: start_w+horizontal_gap*i, y: start_h+vertical_gap*j } });
			    	if (j>0){
			    		var link_name_normal_list = (i+j).toString();
			    		link_name_normal_list = link_name_normal_list.concat('normal_list')
			    		cy.add({ data: { link_name_normal_list, source: normal_list_oneof_iter_inner[j-1], target: normal_list_oneof_iter_inner[j] } })
			    	}
			    	if (j==0){
			    		cy.add({ data: { id:'link_normal_list'.concat((i).toString()), source: gourp_name_normal_list , target: normal_list_oneof_iter_inner[j] } })
			    	}
			    	additional_width = horizontal_gap*i;
			    	// cy.add({data: { id: (i+j).toString() , source: normal_list_oneof_iter_inner[j], target: main_course }});
			    }
			}
			start_w = start_w+additional_width+horizontal_gap+30;
			additional_width = 0;
		}
		// cy.add({ data: { id:'afdafa', source: 'MATH152', target: 'Group 0' } })
		if (normal_list_allof_iter.length >0){
			for (var i = 0; i < normal_list_allof_iter.length; i++){
				cy.add({ data: { id: normal_list_allof_iter[i], parent: 'normalist'}, position: { x: start_w+horizontal_gap*i, y: start_h-vertical_gap-20} });
			    	additional_width = horizontal_gap*i;
			    global_normal = global_normal.concat(normal_list_allof_iter[i]);
			}
			start_w = start_w+additional_width+horizontal_gap+30;
		}
		
		var either_list_oneof_iter = either_pre_req_all['oneof'][0];
		var eitherl_list_allof_iter = either_pre_req_all['allof'];
		// start_w = start_w+300;
		// cy.add({ data: { id: 'eitherlist' }, position:{x:start_w,y:start_h}});
		cy.add({ data: { id: 'eitherlist' }});
		// cy.add({ data: { id: eitherl_list_allof_iter[0] }});
		if (either_pre_req_all['oneof'].length >0){
			for (var i = 0; i < either_list_oneof_iter.length; i++){
			    var either_list_oneof_iter_inner = either_list_oneof_iter[i];
			    var gourp_name_either_list = (i+1).toString();
			    gourp_name_either_list = 'Group_e '.concat(gourp_name_either_list);
			    cy.add({ data: { id: gourp_name_either_list, parent: 'eitherlist'}, position: { x: start_w+horizontal_gap*i, y: start_h-vertical_gap-20 } });
			    for (var j = 0; j < either_list_oneof_iter_inner.length; j++){
			    	// cy.add({ data: { id: normal_list_oneof_iter_inner[j], parent: 'normalist'}, position: { x: start_w+horizontal_gap*i, y: start_h+vertical_gap*j } });
			    	cy.add({ data: { id: either_list_oneof_iter_inner[j],}, position: { x: start_w+horizontal_gap*i, y: start_h+vertical_gap*j } });
			    	// cy.add({ data: { id: normal_list_oneof_iter_inner[j]}, position: { x: start_w+horizontal_gap*i, y: start_h+vertical_gap*j } });
			    	if (j>0){
			    		var link_name_either_list = (i+j).toString();
			    		link_name_either_list = link_name_either_list.concat('normal_list')
			    		cy.add({ data: { link_name_either_list, source: either_list_oneof_iter_inner[j-1], target: either_list_oneof_iter_inner[j] } })
			    	}
			    	if (j==0){
			    		cy.add({ data: { id:'link_either_list'.concat((i).toString()), source: gourp_name_either_list , target: either_list_oneof_iter_inner[j] } })
			    	}
			    	additional_width = horizontal_gap*i;
			    	// cy.add({data: { id: (i+j).toString() , source: normal_list_oneof_iter_inner[j], target: main_course }});
			    }
			}
			start_w = start_w+additional_width+horizontal_gap+30;
			additional_width = 0;
		}
		if (eitherl_list_allof_iter.length >0){
			for (var i = 0; i < eitherl_list_allof_iter.length; i++){
				cy.add({ data: { id: eitherl_list_allof_iter[i], parent: 'eitherlist'}, position: { x: start_w+horizontal_gap*i, y: start_h-vertical_gap-20} });
			    	additional_width = horizontal_gap*i;
			    global_either = global_either.concat(eitherl_list_allof_iter[i]);
			    console.log(global_either);
			}
			start_w = start_w+additional_width+horizontal_gap+30;
		}
		var or_list_oneof_iter = or_pre_req_all['oneof'][0];
		var or_list_allof_iter = or_pre_req_all['allof'];
		// start_w = start_w+300;
		// cy.add({ data: { id: 'eitherlist' }, position:{x:start_w,y:start_h}});
		cy.add({ data: { id: 'orlist' }});
		// cy.add({ data: { id: eitherl_list_allof_iter[0] }});
		if (or_pre_req_all['oneof'].length >0){
			for (var i = 0; i < or_list_oneof_iter.length; i++){
			    var or_list_oneof_iter_inner = or_list_oneof_iter[i];
			    var gourp_name_or_list = (i+1).toString();
			    gourp_name_or_list = 'Group_o '.concat(gourp_name_or_list);
			    cy.add({ data: { id: gourp_name_or_list, parent: 'orlist'}, position: { x: start_w+horizontal_gap*i, y: start_h-vertical_gap-20 } });
			    for (var j = 0; j < or_list_oneof_iter_inner.length; j++){
			    	// cy.add({ data: { id: normal_list_oneof_iter_inner[j], parent: 'normalist'}, position: { x: start_w+horizontal_gap*i, y: start_h+vertical_gap*j } });
			    	cy.add({ data: { id: or_list_oneof_iter_inner[j],}, position: { x: start_w+horizontal_gap*i, y: start_h+vertical_gap*j } });
			    	// cy.add({ data: { id: normal_list_oneof_iter_inner[j]}, position: { x: start_w+horizontal_gap*i, y: start_h+vertical_gap*j } });
			    	if (j>0){
			    		var link_name_or_list = (i+j).toString();
			    		link_name_or_list = link_name_or_list.concat('normal_list')
			    		cy.add({ data: { link_name_or_list, source: or_list_oneof_iter_inner[j-1], target: or_list_oneof_iter_inner[j] } })
			    	}
			    	if (j==0){
			    		cy.add({ data: { id:'link_or_list'.concat((i).toString()), source: gourp_name_or_list , target: or_list_oneof_iter_inner[j] } })
			    	}
			    	additional_width = horizontal_gap*i;
			    	// cy.add({data: { id: (i+j).toString() , source: normal_list_oneof_iter_inner[j], target: main_course }});
			    }
			}
			start_w = start_w+additional_width+horizontal_gap+30;
			additional_width = 0;
		}
		if (or_list_allof_iter.length >0){
			for (var i = 0; i < or_list_allof_iter.length; i++){
				cy.add({ data: { id: or_list_allof_iter[i], parent: 'orlist'}, position: { x: start_w+horizontal_gap*i, y: start_h-vertical_gap-20} });
			    	additional_width = horizontal_gap*i;
			    global_or = global_or.concat(or_list_allof_iter[i]);
			    console.log(global_or);
			}
			start_w = start_w+additional_width+horizontal_gap+30;

		}
		cy.add({ data: { id: main_course}, position: { x: w/2, y: start_h-60-vertical_gap-20 } });
		cy.add({ data: { id: 'ad', source: 'orlist', target: 'eitherlist' } })
		// cy.add({ data: { id: 'ad', source: 'MATH152', target: 'MATH221' } })
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
