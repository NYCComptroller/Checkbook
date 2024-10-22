/*
 Highstock JS v10.0.0 (2022-03-07)

 Indicator series type for Highcharts Stock

 (c) 2010-2021 Sebastian Bochan

 License: www.highcharts.com/license
*/
(function(a){"object"===typeof module&&module.exports?(a["default"]=a,module.exports=a):"function"===typeof define&&define.amd?define("highcharts/indicators/momentum",["highcharts","highcharts/modules/stock"],function(d){a(d);a.Highcharts=d;return a}):a("undefined"!==typeof Highcharts?Highcharts:void 0)})(function(a){function d(a,b,d,g){a.hasOwnProperty(b)||(a[b]=g.apply(null,d),"function"===typeof CustomEvent&&window.dispatchEvent(new CustomEvent("HighchartsModuleLoaded",{detail:{path:b,module:a[b]}})))}
a=a?a._modules:{};d(a,"Stock/Indicators/Momentum/MomentumIndicator.js",[a["Core/Series/SeriesRegistry.js"],a["Core/Utilities.js"]],function(a,b){var d=this&&this.__extends||function(){var a=function(b,c){a=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(a,c){a.__proto__=c}||function(a,c){for(var b in c)c.hasOwnProperty(b)&&(a[b]=c[b])};return a(b,c)};return function(b,c){function m(){this.constructor=b}a(b,c);b.prototype=null===c?Object.create(c):(m.prototype=c.prototype,new m)}}(),
g=a.seriesTypes.sma,n=b.extend,p=b.isArray,q=b.merge;b=function(a){function b(){var b=null!==a&&a.apply(this,arguments)||this;b.data=void 0;b.options=void 0;b.points=void 0;return b}d(b,a);b.prototype.getValues=function(a,b){var c=b.period;b=b.index;var d=a.xData,g=(a=a.yData)?a.length:0,h=[],k=[],l=[],e;if(!(d.length<=c)&&p(a[0])){for(e=c+1;e<g;e++){var f=[d[e-1],a[e-1][b]-a[e-c-1][b]];h.push(f);k.push(f[0]);l.push(f[1])}f=[d[e-1],a[e-1][b]-a[e-c-1][b]];h.push(f);k.push(f[0]);l.push(f[1]);return{values:h,
xData:k,yData:l}}};b.defaultOptions=q(g.defaultOptions,{params:{index:3}});return b}(g);n(b.prototype,{nameBase:"Momentum"});a.registerSeriesType("momentum",b);"";return b});d(a,"masters/indicators/momentum.src.js",[],function(){})});
//# sourceMappingURL=momentum.js.map