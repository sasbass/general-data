<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Ajax contol</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>

var wait = 0;

var totalTimer = 3000;

$(document).ajaxStart(function() {
	console.log('Ajax start!');
	wait = 1;
});

$(document).ajaxStop(function() {
	console.log('Ajax stop!');
	wait = 0;
});

$(document).ready(function(){
	setInterval(function(){
		if(wait === 0) {
			checkData();
			getJackpot();
		}
	}, totalTimer);
});



function checkData(){
	$.ajax({
		'type': 'POST',
		'url': 'ajax.php',
		'data': 'function=check',
		'success': function(data){
			var data = jQuery.parseJSON(data);
			//console.log(data);
		}
	});
}

function getJackpot(){
	$.ajax({
		'type': 'POST',
		'url': 'ajax.php',
		'data': 'function=getJackpot',
		'success': function(data){
			var data = jQuery.parseJSON(data);
			//console.log(data);
		}
	});
}

</script>
</head>
<body>


</body>
</html>