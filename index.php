<head>
	<link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" />
	<link rel="stylesheet" href="https://unpkg.com/tippy.js@6/themes/light.css" />
	<link rel="stylesheet" type="text/css" href="/dashboard/css/extra.css" />
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
	<script src="https://unpkg.com/@popperjs/core@2"></script>
	<script src="https://unpkg.com/tippy.js@6"></script>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h1>Systems Status Page</h1>
			</div>
		</div>
		<div class="row clearfix">
			<div class="col-md-12 column">
				<div class="panel panel-success">
					<div class="panel-heading">
						<h3 class="panel-title">
							<div class="status-left">"Most" Systems Operational</div>
							<div class="status-right">Refreshed <span id="refreshTimer">0</span> seconds ago</div>
						</h3>
					</div>                
				</div>
				<div class="card border-light mb-3">
					<div class="card-header">Horizon Host Servers</div>
					<div class="card-body">
						<div id="horizon" class="card-text"></div>
						<div id="horizon-counter" class="counter"></div>
					</div>
				</div>
				<div class="card border-light mb-3">
					<div class="card-header">Remote Apps Host Servers</div>
					<div class="card-body">
						<div id="rdsh" class="card-text"></div>
						<div id="rdsh-counter" class="counter"></div>
					</div>
				</div>
				<div class="card border-light mb-3">
					<div class="card-header">Application Servers</div>
					<div class="card-body">
						<p id="application" class="card-text"></p>
					</div>
				</div>
				<div class="card border-light mb-3">
					<div class="card-header">Tomcat Servers</div>
					<div class="card-body">
						<p id="tomcat" class="card-text"></p>
					</div>
				</div>
				<div class="card border-light mb-3">
					<div class="card-header">VPN Tunnels</div>
					<div class="card-body">
						<p id="vpn" class="card-text"></p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		///////////////////////////////////////
		////////// Start the timers! //////////
		///////////////////////////////////////	
		
		// Initialize objects
		var timer = null;
		
		// Function to get all of the sensors and populate them
		function refreshData() {
			//getData([PRTG Sensor Id], [tooltip size], [page location by ID]);
			getData('0001', 'small', vpn);
			getData('0002', 'small', application);
			getData('0003', 'small', tomcat);
			getData('0004', 'large', rdsh);
			getData('0005', 'large', horizon);
		}
		
		function getData(id, size, location) {
			// make request to the server
			$.post('includes/getJSON.php', {id:id}, function(data) { 
				var array = createArray(JSON.parse(data));
				getSensors(location, size, array);
			});
		}
		
		function createArray(json) {
			var array = [];
			for (var item in json) {
				array.push([item, json [item]]);
			}
			array = array.sort();
			return array;
		}
		
		function getCounter(location, totalUsers) {
			// Create Active User Counter for Large sections
			var counter = document.getElementById(location.id + '-counter');
			var html = "<b>" + totalUsers + "</b> Active Sessions";
				
			if (counter != null) {
				counter.innerHTML = html;
			} else {
				counter = document.createElement("div");
				counter.className = 'counter';
				counter.id = location.id + '-counter';
				counter.innerHTML = html;
				location.appendChild(counter);
			}
		}
		
		function getSensors(location, size, data) {
			// Initialize
			var totalUsers = 0;

			// Loop over all the items in the array
			for (var i = 0; i < data.length; i++) {
				
				var name = data[i][0];
				var subArray = data[i][1];

				if (location == application) {var status = subArray['Application Server'].Status;}
				if (location == tomcat) {var status = subArray['HTTPS'].Status;}
				if (location == vpn) {var status = subArray.Status;}

				// Set element ID and remove special characters
				var newId = name.split(/[-._ ]/g).join("");
				var element = document.getElementById(newId);
				
				if (status == 'Up') { var health = 'success'; }
				else if (status == 'Warning') { var health = 'warning'; }
				else {var health = 'danger'}
				
				if (size == 'small') {
					if (element != null) {
						// Update existing element
						element.className = "badge info-small badge-" + health;
					} else {
						// Create new element
						element = document.createElement("span");
						element.title = name;
						element.id = newId;
						element.className = "badge info-small badge-" + health;
						element.innerHTML = "<wbr>";
						location.appendChild(element);
						tippy(element, {content(reference){const title = reference.getAttribute('title'); reference.removeAttribute('title'); return title;}, theme: 'light', appendTo: element});
					}
					
				} else if (size == 'large') {
					// Set the initial values
					var stat_cpu = subArray['CPU Load'].Value;
					var stat_mem = subArray['Memory Usage'].Value;
					var stat_dsk = subArray['Disk Usage'].Value;
					var stat_net = subArray['Network'].Value;
					var stat_rdp = subArray['RDP Service'].Status;
					var stat_usr = subArray['RDP Sessions'].Value;
					var stat_prt = subArray['Print Spooler'].Status;
					
					totalUsers += parseInt(stat_usr, 10);
					
					if (stat_cpu < 75){
						if (stat_mem < 75) {health = 'success';}
						else if (stat_mem > 74 && stat_mem < 95) {health = 'warning';}
						else {health = 'danger';}} 
					else if (stat_cpu > 74 && stat_cpu < 95) {health = 'warning';} 
					else {health = 'danger';}
					if (stat_prt != 'Up') { health = 'danger';}
					
					var html = '<b>' + name + '</b><br /><b>CPU Usage: </b>' + stat_cpu + '%<br><b>Mem Usage: </b>' + stat_mem + '%<br><b>Disk Usage: </b>' + stat_dsk + '%<br><b>Network: </b>' + stat_net + ' kbps<br><b>RDP Service: </b>' + stat_rdp + '<br><b>RDP Sessions: </b>' + stat_usr + ' Users<br><b>Print Spooler: </b>' + stat_prt;

					if (element != null) {
						// Update existing element
						element.className = "badge info-small badge-" + health;
						element._tippy.setContent(html);
					} else {
						// Create new element
						element = document.createElement("span");
						element.id = newId;
						element.className = "badge info-small badge-" + health;
						element.innerHTML = "<wbr>";
						location.appendChild(element);		
						tippy(element, {allowHTML: true, theme: 'light', content: html, appendTo: element});
					}
				}
			}
			// Get the Active Session counter
			if (size == 'large') {getCounter(location, totalUsers);}
		}
		
		function automaticUpdater() {
			var refresh = setInterval(function() {
                timer++;
                $('#refreshTimer').html(timer); 
				clearInterval(refresh);
				automaticUpdater();
                if (timer == 30) {
                    timer = null;
					refreshData();
                }
            }, 1000);
		}
		
		//////////////////
		// Initial Load //
		//////////////////
		$(document).ready(function() {
			automaticUpdater();
			refreshData();
		 });
		 
	</script>	
</body> 