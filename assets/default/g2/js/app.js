angular.module('ui.bootstrap.demo', ['ngAnimate', 'ngSanitize', 'ui.bootstrap', 'ui.sortable', 'treeControl']);
angular.module('ui.bootstrap.demo')
  .filter('sumByObjKey', function($parse) {
    return function(data, key) {
      if (typeof(data) === 'undefined' || typeof(key) === 'undefined') {
        return 0;
      }
      var str = key.split('.');
      if (typeof(data) === 'undefined' || typeof(key) === 'undefined') {
        return 0;
      }
      var t = data;
      var sum = 0;

      for (var i = t.length - 1; i >= 0; i--) {
        sum += t[i][key];
      }
      return sum;
    };
  })
  .directive('uibTabButton', function() {
      return {
        restrict: 'EA',
        scope: {
          handler: '&',
          text:'@'
        },
        template: '<li class="uib-tab nav-item" >' +
          '<a href="javascript:;" ng-click="handler()" class="nav-link" ng-bind="text"></a>' +
          '</li>',
        replace: true
      }
    })
    .factory('safeApply', [function($rootScope) {
      return function($scope, fn) {
        var phase = $scope.$root.$$phase;
        if(phase == '$apply' || phase == '$digest') {
          if (fn) {
            $scope.$eval(fn);
          }
        } else {
          if (fn) {
            $scope.$apply(fn);
          } else {
            $scope.$apply();
          }
        }
      }
    }])
  .controller('AppCtrl', function( $scope, $uibModal, $log, $timeout, $templateCache, safeApply) {
    //var $ctrl = this;
    // droppable http://ngmodules.org/modules/ng-sortable
    // http://marceljuenemann.github.io/angular-drag-and-drop-lists/demo/#/advanced

    var rm = 0;

      if(rm === 1)
      $templateCache.removeAll();

    $templateCache.put("uib/template/tabs/tabset.html",
    "<div>\n" +
    "  <ul ui-sortable=\"{}\" class=\"nav nav-{{tabset.type || 'tabs'}}\" ng-class=\"{'nav-stacked': vertical, 'nav-justified': justified}\" ng-transclude></ul>\n" +
    "  <div class=\"tab-content\">\n" +
    "    <div class=\"tab-pane\"\n" +
    "         ng-repeat=\"tab in tabset.tabs\"\n" +
    "         ng-class=\"{active: tabset.active === tab.index}\"\n" +
    "         uib-tab-content-transclude=\"tab\">\n" +
    "    </div>\n" +
    "  </div>\n" +
    "</div>\n" +
    "");

$scope.getCurrentTab = function(e){
  if(e != null)
    e.preventDefault();
  
};

      $scope.getTemplate = function(mode){
          console.log("mode"+mode);
          if (mode)
              return "mode" + mode ;
          else
              return 'modeauto';
      };

    $scope.tmp = {};
    $scope.tmp.tabCounter = 1;
    $scope.tmp.tabSelected = 0;
    $scope.tmp.tabSelectedIdx = 0;
    
    $scope.tmp.workItem = {};
    $scope.tmp.item = {};
    
    //http://wix.github.io/angular-tree-control/
    $scope.itemTmp = {};
    $scope.itemTmp.items = bbb;
    $scope.itemTmp.treeOpts =  {
      nodeChildren: "children",
      dirSelectable: false
      /*,
      injectClasses: {
          ul: "a1",
          li: "a2",
          liSelected: "a7",
          iExpanded: "a3",
          iCollapsed: "a4",
          iLeaf: "a5",
          label: "a6",
          labelSelected: "a8"
      }
      */
    }
    
    $scope.tmpYesNo = {
      title: 'Eliminazione elemento',
      description: 'Sei sicuro di volere eliminare questo elemento?',
      idx: 1
    };
    
    $scope.$on('selectNodeEvent', function(ev, node){
      $scope.openModalDetail('lg', node);
    });
    
    $scope.tabs = [{
      title: 'Serramenti in pvc',
      content: 'Dynamic content 1',
      tabCounter: 0,
      items: []
    }];
    
    $scope.addTab = function() {
      var newTab = {
        title: 'Tab ' + ($scope.tmp.tabCounter),
        content: 'content ' + ($scope.tmp.tabCounter),
        items: []
      };
      $timeout(function() {
        $scope.tabs.push(newTab);
        //$scope.$apply();
      });
      $timeout(function() {
        $scope.tmp.tabSelected = $scope.tabs.length - 1;
        $scope.tmp.tabCounter = $scope.tmp.tabCounter + 1;
      });
      safeApply($scope);
    };
    
    $scope.tabRemoveConfirm = function(i, e){
      $scope.tmpYesNo.idx = i;
      if($scope.tabs.length > 1){
        $scope.openModalYesNo('lg', false);
      }
    }
    
    $scope.tabRemove = function(i, e){
      $timeout(function() {
        $scope.tabs.splice($scope.tmpYesNo.idx, 1);
        r = (i > 0)? i - 1: 0;
        $scope.tmp.tabSelected = r;
        
      });
      safeApply($scope);
    }
    
    $scope.saveItem = function(itm) {
      var i = angular.copy(itm);
      if (i.isnew === false) {
        $scope.tabs[$scope.tmp.tabSelected].items[i.idx] = i;
      } else {
        i.isnew = false;
        $scope.tabs[$scope.tmp.tabSelected].items.push(i);
      }
    };

    $scope.rejectUpdate = function(itm) {
      if ($scope.tmp.workItem.isnew === false) {
        $scope.tabs[$scope.tmp.tabSelected].items[$scope.tmp.workItem.idx] = $scope.tmp.workItem;
      }
    };

    $scope.openModalYesNo = function(size, x) {
      var modalYesNoInstance = $uibModal.open({
        animation: true,
        ariaLabelledBy: 'modal-title',
        ariaDescribedBy: 'modal-body',
        templateUrl: '../../assets/default/g2/tpl/tpl_modal-yesno.html',
        controller: 'ModalYesNoCtrl',
        backdrop: true,
        size: size,
        resolve: {
          tmpYesNo: function() {
            return $scope.tmpYesNo;
          }
        }
      });

      modalYesNoInstance.result.then(function(i) {
        $scope.tabRemove(i);
      }, function() {
        //console.log('cancel');
      });
    };

    $scope.openModalItem = function(size, x) {
      var modalItemInstance = $uibModal.open({
        animation: true,
        ariaLabelledBy: 'modal-title',
        ariaDescribedBy: 'modal-body',
        templateUrl: '../../assets/default/g2/tpl/tpl_modal-select-item.html',
        controller: 'ModalSelectItemCtrl',
        backdrop: true,
        size: size,
        resolve: {
          itemTmp: function() {
            return $scope.itemTmp;
          }
        }
      });

      modalItemInstance.result.then(function(selectedItem) {
        //$scope.saveItem(selectedItem);
        }, function() {
        //$scope.rejectUpdate();
      });
    };



    $scope.openModalDetail = function(size, x, c) {
      if (!isNumeric(x)) {
        angular.copy(x, $scope.tmp.item);
        $scope.tmp.item.idx = $scope.tabs[$scope.tmp.tabSelected].items.length;
        ///$scope.tmp.item.idx = $scope.tabs[$scope.tmp.tabSelectedIdx].items.length;
      } 
      else {
        angular.copy($scope.tabs[$scope.tmp.tabSelected].items[x], $scope.tmp.item);
        //$scope.tmp.item = $scope.tabs[$scope.tmp.tabSelected].items[x];
        ///$scope.tmp.item = $scope.tabs[$scope.tmp.tabSelectedIdx].items[x];

        $scope.tmp.item.idx = x;
        var isequal = angular.equals($scope.tmp.item, $scope.tmp.workItem);
        if (!isequal) {
          angular.copy($scope.tmp.item, $scope.tmp.workItem);
        }
      }

        console.log($scope.tmp.item);

        function ts(){
            var d = new Date();
            return d.getTime();
        };


      var modalDetailInstance = $uibModal.open({
        animation: true,
        ariaLabelledBy: 'modal-title',
        ariaDescribedBy: 'modal-body',
        templateUrl: '../../assets/default/g2/tpl/tpl_modal-select-detail.html?'+timestamp(),
        controller: 'ModalSelectDetailCtrl',
        backdrop: true,
        size: size,
        resolve: {
          tmp: function() {
            return $scope.tmp;
          }
        }
      });

      modalDetailInstance.result.then(function(selectedItem) {
        $scope.saveItem(selectedItem);
      }, function() {
        $scope.rejectUpdate();
      });
    };
  })

  
  $( document ).ready(function() {
   /// $('div').css('color', 'red');
  });
  
  