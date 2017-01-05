function getMaxOfArray(numArray) {
      return Math.max.apply(null, numArray);
    }
    
    function padLeft(nr, n, str) {
      return Array(n - String(nr).length + 1).join(str || '0') + nr;
    }

    function round(number, increment, offset) {
      return Math.ceil((number - offset) / increment ) * increment + offset;
    }
    
    function numberNotNull(n){
      if(n === null)
        n = 0
        
      return n;
    }
    
    function isNumeric(n) {
      return !isNaN(parseFloat(n)) && isFinite(n);
    }
    
    function setOptionPriceCost(option){
      var listino = [
        {code: '33.1/20g/33.1be', price: 4, cost: 2},
        {code: '44.1/16g/44.1be', price: 6, cost: 3},
        {code: '55.1/14g/55.1be', price: 8, cost: 4},
        {code: 'profilo_l', price: 0.0, cost: 0},
        {code: 'profilo_z45', price: 0.6, cost: 0.6},
        {code: 'profilo_z58', price: 0.14, cost: 0.14}
        ];
        
      angular.forEach(listino, function(riga, rigaKey) {
        if(riga.code == option.code){
          option.pricelist_current = riga.price;
          option.pricelist_older = riga.price*0.9;
          option.pricelist_cost = riga.cost;
        }
      });
      return option;
    }
    
    var hasOwnProperty = Object.prototype.hasOwnProperty;

function isEmpty(obj) {

    // null and undefined are "empty"
    if (obj === null) return true;

    // Assume if it has a length property with a non-zero value
    // that that property is correct.
    if (obj.length > 0)    return false;
    if (obj.length === 0)  return true;

    // If it isn't an object at this point
    // it is empty, but it can't be anything *but* empty
    // Is it empty?  Depends on your application.
    if (typeof obj !== "object") return true;

    // Otherwise, does it have any properties of its own?
    // Note that this doesn't handle
    // toString and valueOf enumeration bugs in IE < 9
    for (var key in obj) {
        if (hasOwnProperty.call(obj, key)) return false;
    }

    return true;
}

function getTabIndex(tabs, tabc){
  var i = 0;
   angular.forEach(tabs, function(t, tKey) {
    if(t.tabCounter == tabc){
      return i;
    }
    i++;
  });
  return i;
}
