<?php
	
	//require the framework
	require_once "../requires/initialize.php";
	
	//construct the page
	$page = new Page();
	$page->name = "Report";
	$page->is_admin_only = true;
	
		
	//if the filtering criteria is changed, process it here
	if(isset($_POST['submit'])) {
		//the form was submitted, so we need to reset it
		$session->unset_variable('pet_where');
		
		//if the submit button is clicked, process it
		//if reset was clicked, this will skip - no issues
		if($_POST['submit'] == 'submit') {
		
			$temp = array();
			foreach($_POST AS $key => $value) {
				//ignore the submit item and all empty fields
				if($key != 'submit' && !empty($value)) {
					$temp[$key] = $value;
				}
			}
		
			//replace the filters in the session with the new items
			$session->set_variable('pet_where', $temp);
		}
	}
	
	//by this point, we know what the filter variables are
	//so we need to create the SQL that will reflect those changes
	//these are the function calls to generate the SQL
	//generate_pet_where();
	//generate_pet_order_by();
	
	//if the sorting method for the pets resultset changed, process it here
	if(isset($_GET['toggle'])) {
		//we need to process this change
		//so first we need to see what the current sorting method is
		if(isset($session->pet_order_by)) 
			$current_sort = $session->pet_order_by;
		else {
			$current_sort = array();
			$current_sort['column'] = 'name';
			$current_sort['order'] = 'ASC';
		}
		
		//so now we need to set the new sort
		$new_sort = array();
		//it is the new column item from the $_GET variable
		$new_sort['column'] = $_GET['toggle'];
		
		//now we need to determine the column sort order
		if($_GET['toggle'] == $current_sort['column']) {
			//the values are equivalent, simply switch from ASC to DESC and vice-versa
			if($current_sort['order'] == 'ASC')
				$new_sort['order'] = 'DESC';
			else
				$new_sort['order'] = 'ASC';
		} else {
			//the values are not equivalent, force set to ASC
			$new_sort['order'] = 'ASC';
		}
				
		//set the new sort mechanism
		$session->set_variable('pet_order_by', $new_sort);
		//redirect back
		redirect_head(file_name_without_get());
		
	}

	//grab the set of pets to display
	$sql = "SELECT `p`.* FROM `pet` AS `p` ";
	$sql .= "INNER JOIN `breed` AS `b` ON `b`.`breed_wk` = `p`.`breed_wk` ";
	$sql .= "INNER JOIN `pet_type` AS `pt` ON `pt`.`pet_type_wk` = `b`.`pet_type_wk` ";
	$sql .= "INNER JOIN `status` AS `s` ON `s`.`status_wk` = `p`.`status_wk` ";
	$sql .= "INNER JOIN `color` AS `c` ON `c`.`color_wk` = `p`.`color_wk` ";
	$sql .= "WHERE `p`.`is_deleted` = 0 ";
	$sql .= generate_pet_where()." ";
	$sql .= generate_pet_order_by(). " ";
	$sql .= ";";
	
	

	//grab the body of pets
	$page->body = display_pet_blog($sql);
	
	//page body object
	//$pageObj = find_by_sql($sql);
	
	//include the header
	require_once "../requires/template/header.php";
	
	// temporary messages section
	 echo "<p id=\"ajax_message\" style=\"color: red; font-family: courier;\"></p>";

?>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js" charset="utf-8"></script>
<script>
google.charts.load("current", {packages: ["corechart"]});
google.charts.setOnLoadCallback(displayReports);

function displayReports(){
	try{
		//displayChart1();
		displayChart2();
	}
	catch(e){
		document.getElementById('reportCombo1').innerHTML = "Charts are unavailable at this time, Please reload the page or try again later";
	}
}


