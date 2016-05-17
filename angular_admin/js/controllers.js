/**
 * INSPINIA - Responsive Admin Theme
 * Copyright 2015 Webapplayers.com
 *
 */

/**
 * MainCtrl - controller
 */
function MainCtrl($scope, $http, FitConstants, $cookieStore, $rootScope, $modal, $state) {

    this.userName           = 'Administrator';
    this.helloText          = 'Welcome in Fitnees For Polo';
    this.descriptionText    = 'Administration';

    $scope.PAGER_EXERCISE_ROWS  = 5;
    $scope.PAGER_CLIENT_ROWS    = 5;

    $scope.url_back = FitConstants.url_back;
    $scope.path     = FitConstants.url_path;
    $scope.url_img  = FitConstants.url_user_img;
    $scope.url_exercise_img = FitConstants.url_exercise_img;


    $scope.user = $cookieStore.get('user');
    if(!$scope.user){
        $scope.user = [];
    }

    $scope.refresh = function() {
       $state.go($state.current, {}, {reload: true});
    };


    $scope.checkLogin = function(){
        if(!$scope.user.id){
            //window.location.href = $scope.path + "/login.html";
            $state.go("login");
        }
    };

    $scope.signinUser = function(){

        var data = {User:$scope.User};
        
        var url =  $scope.url_back +"/users/signin/";
        $http.post(url, data).success(function(response){
            if(response.success){
                //console.log(response);
                var user_login =  response.content.User;
                
                $scope.user['id']       = user_login.id;
                $scope.user['role_id']  = user_login.role_id;
                $scope.user['token']    = user_login.token;

                var user = {
                    id :        user_login.id,
                    role_id :   user_login.role_id,
                    token :     user_login.token
                };

                $cookieStore.put('user', user);
                //$state.go('index.main',{}, {reload: true});
                window.location.href = $scope.path + "/index.html";


            }else{
                // $scope.username = "";
                 $scope.pass = "";
            }
        });

    };


    $scope.logout = function(){
        $scope.user = [];
        $cookieStore.put('user', []);

        $state.go('login');
    };

    $rootScope.sessionExpired = function(){

        var modalInstance = $modal.open({
            templateUrl: 'views/common/sessionExpired.html',
            controller: ModalInstanceCtrl,
            scope: $scope
        });

        $scope.user = [];
        $cookieStore.put('user', []);
    };


    $rootScope.sendToken = function(){
        if($scope.user.token){
            var User = {
                'role'  : 'admin',
                'id'    : $scope.user.id,
                'token' : $scope.user.token,
                'deviceID' : "1234",
                'deviceOS' : "xp"
            };
        }else{
            var User = null;
        }

        
        return {User: User};
    };


    $scope.cancelBtn = function(sref){
        $scope.sref = sref;

        var modalInstance = $modal.open({
            templateUrl: 'views/common/cancelForm.html',
            controller: ModalInstanceCtrl,
            scope: $scope
        });
    };
};



function ModalInstanceCtrl ($scope, $modalInstance, $state) {

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };

    $scope.ok = function (func_ok,id) {
        $scope.$emit(func_ok, {
            id: id
        });
        $state.go($state.current, {}, {reload: true});
        $modalInstance.close();
    };
};


function Pagination($scope, Pagination){
    //var num_rows = 4
    //$scope.pagination = Pagination.getNew(num_rows);
};


/**
 * draggablePanels - Controller for draggable panels 
 */
function draggablePanels($scope) {

    $scope.sortableOptions = {
        connectWith: ".connectPanels",
        update: function(e, ui) {

            //console.log("update");
        }
    };

};


angular
    .module('fitness4polo')
    .controller('Pagination', Pagination)
    .controller('ExerciseCtrl', ExerciseCtrl)
    .controller('UserCtrl', UserCtrl)
    .controller('ClientCtrl', ClientCtrl)
    .controller('MainCtrl', MainCtrl)
    .controller('TrainningCtrl', TrainningCtrl)
    .controller('draggablePanels',draggablePanels)