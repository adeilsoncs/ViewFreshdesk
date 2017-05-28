<!DOCTYPE html>
<html >
<title>Chamados</title>
<head>
<link rel="stylesheet" href="./dist/css/styles.css">

	<!-- Load jQuery library -->
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<!-- Load custom js -->
	<script type="text/javascript" src="./dist/js/custom.js"></script>
	<link href="./dist/css/animsition.min.css" rel="stylesheet">
</head>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.3/angular.min.js"></script>
<body class="animsition"> 

<ul id="main">
  <li style="float: left;"><a class="animsition-link" href="http://adeilson.com/"><img style="margin-right:10px" src="./dist/images/logo.png" onclick="openNav()" alt="logo" width="150"/></a></li>
  <li style="margin-left:60px"><h1 style="margin-right:350px;">Resolvendo Agora</h1></li>
</ul>

<div ng-app="myApp" ng-controller="customersCtrl">
	<table id="myTable" class="table-fill" align="center">
		<thead>
			<th class="text-left" style="width: 59px;">#</th>
			<th class="text-left" style="width: 900px;">Solicitação</th>
			<th class="text-left" style="width: 300px;">Assunto</th>
			<th class="text-left" style="width: 200px;">Criado por</th>
      		<th class="text-left" style="width: 200px;">Analista</th>
		</thead>
		<tbody class="table-hover">
		<tr ng-repeat="ticket in tickets" ng-show="ticket.status=='8'">
			<td><b><a href="http://portal.lexos.com.br/helpdesk/tickets/{{ ticket.display_id }}" target="_blank">#{{ ticket.display_id }}</a></b></td>
			<td><b>{{ ticket.subject | limitTo: 60 }}{{ticket.subject.length > 60 ? '...' : ''}}</b></td>
			<td><b>{{ ticket.custom_field | limitTo: 25 }}{{ticket.custom_field.length > 25 ? '...' : ''}}</b></td>
			<td><b>{{ ticket.requester_name | limitTo: 15}}{{ticket.requester_name.length > 15 ? '...' : ''}}</b></td>
      <td><b>{{ ticket.source_name}}</b></td>
		</tr>
		</tbody>
	</table>
  
	<br>
	<h2>Suporte Agendado</h2>
	<hr>
	<table class="table-fill" align="center">
		<thead>
			<th class="text-left" style="width: 59px;">#</th>
			<th class="text-left" style="width: 160px;">Vencimento</th>
			<th class="text-left">Solicitação</th>
			<th class="text-left" style="width: 200px;">Assunto</th>
			<th class="text-left" style="width: 200px;">Criado por</th>
      		<th class="text-right">#</th>
		</thead>
		<tbody class="table-hover">
		<tr ng-repeat="ticket in tickets" ng-show="ticket.status=='9'" ng-class="getClass(ticket.due_by)">
			<td><b><a href="http://portal.lexos.com.br/helpdesk/tickets/{{ ticket.display_id }}" target="_blank">#{{ ticket.display_id }}</a></b></td>
			<td><b>{{ ticket.due_by_br | limitTo: 16}}</b></td>
			<td><b>{{ ticket.subject | limitTo: 60 }}{{ticket.subject.length > 60 ? '...' : ''}}</b></td>
			<td><b>{{ ticket.custom_field }}</b></td>
			<td><b>{{ ticket.requester_name | limitTo: 15}}{{ticket.requester_name.length > 15 ? '...' : ''}}</b></td>
      		<td ng-if="getClass(ticket.due_by)==='Atrasado'" style="padding: 0px;width: 40px;"><img height="30" style="margin-left:5px;margin-right:5px;max-width: 120px;" src="./dist/images/bad.png" title="{{ ticket.due_by_br }}"/></td>
      		<td ng-if="getClass(ticket.due_by)==='Hoje'" style="padding: 0px;width: 40px;"><img height="30" style="margin-left:5px;margin-right:5px;max-width: 120px" src="./dist/images/nice.png" title="{{ ticket.due_by_br }}"/></td>
      		<td ng-if="getClass(ticket.due_by)==='Agendado'" style="padding: 0px;width: 40px;"><img height="30" style="margin-left:5px;margin-right:5px;max-width: 120px" src="./dist/images/agendado.png" title="{{ ticket.due_by_br }}"/></td>
		</tr>
		</tbody>
	</table>

	<br>
</div>
<footer><font size="3">Copyright &copy; adeilson.com</font></footer>
<script>
	var app = angular.module('myApp', []);

	app.controller('customersCtrl', function($scope, $http, $interval) {

		$scope.loadData = function () {
			$http.get("./backend/request.php").then(function successCallback(response) {
				$scope.tickets = response.data;
			}, function errorCallback(response) {
				console.log('error');
			});
		};
		$scope.loadData();	
		$interval(function() {
			$scope.loadData();
		},11000);

		//

		$scope.getClass = function(dateTicket){
			var dateAtual = new Date();
			var ticket = new Date(dateTicket);

			if((new Date(dateTicket).getTime() < new Date(dateAtual).getTime())){
				return 'Atrasado';
			}else if((new Date(dateTicket).getDate() + "/" + (new Date(dateTicket).getMonth() +1) === (new Date(dateAtual).getDate() + "/" + (new Date(dateAtual).getMonth() +1)))){
				return 'Hoje';
			}else{
				return 'Agendado';
			}
		};

	});
</script>
<script src="./dist/js/animsition.min.js" charset="utf-8"></script>
<script>
  $(document).ready(function() {
    $('.animsition').animsition();
  });
</script>
</body>
</html>

