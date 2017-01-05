angular.module('ui.bootstrap.demo').controller('ModalSelectItemCtrl', function ($rootScope, $scope, $uibModalInstance, itemTmp) {
  $scope.itemTmp = itemTmp;
  
  $scope.selectNode = function(i){
    $rootScope.$broadcast('selectNodeEvent', i);
    $uibModalInstance.dismiss('cancel');
  };
  
  $scope.cancel = function () {
    $uibModalInstance.dismiss('cancel');
  };
  
});