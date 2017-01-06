/*
 *** METODI

 *** NEEDS (var needs) FANNO RIFERIMENTO AL
 *name
 *code
 *value
 *type -- number | text
 *mode -- input | auto
 method -- null | calc | gluecode
 watch (string)
 method_args (string - object)




 input
 calc
 method:
 area4CalcPrices
 method_args:
 maxOf
 minOf
 fixed
 real
 simplePercentage


 gluecode
 method_args:
 str_lpad


 method (sono quelli implementati nel codice) maxOf | minOf | fixed | real - calc
 watch
 method_args


 */

    var config = {//pricelist  
      pricelist_code_current: '1a',
      pricelist_code_last: '1a', //todo sarebbe meglio in via generale
    }
    
    var method_args_vetro = {
        func: 'maxOf', //fixed, //min
        func_args: {values: ['tmp.item.area', 'min'], ret: 'qty'},
        f_args: {
            fields: ['tmp.item.area'],
            values: ['min'],
            ret: 'qty'
        },
        multiple: 0.01,
        min: 0.5,
        max: null
      };
    
    var tipoVetro = [{
      name: "vetro 33.1/20g/33.1be",
      code: "33.1/20g/33.1be",
      po_code: '33.1/20g/33.1be',
      po_method: 'compare',
      po_method_args: method_args_vetro,
      po_method_args_default: {min: 0.7},
    }, {
      name: "vetro 44.1/16g/44.1be",
      code: "44.1/16g/44.1be",
      po_code: '44.1/16g/44.1be',
      po_method: 'compare',
      po_method_args: method_args_vetro,
      po_method_args_default: {min: 0.7},
      
    }, {
      name: "vetro 55.1/14g/55.1be",
      code: "55.1/14g/55.1be",
      po_code: '55.1/14g/55.1be',
      po_method: 'compare',
      po_method_args: method_args_vetro,
      po_method_args_default: {min: 0.7},
    }];

    var method_args_profilo = {
        multiple: 1,
        percentage: 0.05
      };

    var tipoProfilo = [{
      name: "profilo a L",
      code: "profilo_l",
      po_code: 'profilo_l',
      po_method: 'math',
      po_method_args: {
          func: 'percentage',
          f_args: {
              fields: ['tmp.item.price']
          },
          //percentage: 0,
          multiple: 1,
          min: null,
          max: null,
      },
      po_method_args_default: null,
      
    }, {
      name: "profilo Z 45",
      code: "profilo_z45",
      po_code: 'profilo_z45',
      po_method: 'math',
      po_method_args: {
        func: 'percentage',
        f_args: {
            fields: ['tmp.item.price']
        },
        //percentage: 0.2,
        multiple: 1,
        min: null,
        max: null,
      },
      po_method_args_default: null,
      
    }, {
      name: "profilo Z 58",
      code: "profilo_z58",
      po_code: 'profilo_z58',
        po_method: 'math',
        po_method_args: {
            func: 'percentage',
            f_args: {
                fields: ['tmp.item.price']
            },
            //percentage: 0.8,
            multiple: 1,
            min: null,
            max: null,
        },
        po_method_args_default: null,
    }];

    //detail
    var sss = [{
      name: "Profilo",
      code: "PRFL",
      mode: 'mode-select',
      method: null,
      options: tipoProfilo,
      selected: {
        code: 'profilo_l'
      }
    }, {
      name: "Vetro",
      code: "VTR",
      mode: 'mode-select',
      method: null,
      options: tipoVetro,
      selected: {
        code: '44.1/16g/44.1be'
      }
    }, {
        name: "i1",
        code: "i1",
        mode: 'mode-input',
        method: null,
        value: 'valore default',
        selected: {
            price_mode: 'manual',
            price_manual: 11.00
        }
    }
];
/*
 , {
 name: "vrn",
 code: "vrn",
 mode: 'auto',
 method: 'elaborateFields',
 method_args: {
 func: 'sum',
 func_args: {
 fields: ['tmp.item.price', 'PRFL']
 }
 }
 }
 */

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
      value: null,
      type: 'number',
      mode: 'auto',// 'calc',
      method: 'calc',
      watch: 'tmp.item.L * tmp.item.H',
      method_args: "tmp.item.L * tmp.item.H / 1000000"
    }, {
      name: 'perimeter 4L',
      code: 'perimeter4',
      value: null,
      type: 'number',
      mode: 'auto', //'calc',
      method: 'calc',
      watch: 'tmp.item.area',
      method_args: "(tmp.item.L + tmp.item.L + tmp.item.H + tmp.item.H) / 1000"
    }, {
      name: 'perimeter 3L',
      code: 'perimeter3',
      type: 'number',
      mode: 'auto',
      method: 'calc',
      watch: 'tmp.item.area',
      method_args: "(tmp.item.L + tmp.item.H + tmp.item.H) / 1000"
    }, {
      name: 'Build grid2D code',
      code: 'gridCode2D100',
      mode: 'auto',
      type: 'text',
      method: "gluecode",
      watch: "tmp.item.area",
      method_args: {
        func: 'str_lpad',
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
