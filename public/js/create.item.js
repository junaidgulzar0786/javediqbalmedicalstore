(function(){
    var app = angular.module('tutapos', [ ]);

    app.controller("CreateItemCtrl", [ '$scope', '$http', function($scope, $http) {

        $scope.choices = [{id: 'batch1'}];
        $scope.addNewChoice = function() {
            var newItemNo = $scope.choices.length+1;
            $scope.choices.push({'id':'batch1'+newItemNo});
        };
        
        $scope.removeChoice = function() {
            var lastItem = $scope.choices.length-1;
            $scope.choices.splice(lastItem);
        };
  
    }]);

    app.controller("EditItemCtrl", [ '$scope', '$http','$window', function($scope, $http,$window) {
        $scope.choices = [];
        addEventListener('load', load, false);
        var item_id;
        function load(){
            
            item_id = document.getElementById("get_item_id").value;
            $http.post('/javediqbalmedicalstore/public/api/item-batch', { item_id:item_id }).
            success(function(data, status, headers, config) {
                $scope.choices = data;
            });
            $scope.addNewChoice = function() {
                var newItemNo = $scope.choices.length+1;
                $scope.choices.push({'id':'batch1'+newItemNo});
            };
        
            $scope.removeChoice = function(id) {
                 var deleteInventory = $window.confirm('Are you absolutely sure you want to delete?');

                if (deleteInventory) {
                    if(isEmpty(id)){
                        var lastItem = $scope.choices.length-1;
                        $scope.choices.splice(lastItem);
                        return;
                    }
                    $http.post('/javediqbalmedicalstore/public/api/item-inventory-destroy', { inventory_id:id }).
                    success(function(data, status, headers, config) {
                        //$scope.choices = data;
                        if(data == 1){
                            var lastItem = $scope.choices.length-1;
                            $scope.choices.splice(lastItem);
                        }else if(data == 0){
                            var lastItem = $scope.choices.length-1;
                            $scope.choices.splice(lastItem);

                        }
                    });
                }
            };

        }
       
       
    }]);
    app.controller("InventoryItemCtrl", [ '$scope', '$http','$window', function($scope, $http,$window) {
        $scope.choices = [];
        $scope.totalQuantity=0;
        addEventListener('load', load, false);
        var item_id;
        function load(){
            
            item_id = document.getElementById("get_item_id").value;
            $http.post('/javediqbalmedicalstore/public/api/item-batch', { item_id:item_id }).
            success(function(data, status, headers, config) {
                $scope.choices = data;
                angular.forEach($scope.choices, function(choice, index) {
                    $scope.totalQuantity += choice.in_out_qty;
                });
            });
            $scope.addNewChoice = function() {
                var newItemNo = $scope.choices.length+1;
                $scope.choices.push({'id':'batch1'+newItemNo});
            };
        
            $scope.removeChoice = function(id,quantity) {
                 var deleteInventory = $window.confirm('Are you absolutely sure you want to delete?');

                if (deleteInventory) {
                    if(isEmpty(id)){
                        var lastItem = $scope.choices.length-1;
                        $scope.choices.splice(lastItem);
                        return;
                    }
                    $http.post('/javediqbalmedicalstore/public/api/item-inventory-destroy', { inventory_id:id }).
                    success(function(data, status, headers, config) {
                        //$scope.choices = data;
                        if(data == 1){
                            $scope.totalQuantity = $scope.totalQuantity - quantity;
                            var lastItem = $scope.choices.length-1;
                            $scope.choices.splice(lastItem);
                        }else if(data == 0){
                            var lastItem = $scope.choices.length-1;
                            $scope.choices.splice(lastItem);

                        }
                    });
                }
            };

        }
       
       
    }]);
    function isEmpty(str) {
        return (!str || 0 === str.length);
    }
})();