function displayChart2(){
	try{
		var data = [];
		var data0 = [];
		var header = ['Period', '# of views'];
		var row = [];
		data.push(header);
		data0.push(header);
		<?php	
			$sql2 = "SELECT DATE(l.create_dt) AS visit_date, COUNT(*) AS count FROM log AS l WHERE DATE(l.create_dt) >= CURDATE() - INTERVAL 30 DAY GROUP BY DATE(l.create_dt) ORDER BY DATE(l.create_dt);";
			$sql3 = "SELECT YEAR(l.create_dt) AS visit_year, MONTH(l.create_dt) AS visit_month, COUNT(*) AS count FROM log AS l WHERE DATE(l.create_dt) >= CURDATE() - INTERVAL 12 MONTH GROUP BY MONTH(l.create_dt) ORDER BY YEAR(l.create_dt), MONTH(l.create_dt);";
			class LogCount extends Database_Object {
				protected static $table_name = 'logCount';
				protected static $db_fields = array('visit_date','visit_year','visit_month', 'count');
				public $count;
				public $visit_date;
				public $visit_year;
				public $visit_month;
			}
			$logc = logCount::find_by_sql($sql2);
			foreach($logc as $lc){?> 
				row =['<?php echo $lc->visit_date; ?>',<?php echo $lc->count; ?>];
				data.push(row);
			<?php }
			$logm = logCount::find_by_sql($sql3);
			foreach($logm as $lm){
				?> 
				row =['<?php echo $lm->visit_year."-".$lm->visit_month;?>',<?php echo $lm->count; ?>];
				data0.push(row);
			<?php }
		?>
		// Create and populate the data tables.
		var dataview = [];
		dataview[0] = google.visualization.arrayToDataTable(data);
		dataview[1] = google.visualization.arrayToDataTable(data0);
		var current = 0;
		// Create and draw the visualization.
		var options = {title: " ", areaOpacity: 0, animation:{duration: 1000, easing: 'out'}, chartArea: {width: '85%'}, legend:{position: 'bottom'}, backgroundColor: '#f5f5f5', vAxis:{title: '# of Views'}, hAxis:{title: 'Time Period'}, height: 480, areaOpacity: 0.0, series:{0:{color:'#333'}},seriesType: 'bars'};
		var chart = new google.visualization.ComboChart(document.getElementById('reportCombo2'));
		var button = document.getElementById('chart2Button');
		function drawChart() {
		  // Disabling the button while the chart is drawing.
		  button.disabled = true;
		  google.visualization.events.addListener(chart, 'ready',
			  function() {
				button.disabled = false;
				button.value = 'Switch to ' + (current ? '#Visits / 30 Days' : '#Visits / 12 Months');
			  });
		  options['title'] = 'Page Visits in the Last ' + (current ? '12 Months' : '30 Days');
		  chart.draw(dataview[current], options);
		}
		drawChart();
		button.onclick = function() {
		  current = 1 - current;
		  drawChart();
		}
	}
	catch(e){
		document.getElementById('reportCombo2').innerHTML = "Charts are unavailable at this time, Please reload the page or try again later";
	}
}

function displayChart1(){
	try{
		var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		var today = new Date();
		var currentMonth = today.getMonth();
		var currentYear = today.getFullYear();
		var statuses = [];
		var statusesWk = [];
		var data = [];
		var header = ['Months'];
		<?php			
			$pets = Pet::find_by_sql($sql);
			$stats = Status::find_by_sql("SELECT DISTINCT * FROM status;");
			foreach($stats as $stat){?>
				statuses.push(<?php echo "'".$stat->name."'";?>);
				header.push(<?php echo "'".$stat->name."'";?>);
				statusesWk.push(<?php echo $stat->status_wk;?>);
			<?php
			}
		?>
		data.push(header);
		for(var a=0; a < months.length; a++){
			var row = [];
			var month = months[a];
			var year = currentYear;
			if(currentMonth < a)
				year = year - 1;
			row.push(months[a]+"-"+year);
			for(var b=0; b < statuses.length; b++){ 
				var count = 0;
				var create_date = "";
				<?php
					foreach($pets as $p){ ?>
						var create_date = new Date("<?php echo $p->create_dt; ?>");
						//create_date = create_date.substr(0, 10); //if you treat echoed PHP date as ordinary string
						//cyear = create_date.substr(0,4);
						//cmonth = create_date.substr(5,2);
						var cyear = create_date.getFullYear();
						var cmonth = create_date.getMonth();
						//console.log(<?php echo $p->status_wk; ?> +" == "+ statusesWk[b] +" and "+  parseInt(cyear) +" == "+ year +" and "+ parseInt(cmonth) +" == "+ a);
						if(<?php echo $p->status_wk; ?> == statusesWk[b] &&  parseInt(cyear) == year && parseInt(cmonth) == a){
							count++;
							//console.log(true);
						}
					<?php
					}
				?>
				row.push(count);
			}
			data.push(row);
		}
		//console.log(data);
		var dataview = google.visualization.arrayToDataTable(data);
		var options = {title: " A Comparison of Pet Status' in the last 12 Months", chartArea: {width: '85%'}, areaOpacity: 0, legend:{position: 'bottom'}, backgroundColor: '#f5f5f5', vAxis:{title: '# of Pets'}, hAxis:{title: 'Time Period'}, height: 480, areaOpacity: 0.0, seriesType: 'bars'};
		var chart = new google.visualization.ComboChart(document.getElementById('reportCombo1'));
		chart.draw(dataview, options);
	}
	catch(e){
		document.getElementById('reportCombo1').innerHTML = "Charts are unavailable at this time, Please reload the page or try again later";
	}
}


</script>


<div class="container"><div class="row"><div class="col-xs-1">


</div><div <div class="col-xs-9">

<?php //echo $page->body; 

//var_dump($pageObj); //display the page
?>
<div id="reportCombo2"> </div><br>
<input type="button" id="chart2Button" class="btn btn-success btn-md btn-block"><br />
<div></div>
</div></div></div>
	
<?php require_once "../requires/template/footer.php"; ?>
	
