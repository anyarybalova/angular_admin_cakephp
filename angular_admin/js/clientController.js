
function ClientCtrl($scope, $http, $stateParams, $modal, $state, $sce, Pagination, $rootScope){
	   
    $scope.form_errors = false;
    
    
    $scope.getClientList = function(){

		$scope.contracts = {};
        var num_rows =  $scope.PAGER_CLIENT_ROWS;
        $scope.paginationClient = Pagination.getNew(num_rows);

		var url = $scope.url_back+"/clients/";
	    $http.get(url).success(function(respuesta){
            if(respuesta.content){
	           $scope.clients = respuesta.content;
               $scope.paginationClient.numPages = Math.ceil($scope.clients.length/$scope.paginationClient.perPage);
            }
        });
    };



    $scope.getCountryList = function(){
        $scope.countries = []; 

        var url =  $scope.url_back + "/countries";
        $http.get(url).success(function(response){
            var countries = [];
            angular.forEach(response, function(country, index) {
                angular.forEach(country, function(c, item) {
                        countries.push(c);
                });
            });
            $scope.countries = countries;
        });
    };

    $scope.initForm = function(){
        $scope.fd = new FormData();
    };

    $scope.submitAddClient = function(){
       // if($scope.checkData()){
            $scope.Client.country_id = $scope.selCountry.id;
            console.log($scope.Client);
            return;
           // var data = {Client:$scope.Client};

            var url = $scope.url_back+"/clients/add";

            $scope.fd.append('data',JSON.stringify(data));
            var token =  $rootScope.sendToken();
            var data = angular.extend({}, {Client:$scope.Client} ,token);
            $scope.fd.append('data',JSON.stringify(data));
            
            $http.post(url, $scope.fd ,{
                headers: {'Content-Type': undefined}}).success(function(response){
                if(response.success){
                    $scope.users = response.users;
                    $state.go("clients.list");
                }else{
                    $scope.form_errors = response.errors;
                }

            }); 
            
            
        /*}else{
            return false;
        }*/
    };

    $scope.checkData = function(){
        var form_errors = [];
        var cliente = $scope.Client;
        
        if(!cliente.first_name){
            form_errors['first_name'] = true;
        }
        if(!cliente.last_name){
            form_errors['last_name'] = true;
        }
        if(!cliente.phone){
            form_errors['phone'] = true;
        }
        $scope.form_errors = form_errors;
        return (form_errors.length == 0);
    }

    $scope.deleteModal = function (client_id, first_name, last_name) {
        $scope.name = first_name +" "+ last_name;
        $scope.client_id = client_id;

        var modalInstance = $modal.open({
            templateUrl: 'views/clients/delete_client.html',
            controller: ModalInstanceCtrl,
            scope: $scope
        });
    };


    $scope.$on('deleteClient', function(event, obj) {     
        var url =  $scope.url_back + "/clients/delete/"+obj.id;
            $http.get(url).success(function(respuesta){
        });
    });


    $scope.$on("fileSelected", function (event, args) {
        $scope.$apply(function () {            
            //add the file object to the scope's files collection
            //$scope.files.push(args.file);
 
        });
        $scope.fd = new FormData();
        
        if(args.file.type != "image/jpeg" && args.file.type != "image/png" && args.file.type != "image/jpg" ){
             alert("You can upload only jpg o png files");
             return;
        }
     
        if(args.file.size > 10485760){
             alert("You can't upload a files over 10MB");
             return;
        }
            $scope.fd.append('file',args.file);
            $scope.file = args.file;
             
            var fileReader = new FileReader();
            fileReader.readAsDataURL($scope.file);
            fileReader.onload = function(e) { 
                $scope.photo_url = e.target.result;
                $scope.$apply(function(){
                    $scope.photo_url = e.target.result;
                });

            };
        
    });



    $scope.getClient = function(){
        $scope.client_id = $stateParams.client_id;

        var url = $scope.url_back + "/clients/view/"+$scope.client_id;
        $http.get(url).success(function(response){
            $scope.Client = response.content.Client;

            var sub = $scope.countries;
            var p = $.map(sub, function(e){ return e.id; }).indexOf(response.content.Country.id); 
            $scope.selCountry = $scope.countries[p];  

            var date = $scope.Client.birth_date;
            date = date.split("-").join("/");
            $scope.Client.birth_date = new Date(date);
        });  
    };


    $scope.submitEditClient = function(){
       // if($scope.checkData()){
            $scope.Client.country_id = $scope.selCountry.id;

            var url = $scope.url_back+"/clients/edit/"+$scope.client_id;

            $scope.fd.append('data',JSON.stringify(data));
            var token =  $rootScope.sendToken();
            var data = angular.extend({}, {Client:$scope.Client} ,token);
            $scope.fd.append('data',JSON.stringify(data));
            
                
            $http.post(url, $scope.fd ,{
                headers: {'Content-Type': undefined}}).success(function(response){
                if(response.success){
                    $scope.users = response.users;
                    $state.go("clients.list");
                }else{
                    $scope.form_errors = response.errors;
                }
            }); 
            
        /*}else{
            return false;
        }*/
    };

 

}