angular.module('ui.bootstrap.demo').controller('ModalSelectDetailCtrl', function ($rootScope, $scope, $uibModalInstance, tmp) {
  
  $scope.dataForTheTree = ppp; //$scope.dataForTheTree = aaa;
  
  $scope.folder = {};
  
  var theitem = tmp.item;
  
  $scope.pricelist_current = 1; 
  $scope.pricelist_older = null;
  
  if(theitem.isnew === false){
    angular.forEach(theitem.needs, function(value, key) {
      if (value.mode == 'input' || value.mode == 'hidden') {
        value.value = theitem[value.code];
      }
    });
  }
  else{
    theitem.pricelist_current = null;
    theitem.pricelist_older = null;
    theitem.price_manual = 0;
    theitem.price_current = null;
    theitem.price_older = null;
    theitem.price_nullValue = null;
    theitem.price_mode = 'current'; // 'current', 'older', 'manual', 'nullValue'
    theitem.price = null;
    theitem.total_price = null;
    theitem.total_row_price = null;
    
    angular.forEach(theitem.needs_default, function(value, key) {
      angular.forEach(theitem.needs, function(v, k) {
        if (v.code == value.code) {
          v.value = value.value;
        }
      });
    });
    
    angular.forEach(theitem.details_default, function(value, key) {
      angular.forEach(theitem.details, function(v, k) {
        if (v.code == value.code) {
          v.selected = value.value;
        }
      })
    });
  }
  
  //todo: si potrebbe generalizzare needs --> needs_detail
  angular.forEach(theitem.needs, function(value, key) {
    if (value.mode == 'input' || value.mode == 'hidden') {
      theitem[value.code] = value.value;
    }
    if (value.mode == 'calc') {
      if (value.method == 'calc') {
        $scope.$watch(value.watch, function(v) {
          theitem[value.code] = $scope.$eval(value.method_args);
        });
      }
    }
    if (value.mode == 'gluecode') {
      if (value.method == 'str_lpad') {
        $scope.$watch(value.watch, function(v) {
          var p = '';
          angular.forEach(value.method_args.elements, function(va, ka) {
            var n = round($scope.$eval(va.target), va.round, 0);
            p = p + padLeft(n, value.method_args.lngth, value.method_args.str);
          });
          theitem[value.code] = p;
        });
      }
    }
  });
      
  angular.forEach(theitem.details, function(detail, detailKey) {
    angular.forEach(detail.options, function(option, optionKey) {
      if(theitem.isnew !== false){
        option.pricelist_current= null;
        option.pricelist_older= null;
        option.price_manual= 0;
        option.price_current= null;
        option.price_older= null;
        option.price_nullValue= null;
        option.price_mode = 'current'; // 'current', 'older', 'manual', 'nullValue'
        option.price= null;
      }
      
      if(option.method == 'area4CalcPrices') {
        option = setOptionPriceCost(option);
        
        $scope.$watch('tmp.item.area',function(newValue, oldValue) {
          var ma = option.method_args;
          if(newValue) {
            if(ma.method == 'maxOf'){
              var arr = [];
              angular.forEach(ma.method_args.values, function(a, n) {
                if(a.indexOf('.') > 0){
                  arr.push($scope.$eval(a));
                }
                else{
                  arr.push(ma[a]);
                }
              });
              option[ma.method_args.ret] = getMaxOfArray(arr);
            }
            else if(ma.method == 'minOf'){
              
            }
            else if(ma.method == 'fixed'){
              option[ma.method_args.ret] = $scope.$eval(a);
            }
            else if(ma.method == 'real'){
              option[ma.method_args.ret] = theitem.area;
            }
          
            option.price_current = Math.round(option[ma.method_args.ret] * option.pricelist_current * 100) / 100;
            option.price_older = Math.round(option[ma.method_args.ret] * option.pricelist_older * 100) / 100;
            $scope.getOptionPrice(option);
            
            if(option.code == detail.selected.code && theitem.isnew !== false){
              detail.selected = option;
            }
          }
        });
      }
      else if(option.method == 'simplePercentage'){
        option = setOptionPriceCost(option);
      
        $scope.$watch('tmp.item.price',function(newValue, oldValue) {
          option.price_current = Math.round(newValue * option.pricelist_current * 100) / 100;
          option.price_older = Math.round(newValue * option.pricelist_older * 100) / 100;
          
          $scope.getOptionPrice(option);
          if(option.code == detail.selected.code  && theitem.isnew !== false){
            detail.selected = option;
          }
        });
      }
      else if(option.method == 'sumOfFields'){
        option = setOptionPriceCost(option);
        //todo : all method is todo
        $scope.$watch('tmp.item.price',function(newValue, oldValue) {
          option.price_current = Math.round(newValue * option.pricelist_current * 100) / 100;
          option.price_older = Math.round(newValue * option.pricelist_older * 100) / 100;
          $scope.getOptionPrice(option);
          
          if(option.code == detail.selected.code  && theitem.isnew !== false){
            detail.selected = option;
          }
        });
      }
    //end option
    });
    //end detail
  });
  
  $scope.$watch('tmp.item.price_mode', function(newValue, oldValue) {
    $scope.getOptionPrice(theitem);
  });
  
  $scope.$watch('tmp.item.price_manual', function(newValue, oldValue) {
    $scope.getOptionPrice(theitem);
  });
  
  $scope.$watch('tmp.item.details[tmp.indexPopover].selected.price_mode', function(newValue, oldValue) {
    var ipo = $scope.tmp.indexPopover || 0;
    var ics = theitem.details[ipo];
    $scope.getOptionPrice(ics.selected);
  });
  
  $scope.$watch('tmp.item.details[tmp.indexPopover].selected.price_manual', function(newValue, oldValue) {
    var ipo = $scope.tmp.indexPopover || 0;
    var ics = theitem.details[ipo];
    $scope.getOptionPrice(ics.selected);
  });
  
  $scope.$watch('tmp.item.qty', function(newValue, oldValue) {
    $rootScope.$broadcast('changeTotalRow', $scope.item);
  });
  
  $scope.$on('changeTotalRow', function(ev, args){
    var p = numberNotNull(theitem.price);
    angular.forEach(theitem.details, function(d, key) {
      p += numberNotNull(d.selected.price);
    });
    theitem.total_price = Math.round(p * 100) / 100;
    theitem.total_row_price = Math.round(p * numberNotNull(theitem.qty) * 100) / 100;
  });
  
  $scope.getOptionPrice = function (option){
    var retprice = null;
    switch(option.price_mode) {
        case 'older':
            retprice = option.price_older;
            break;
        case "current":
            retprice = option.price_current;
            break;
        case 'manual':
            retprice = option.price_manual;
            break;
        case 'nullValue':
            retprice = option.price_nullValue;
            break;
    }
    option.price = retprice;
    $rootScope.$broadcast('changeTotalRow', $scope.item);
  };
  
  $scope.openPopover = function(idx){
    tmp.indexPopover = idx;
  };
  
  $scope.$watch('tmp.item.area',function(newValue, oldValue) {
    var retprice = -1;
    var a = theitem.area;
    
    if(a == 0.6)
            retprice = 1200;
    else if(a < 0.6) 
            retprice = 1100;
           
    else if(a > 0.6)
            retprice = 1300;

    theitem.price_current = retprice;
    $scope.getOptionPrice(theitem);
  });

  $scope.tmp = tmp;
  
  $scope.ok = function () {
    $uibModalInstance.close($scope.tmp.item);
  };

  $scope.cancel = function () {
    $uibModalInstance.dismiss('cancel');
  };
});

