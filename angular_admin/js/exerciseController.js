function ExerciseCtrl($scope, $http, $stateParams, $modal, $state, $sce, Pagination, $rootScope){
	   
    $scope.form_errors = false;
    $scope.equip_selected = [];
    $scope.muscles_selected = [];

    $scope.getExerciseList = function(){
		$scope.exercises = {};
        var num_rows =  $scope.PAGER_EXERCISE_ROWS;
        $scope.paginationExercise = Pagination.getNew(num_rows);

		var url = $scope.url_back+"/exercises/";
	    $http.get(url).success(function(respuesta){
	           $scope.exercises = respuesta;
                if($scope.exercises){
                    $scope.paginationExercise.numPages = Math.ceil($scope.exercises.length/$scope.paginationExercise.perPage);
                }
        });
    };

    $scope.getMuscleGroup = function(){
        $scope.muscles = {};

        var url = $scope.url_back+"/muscle_groups/";
        $http.get(url).success(function(response){
            var muscles = [];
            angular.forEach(response, function(muscle, index) {
                angular.forEach(muscle, function(m, item) {
                        muscles.push(m);

                });
            });
            $scope.muscles = muscles;
        });
    };

    //para search select box
    $scope.getMuscle_Array = function(){
        $scope.muscles_array = [];

        var url = $scope.url_back+"/muscle_groups/";
        $http.get(url).success(function(response){
            var muscles = [];
            muscles[0] = "";
            angular.forEach(response, function(muscle, index) {
                angular.forEach(muscle, function(m, item) {
                        muscles.push(m['name']);
                });
            });
            $scope.muscles_array = muscles;
        });
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



    $scope.getSubscriptionsGroup = function(){
        $scope.subscriptions = {};

        var url = $scope.url_back+"/subscriptionTypes/";
        $http.get(url).success(function(response){
            var subscriptions = [];
            angular.forEach(response, function(subscription, index) {
                angular.forEach(subscription, function(m, item) {
                        subscriptions.push(m);
                });
            });
            $scope.subscriptions = subscriptions;
        });
    };

    $scope.getEquipments = function(){
        $scope.equipments = {};

        var url = $scope.url_back+"/equipments/";
        $http.get(url).success(function(response){
            var equipments = [];
            angular.forEach(response, function(equip, index) {
                angular.forEach(equip, function(e, item) {
                        equipments.push(e);
                });
            });
            $scope.equipments = equipments;
        });
    };


    $scope.getObjetiveTypes = function(){
        $scope.objetives = {};

        var url = $scope.url_back+"/objetive_types/";
        $http.get(url).success(function(response){
            var objetives = [];
            angular.forEach(response, function(obj, index) {
                angular.forEach(obj, function(o, item) {
                        objetives.push(o);
                });
            });
            $scope.objetives = objetives;
        });
    };


    $scope.initForm = function(){
        $scope.resources = [];
    };

    $scope.submitAddExercise = function(){
        //$scope.equipments = {};
        
        if(!this.check_selects()){
            return false;
        }
        
        $scope.Exercise.subscription_type_id    = $scope.selSubscriptionType.id;
        $scope.Exercise.equipments              = $scope.equip_selected;
        $scope.Exercise.objetive_type_id        = $scope.selObjetiveType.id;
        $scope.Exercise.muscle_groups           = $scope.muscles_selected;

        if($scope.selSubscriptionType.id == 1 && !$scope.Exercise.cost){
            $scope.Exercise.cost = 0;
        }
        
        //var data = {Exercise: $scope.Exercise};
        
        $scope.formData = new FormData();
        angular.forEach($scope.resources, function(rec, index){
           $scope.formData.append('file',rec.file);
        });

        var url = $scope.url_back+"/exercises/add";

        //$scope.formData.append('data',JSON.stringify(data));
        var token =  $rootScope.sendToken();
        var data = angular.extend({}, {Exercse:$scope.Exercse} ,token);
        $scope.formData.append('data',JSON.stringify(data));
        

        $http.post(url, $scope.formData ,{
            headers: {'Content-Type': undefined}}).success(function(response){
        //console.log(response);
            if(response.success){
                $state.go('exercises.list');
            }
        });
        
    };

    $scope.check_selects = function(){
        //check muscle group
        //check objetive type
        $scope.form_errors = false;

        if($scope.selObjetiveType){
            $scope.Exercise.objetive_type_id = $scope.selObjetiveType.id;
        }else{
            $scope.form_errors = true;
            $scope.form_errors = {"objetive_type_id":["Please choose a objetive type."]};
        }

        if($scope.muscles_selected.length > 0){

        }else{
            $scope.form_errors = true;
            $scope.form_errors = {"muscle_group_id":["Please choose a muscle group."]};
        }
        
        //check subscription type
        if($scope.selSubscriptionType){
            $scope.Exercise.subscription_type_id = $scope.selSubscriptionType.id;
        }else{
            $scope.form_errors = true;
            $scope.form_errors = {"subscription_type_id":["Please choose a subscription type ."]};
        }

        if($scope.form_errors){
            return false;
        }else{
            return true;
        }
    };

    $scope.equipmentsSelected = function($id){
        if($scope.equip_selected.indexOf($id) == -1){
            $scope.equip_selected.push($id);
        }else{
            $scope.equip_selected.splice($scope.equip_selected.indexOf($id),1);
        }
    };


    $scope.musclesSelected = function($id){
        if($scope.muscles_selected.indexOf($id) == -1){
            $scope.muscles_selected.push($id);
        }else{
            $scope.muscles_selected.splice($scope.muscles_selected.indexOf($id),1);
        }
    };


    $scope.getEditExercise = function(){
        $scope.Exercise = {};
        
        var url = $scope.url_back+"/exercises/view/"+$stateParams.exercise_id;
        $http.get(url).success(function(response){
            $scope.Exercise = response.Exercise;
            
            var sub = $scope.subscriptions; 
            var p2 = $.map(sub,function(e){ return e.id; }).indexOf(response.Subscription_type.id);
            $scope.selSubscriptionType = $scope.subscriptions[p2];
           
            var objs = $scope.objetives; 
            var p3 = $.map(objs, function(e){ return e.id; }).indexOf(response.Objetive_type.id);
            $scope.selObjetiveType = $scope.objetives[p3];
            
            var equip = [];
            equip.push(response.ExerciseEquipment.map(function(e){return e.equipment_id; }));
            $scope.sel_equipments = equip[0];

            var musc = [];
            musc.push(response.ExerciseMuscleGroup.map(function(e){return e.muscle_group_id; }));
            $scope.sel_muscles = musc[0];

        });
    };


   


    $scope.deleteExerciseModal = function (exercise_id, exercise_name) {
        $scope.name = exercise_name;
        $scope.exercise_id = exercise_id;

        var modalInstance = $modal.open({
            templateUrl: '/fitness4polo/fitness4polo_admin/views/exercises/deleteExercise.html',
            controller: ModalInstanceCtrl,
            scope: $scope
        });
    };
    

    $scope.$on('deleteExercise', function(event, obj) {   
        var url = $scope.url_back+"/exercises/delete/"+obj.id;

        $http.get(url).success(function(response){
            if(response.success){
                $state.go("exercises.list");
            }
        });
    });



    $scope.$on("imagesUploaded", function (event, args) {

        /*if(args.file.type != "image/jpeg" && args.file.type != "image/png" && args.file.type != "image/jpg" ){
             alert("You can upload only jpg o png files");
             return;
        }
     
        if(args.file.size > 10485760){
             alert("You can't upload a files over 10MB");
             return;
        }
        */

        angular.forEach(args.files, function(file, index){
            //$scope.formData.append('file', file);
            $scope.file = file;
            //console.log($scope.formData);
            var fileReader = new FileReader();
            fileReader.readAsDataURL($scope.file);

            fileReader.onload = function(e){ 
                $scope.$apply(function(){
                    $scope.resources.push({name:file.name, type:'image', src: e.target.result, file: file });
                });
            };
        });

    });

    /**EDIT **/
    $scope.equipmentsChanged = function(equipment_id){

        if($scope.sel_equipments.indexOf(equipment_id) == -1){
            //add equipment to exercise
            var url = $scope.url_back+"/exercises/"+$stateParams.exercise_id+"/addEquipment/"+equipment_id;
            $http.get(url).success(function(response){
                if(response.success){
                }
            });
        }else{
            //remove equipment from exercise
            var url = $scope.url_back+"/exercises/"+$stateParams.exercise_id+"/removeEquipment/"+equipment_id;
            
            $http.get(url).success(function(response){
                if(response.success){
                   // $scope.sel_equipments.indexOf(equipment_id) 
                }
            });
        }
    };


    $scope.musclesChanged = function(muscle_group_id){

        if($scope.sel_muscles.indexOf(muscle_group_id) == -1){
            //add muscle group to exercise
            var url = $scope.url_back+"/exercises/"+$stateParams.exercise_id+"/addMuscleGroup/"+muscle_group_id;
            $http.get(url).success(function(response){
                if(response.success){
                }
            });
        }else{
            //remove muscle group from exercise
            var url = $scope.url_back+"/exercises/"+$stateParams.exercise_id+"/removeMuscleGroup/"+muscle_group_id;
            
            $http.get(url).success(function(response){
                if(response.success){
                   // $scope.sel_muscles.indexOf(muscle_group_id) 
                }
            });
        }
    };

  
    $scope.submitEditExercise = function(){
        //$scope.equipments = {};
        //$scope.Exercise.muscle_groups           = $scope.muscles_selected;
        $scope.Exercise.subscription_type_id    = $scope.selSubscriptionType.id;
        //$scope.Exercise.equipments              = $scope.equip_selected;
        $scope.Exercise.objetive_type_id        = $scope.selObjetiveType.id;

        var data = {Exercise: $scope.Exercise};

        var url = $scope.url_back+"/exercises/edit";

        $http.post(url, data).success(function(response){
            //console.log(response);
            if(response.success){
                $state.go('exercises.list');
            }
        });
        
    };


    $scope.removeResource = function($event, name_file){
        $(event.target).parent().parent().remove();

        var aux = $scope.resources;
        var index = $.map(aux, function(e){ return e.name; }).indexOf(name_file);
        $scope.resources.splice(index, 1);
    };
    

    $scope.addVideoModal = function(){
        var modalInstance = $modal.open({
            templateUrl: 'views/exercises/addVideo.html',
            controller: ModalInstanceCtrl,
            scope: $scope
        });
    };


    $scope.addVideo = function(url,name){
        $scope.resources.push({name:name, type:'video', src: url, file: url });
    };

}