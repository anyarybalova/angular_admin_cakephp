
function UserCtrl($scope, $http, $stateParams, $modal, $state, $sce){
	
    $scope.form_errors = false;

    $scope.getUserList = function(){
		$scope.users = {};

		var url = $scope.url_back+"/users/";
	    $http.get(url).success(function(response){
	           $scope.users = response.content;
        });
    };

    $scope.getRolesList = function(){
    	$scope.countries = []; 

		var url =  $scope.url_back + "/roles";
	       $http.get(url).success(function(response){
	        var roles = [];
            angular.forEach(response, function(role, index) {
                angular.forEach(role, function(r, item) {
                        roles.push(r);
                });
            });

            $scope.roles = roles;
	    });
    };
    
    $scope.getProfileUser = function(){

        var url =  $scope.url_back + "/users/view/"+$stateParams.user_id;
            $http.get(url).success(function(response){
                if(response.success){
                    $scope.User = response.content.User;

                    var result = $.grep($scope.roles, function(e){ return e.id == $scope.User.role_id; });
                    //console.log(result[0]);

                    $scope.role_id = result.shift();
                }
        });
    };


    $scope.submitAddUser = function(){

        if($scope.User && $scope.role_id){
            $scope.User.role_id = $scope.role_id.id;
        }else{
            return;
        }
    	
    	var data = {User:$scope.User};
    	var url = $scope.url_back+"/users/add";
	    $http.post(url, data).success(function(response){
            if(response.success){
           		$scope.users = response.users;
           		$state.go("admin.users");
       		}else{
                $scope.form_errors = response.errors;
                return false;
            }
        });	
    

    };


    $scope.deleteUserModal = function (user_id, login) {
    	$scope.login = login;
        $scope.user_id = user_id;

        var modalInstance = $modal.open({
            templateUrl: 'views/users/delete_user.html',
            controller: ModalInstanceCtrl,
  			scope: $scope
        });
    };


    $scope.$on('deleteUser', function(event, obj) {   

    	var url =  $scope.url_back + "/users/delete/"+obj.id;
	        $http.get(url).success(function(respuesta){
	    });
    });

}