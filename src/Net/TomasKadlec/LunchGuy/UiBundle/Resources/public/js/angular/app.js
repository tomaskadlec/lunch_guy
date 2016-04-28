(function() {

    var app = angular.module('obedar', [])
        .config(function ($interpolateProvider) {
            $interpolateProvider.startSymbol('{[').endSymbol(']}');
        });

    var state = {
        'restaurant' : undefined,
        'menu': undefined,
        'cached': undefined
    };

    app.controller('RestaurantsController', [ '$http', '$log', function($http, $log) {

        this.state = state;

        var controller = this;

        this.restaurants = [];

        $http.get(apiBaseUrl + '/restaurants')
            .success(function(data){
               controller.restaurants = data;
            });

        this.select = function(restaurant) {
            this.state.restaurant = restaurant;
            $http
                .get(apiBaseUrl + '/restaurants/' + this.state.restaurant + '/menu')
                .success(function (data) {
                    controller.state.menu = data.data.attributes.content;
                    controller.state.cached = data.data.attributes.cached;
                });
        }
    }]);

    app.controller('MenuController', function() {

        this.state = state;

    });

})();