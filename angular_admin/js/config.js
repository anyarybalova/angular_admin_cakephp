/**
 * INSPINIA - Responsive Admin Theme
 * Copyright 2015 Webapplayers.com
 *
 * Inspinia theme use AngularUI Router to manage routing and views
 * Each view are defined as state.
 * Initial there are written state for all view in theme.
 *
 */
function config($stateProvider, $urlRouterProvider, $ocLazyLoadProvider, $httpProvider, $locationProvider) {
    $urlRouterProvider.otherwise("/index/main");

    $httpProvider.interceptors.push('APIInterceptor');

    $ocLazyLoadProvider.config({
        // Set to true if you want to see what and when is dynamically loaded
        debug: false
    });

    /*$locationProvider.html5Mode({
        enabled: true,
        requireBase: false
    });*/
    //console.log($locationProvider);

    $stateProvider

        .state('index', {
            abstract: true,
            url: "/index",
            templateUrl: "views/common/content.html",
        })
        .state('index.main', {
            url: "/main",
            templateUrl: "views/main.html",
            data: { pageTitle: 'HOME' }
        })
        .state('login', {
            url: "/login",
            templateUrl: "login.html",
        })
        .state('admin', {
            abstract: true,
            url: "/users",
            templateUrl: "views/common/content.html",
        })
        .state('admin.users', {
            url: "/users",
            templateUrl: "views/users/users.html",
            data: { pageTitle: 'Users' }
        })
        .state('admin.add_user', {
            url: "/add_user",
            templateUrl: "views/users/add_user.html",
            data: { pageTitle: 'Add user' }
        })
        .state('admin.edit_user', {
            url: "/edit_user/:user_id",
            templateUrl: "views/users/edit_user.html",
            data: { pageTitle: 'Edit user' }
        })
        .state('admin.roles', {
            url: "/roles",
            templateUrl: "views/users/roles.html",
            data: { pageTitle: 'Roles' }
        })
        .state('clients', {
            abstract: true,
            url: "/clients",
            templateUrl: "views/common/content.html",
        })
        .state('clients.list', {
            url: "/clients_list",
            templateUrl: "views/clients/clients.html",
            data: { pageTitle: 'Clients' }
        })
        .state('clients.add_client', {
            url: "/add_client",
            templateUrl: "views/clients/add_client.html",
            data: { pageTitle: 'Add client'},
            resolve: {
                loadPlugin: function ($ocLazyLoad) {
                    return $ocLazyLoad.load([
                        {
                            name: 'datePicker',
                            files: ['css/plugins/datapicker/angular-datapicker.css','js/plugins/datapicker/datePicker.js']
                        }
                        ]);
                        }
                }
        })
        .state('clients.edit_client', {
            url: "/edit_client/:client_id",
            templateUrl: "views/clients/edit_client.html",
            data: { pageTitle: 'Edit client'},
            resolve: {
                loadPlugin: function ($ocLazyLoad) {
                    return $ocLazyLoad.load([
                        {
                            name: 'datePicker',
                            files: ['css/plugins/datapicker/angular-datapicker.css','js/plugins/datapicker/datePicker.js']
                        }
                        ]);
                        }
                }
        })
        .state('exercises', {
            abstract: true,
            url: "/exercises",
            templateUrl: "views/common/content.html",
        })
        .state('exercises.list', {
            url: "/exercises",
            templateUrl: "views/exercises/list_exercises.html",
            data: { pageTitle: 'Exercises' }
        })
        .state('exercises.new', {
            url: "/new",
            templateUrl: "views/exercises/new_exercise.html",
            data: { pageTitle: 'New exercise' },
            resolve: {
                loadPlugin: function ($ocLazyLoad) {
                    return $ocLazyLoad.load([
                        {
                            insertBefore: '#loadBefore',
                            name: 'localytics.directives',
                            files: ['css/plugins/chosen/chosen.css','js/plugins/chosen/chosen.jquery.js','js/plugins/chosen/chosen.js']
                        },
                        ]);
                        }
                }
        })
        .state('exercises.edit', {
            url: "/edit/:exercise_id",
            templateUrl: "views/exercises/edit_exercise.html",
            data: { pageTitle: 'Edit exercise' },
            resolve: {
                loadPlugin: function ($ocLazyLoad) {
                    return $ocLazyLoad.load([
                        {
                            insertBefore: '#loadBefore',
                            name: 'localytics.directives',
                            files: ['css/plugins/chosen/chosen.css','js/plugins/chosen/chosen.jquery.js','js/plugins/chosen/chosen.js']
                        },
                        ]);
                        }
                }
        })
        .state('sponsors', {
            abstract: true,
            url: "/sponsors",
            templateUrl: "views/common/content.html",
        })
        .state('sponsors.list', {
            url: "/sponsors",
            templateUrl: "views/sponsors/list_sponsors.html",
            data: { pageTitle: 'Sponsors' }
        })
        .state('sponsors.new', {
            url: "/new",
            templateUrl: "views/sponsors/new_sponsor.html",
            data: { pageTitle: 'New sponsor' }
        })
        .state('trainnings', {
            abstract: true,
            url: "/trainnings",
            templateUrl: "views/common/content.html",
        })
        .state('trainnings.list', {
            url: "/trainnings",
            templateUrl: "views/trainnings/list_trainnings.html",
            data: { pageTitle: 'Trainning Plans' }
        })
        .state('trainnings.new', {
            url: "/new",
            templateUrl: "views/trainnings/new_trainning.html",
            data: { pageTitle: 'New trainning plan' }
        })
        .state('trainnings.new_workout', {
            url: "/new_workout",
            templateUrl: "views/trainnings/new_workout.html",
            data: { pageTitle: 'New Workout Session' }
        });

      
}
angular
    .module('fitness4polo')
    .config(config)
    .run(function($rootScope, $state) {
        $rootScope.$state = $state;
    })
    .service('APIInterceptor', function($rootScope) {
    var service = this;

    service.request = function(config) { 
        var url = config.url;
        if(url.indexOf("http") !=-1){

            if(config.method == "GET"){
                config.params = $rootScope.sendToken();
            }else{
                if((url.indexOf("signin") ==-1) && (url.indexOf("add") ==-1) && (url.indexOf("edit") ==-1)){
                    var dataSend = config.data;
                    var token = $rootScope.sendToken();
                    var data = angular.extend({}, token , dataSend);
                    config.data = data; 

                }
            }
        }
        
        return config;
    };

    service.response = function(response) {
        if(angular.isObject(response.data)){
            if (response.data.NotAccess) {
                 $rootScope.sessionExpired();
            }
        }
        return response;
        }
    });
