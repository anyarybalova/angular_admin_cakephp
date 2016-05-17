function TrainningCtrl($scope, $http, $stateParams, $modal, $state, $sce, Pagination, $rootScope){
	   
    $scope.form_errors = false;
    $scope.equip_selected = [];
    $scope.muscles_selected = [];

    $scope.getTrainningList = function(){
        $scope.trainnings = {};
        var num_rows =  $scope.PAGER_TRAINNING_ROWS;
        $scope.paginationTrainning = Pagination.getNew(num_rows);
        $scope.paginationTrainning.numPages = 1;
        /*
        var url = $scope.url_back+"/trainnings/";
        $http.get(url).success(function(respuesta){
               $scope.trainnings = respuesta;
                if($scope.trainnings){
                    $scope.paginationTrainning.numPages = Math.ceil($scope.trainnings.length/$scope.paginationTrainning.perPage);
                }
        });
        */
    };

       //para search select box
    $scope.getSubscription_Array = function(){
        $scope.subscrip_array = [];

        var url = $scope.url_back+"/subscriptionTypes/";
        $http.get(url).success(function(response){
            var subscrip = [];
            subscrip[0] = "";
            angular.forEach(response, function(sub, index) {
                angular.forEach(sub, function(s, item) {
                        subscrip.push(s['name']);
                });
            });
            $scope.subscrip_array = subscrip;
        });
    };


    $scope.selectedDiv = function($event, id, name, type){

        $(".exercise_item").removeClass('div_active');

        if($(event.target).hasClass("exercise_item")){
            $(event.target).addClass('div_active');
        }else{
            if($(event.target).parent().parent().hasClass("exercise_item")){
                $(event.target).parent().parent().addClass("div_active");
            }
        }
        $scope.exercise_name = name;
        $scope.exercise_type = type;
    };
};