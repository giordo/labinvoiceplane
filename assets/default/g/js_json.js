

var area4CalcPrices = {
      method: 'area4CalcPrices',
      method_args: {
        method: 'maxOf', //fixed, //minOf
        method_args: {values: ['tmp.item.area', 'min'], ret: 'qty'}, 
        multiple: 1,
        min: 0.5
      },
      override: 'method_args_default'
    };
    var config = {//pricelist  
      pricelist_code_current: '1a',
      pricelist_code_last: '1a', //todo sarebbe meglio in via generale
    }
    
    var method_args_vetro = {
        method: 'maxOf', //fixed, //min
        method_args: {values: ['tmp.item.area', 'min'], ret: 'qty'}, 
        multiple: 1,
        min: 0.5
      };
    
    var tipoVetro = [{
      name: "vetro 33.1/20g/33.1be",
      code: "33.1/20g/33.1be",
      pricelist_option_code: '33.1/20g/33.1be',
      method: 'area4CalcPrices',
      method_args: method_args_vetro,
      method_args_default: {min: 0.7},
      //pricelist_option_price: null,
      //pricelist_option_cost: null,
      //qty: null, //todo sarà la chiamata alla funzione area4CalcPrices('max', method_args);
      //_qty: area4CalcPrices,
    //price
      //price: null, //null
      //price_pricelist: null, // todo to calculate
      //price_custom: null,
    //cost
      //cost_pricelist: null, // todo to calculate
    }, {
      name: "vetro 44.1/16g/44.1be",
      code: "44.1/16g/44.1be",
      pricelist_option_code: '44.1/16g/44.1be',
      method: 'area4CalcPrices',
      method_args: method_args_vetro,
      method_args_default: {min: 0.7},
      
    }, {
      name: "vetro 55.1/14g/55.1be",
      code: "55.1/14g/55.1be",
      pricelist_option_code: '55.1/14g/55.1be',
      method: 'area4CalcPrices',
      method_args: method_args_vetro,
      method_args_default: {min: 0.7},
      
    }];

    var method_args_profilo = {
        multiple: 1,
        percentage: 0.05
      };

    var tipoProfilo = [{
      name: "profilo a L",
      code: "profilo_l",
      pricelist_option_code: 'profilo_l',
      method: 'simplePercentage',
      method_args: {
        multiple: 1,
        percentage: 0
      },
      method_args_default: null,
      
    }, {
      name: "profilo Z 45",
      code: "profilo_z45",
      pricelist_option_code: 'profilo_z45',
      method: 'simplePercentage',
      method_args: {
        multiple: 1,
        percentage: 0.05
      },
      method_args_default: null,
      
    }, {
      name: "profilo Z 58",
      code: "profilo_z58",
      pricelist_option_code: 'profilo_z58',
      method: 'simplePercentage',
      method_args: {
        multiple: 1,
        percentage: 0.07
      },
      method_args_default: null,
      
    }];

    var sss = [{
      name: "Profilo",
      code: "PRFL",
      options: tipoProfilo,
      selected: {
        code: 'profilo_l'
      }
    }, {
      name: "Vetro",
      code: "VTR",
      options: tipoVetro,
      selected: {
        code: '44.1/16g/44.1be'
      }
    }];

    function opt(list, item) {
      return list[1];
    }

    var sUno = [{
      code: "PRFL",
      value: {
        code: 'profilo_l'
      }
    }, {
      code: "VTR",
      value: {
        code: '44.1/16g/44.1be'
      }
    }];

    var sDue = [{
      code: "PRFL",
      value: {
        code: 'profilo_z45'
      }
    }, {
      code: "VTR",
      value: {
        code: '44.1/16g/44.1be'
      }
    }];

    var sTre = [{
      code: "PRFL",
      value: {
        code: 'profilo_z58'
      }
    }];

    //http://mathjs.org/docs/expressions/parsing.html

    var needs = [
    {
      "name": "qty",
      "code": "qty",
      "value": 1,
      "type": "number",
      "mode": "input"
    }, {
      name: 'L',
      code: 'L',
      value: 600,
      type: 'number',
      mode: 'input'
    }, {
      name: 'H',
      code: 'H',
      value: 1000,
      type: 'number',
      mode: 'input'
    }, {
      name: 'area',
      code: 'area',
      mode: 'calc',
      type: 'number',
      method: 'calc',
      watch: 'tmp.item.L * tmp.item.H',
      method_args: "tmp.item.L * tmp.item.H / 1000000"
    }, {
      name: 'perimeter 4L',
      code: 'perimeter4',
      mode: 'calc',
      type: 'number',
      method: 'calc',
      watch: 'tmp.item.L * tmp.item.H',
      method_args: "tmp.item.L + tmp.item.L + tmp.item.H + tmp.item.H"
    }, {
      name: 'perimeter 3L',
      code: 'perimeter3',
      mode: 'calc',
      type: 'number',
      method: 'calc',
      watch: 'tmp.item.L * tmp.item.H',
      method_args: "tmp.item.L + tmp.item.H + tmp.item.H"
    }, {
      name: 'Build grid2D code',
      code: 'gridCode2D100',
      mode: 'gluecode',
      type: 'text',
      method: "str_lpad",
      watch: "tmp.item.L * tmp.item.H",
      method_args: {
        lngth: 8,
        str: '0',
        elements: [{
          name: 'L100',
          target: 'tmp.item.L',
          round: 100
        }, {
          name: 'H100',
          target: 'tmp.item.H',
          round: 100
        }]
      }
    }];


    var ppp = [{
      label: "Finestra SHU 1 anta",
      children: [],
      code: "SHU_FN1",
      needs: needs,
      needs_default: [{
        name: 'L',
        value: 700
      }, {
        name: 'H',
        value: 1600
      }],
      details: sss,
      details_default: sUno
    }, {
      label: "Finestra SHU 2 ante",
      children: [],
      code: "SHU_FN2",
      needs: needs,
      needs_default: [{
        name: 'L',
        value: '1400'
      }, {
        name: 'H',
        value: '1700'
      }],
      details: sss,
      details_default: sDue
    }];

