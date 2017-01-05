angular.module('ui.bootstrap.demo').controller('ModalYesNoCtrl', function ($scope, $uibModalInstance, tmpYesNo) {
  $scope.tmpYesNo = tmpYesNo;
  
  $scope.ok = function () {
   $uibModalInstance.close(tmpYesNo.idx);
  };
  
  $scope.cancel = function () {
    $uibModalInstance.dismiss('cancel');
  };
  
});