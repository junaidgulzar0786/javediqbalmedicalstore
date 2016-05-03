(function(){
    var app = angular.module('tutapos', [ ]);

    app.controller("SearchItemCtrl", [ '$scope', '$http', function($scope, $http) {
        console.log($scope.add_payment);
        $scope.items = [ ];
        $http.get('api/item').success(function(data) {
            $scope.items = data;
        });
        $scope.saletemp = [ ];
        $scope.newsaletemp = { };
        $scope.submitDisable = true;
        $scope.addPaymentChange = function(){
            console.log($scope.total_payment);
            if($scope.add_payment > 0){
                $scope.submitDisable = false;
            }
            else{
                $scope.submitDisable = true;
            }
        }
        $http.get('api/saletemp').success(function(data, status, headers, config) {
            $scope.saletemp = data;
        });
        $scope.addSaleTemp = function(item, newsaletemp) {
            
            $http.post('api/saletemp', { item_id: item.id, cost_price: item.cost_price, selling_price: item.selling_price }).
            success(function(data, status, headers, config) {
                $scope.saletemp.push(data);
                    $http.get('api/saletemp').success(function(data) {
                    $scope.saletemp = data;
                    });
            });
        }
        $scope.updateSaleTemp = function(newsaletemp) {
            var costPriceDis = (newsaletemp.item.cost_price * newsaletemp.quantity) * ( newsaletemp.discount / 100 ) ;
            $http.put('api/saletemp/' + newsaletemp.id, { quantity: newsaletemp.quantity,discount: newsaletemp.discount, total_cost: (newsaletemp.item.cost_price * newsaletemp.quantity) - costPriceDis,
                total_selling: (newsaletemp.item.selling_price * newsaletemp.quantity) - - costPriceDis }).
            success(function(data, status, headers, config) {
                
                });
        }

        $scope.updateSaleTempDiscount = function(newsaletemp) {
            
            var costPriceDis = (newsaletemp.item.cost_price * newsaletemp.quantity) * ( newsaletemp.discount / 100 ) ;
            $http.put('api/saletemp/' + newsaletemp.id, { quantity: newsaletemp.quantity,discount: newsaletemp.discount, total_cost: (newsaletemp.item.cost_price * newsaletemp.quantity) - costPriceDis ,
                total_selling: (newsaletemp.item.selling_price * newsaletemp.quantity) - costPriceDis }).
            success(function(data, status, headers, config) {
                });
        }

        $scope.removeSaleTemp = function(id) {
            $http.delete('api/saletemp/' + id).
            success(function(data, status, headers, config) {
                $http.get('api/saletemp').success(function(data) {
                        $scope.saletemp = data;
                        });
                });
        }
        $scope.sum = function(list) {
            var total=0;
            angular.forEach(list , function(newsaletemp){
                var discount = (newsaletemp.item.selling_price * newsaletemp.quantity) * (newsaletemp.discount/100);
                total+= parseFloat(newsaletemp.item.selling_price * newsaletemp.quantity)-discount;
            });
            return total;
        }

    }]);
})();