var prodottoProva = {
      label: "Finestra KOM 1 anta",
      children: [],
      code: "KOM_FN1",
      needs: needs,
      needs_default: [{
        name: 'L',
        value: '600'
      }, {
        name: 'H',
        value: '1500'
      }],
      details: sss,
      details_default: sUno
    };

    var qqq = [{
      label: "Finestra KOM 1 anta",
      children: [],
      code: "KOM_FN1",
      needs: needs,
      needs_default: [{
        name: 'L',
        value: '600'
      }, {
        name: 'H',
        value: '1500'
      }],
      details: sss,
      details_default: sUno
    }, {
      label: "Finestra KOM 2 ante",
      children: [],
      code: "KOM_FN2",
      needs: needs,
      needs_default: [{
        name: 'L',
        value: '1200'
      }, {
        name: 'H',
        value: '1800'
      }],
      details: sss,
      details_default: sDue
    }, {
      label: "Finestra KOM 3 ante",
      children: [],
      code: "KOM_FN3",
      needs: needs,
      needs_default: [{
        name: 'L',
        value: '1800'
      }, {
        name: 'H',
        value: '1900'
      }],
      details: sss,
      details_default: sTre
    }];

    var bbb = [{
      label: 'PVC SHUCO',
      children: ppp
    }, {
      label: 'PVC KOMMERING',
      children: qqq
    }];
    var aaa = [{
      label: 'SERRAMENTI PVC',
      children: bbb
    }, {
      label: 'SERRAMENTI PROVA',
      children: [{
        label: 'PVC SHUCO',
        children: ppp
      }]
    }];
    //var d = [{label: 'CATALOGO', children: aaa}];
    var d = ppp;
