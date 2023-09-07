/*
 Highstock JS v10.0.0 (2022-03-07)

 Indicator series type for Highcharts Stock

 (c) 2010-2021 Wojciech Chmiel

 License: www.highcharts.com/license
*/
(function(a){"object"===typeof module&&module.exports?(a["default"]=a,module.exports=a):"function"===typeof define&&define.amd?define("highcharts/indicators/ppo",["highcharts","highcharts/modules/stock"],function(c){a(c);a.Highcharts=c;return a}):a("undefined"!==typeof Highcharts?Highcharts:void 0)})(function(a){function c(a,b,c,f){a.hasOwnProperty(b)||(a[b]=f.apply(null,c),"function"===typeof CustomEvent&&window.dispatchEvent(new CustomEvent("HighchartsModuleLoaded",{detail:{path:b,module:a[b]}})))}
a=a?a._modules:{};c(a,"Stock/Indicators/PPO/PPOIndicator.js",[a["Core/Series/SeriesRegistry.js"],a["Core/Utilities.js"]],function(a,b){var c=this&&this.__extends||function(){var a=function(b,d){a=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(a,d){a.__proto__=d}||function(a,d){for(var b in d)d.hasOwnProperty(b)&&(a[b]=d[b])};return a(b,d)};return function(b,d){function c(){this.constructor=b}a(b,d);b.prototype=null===d?Object.create(d):(c.prototype=d.prototype,new c)}}(),f=a.seriesTypes.ema,
l=b.correctFloat,m=b.extend,n=b.merge,p=b.error;b=function(a){function b(){var b=null!==a&&a.apply(this,arguments)||this;b.data=void 0;b.options=void 0;b.points=void 0;return b}c(b,a);b.prototype.getValues=function(a,b){var c=b.periods,d=b.index;b=[];var g=[],h=[],e;if(2!==c.length||c[1]<=c[0])p('Error: "PPO requires two periods. Notice, first period should be lower than the second one."');else{var k=f.prototype.getValues.call(this,a,{index:d,period:c[0]});a=f.prototype.getValues.call(this,a,{index:d,
period:c[1]});if(k&&a){c=c[1]-c[0];for(e=0;e<a.yData.length;e++)d=l((k.yData[e+c]-a.yData[e])/a.yData[e]*100),b.push([a.xData[e],d]),g.push(a.xData[e]),h.push(d);return{values:b,xData:g,yData:h}}}};b.defaultOptions=n(f.defaultOptions,{params:{period:void 0,periods:[12,26]}});return b}(f);m(b.prototype,{nameBase:"PPO",nameComponents:["periods"]});a.registerSeriesType("ppo",b);"";return b});c(a,"masters/indicators/ppo.src.js",[],function(){})});
//# sourceMappingURL=ppo.js.map