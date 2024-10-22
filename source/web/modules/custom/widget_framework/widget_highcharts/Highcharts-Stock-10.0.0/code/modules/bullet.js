/*
 Highcharts JS v10.0.0 (2022-03-07)

 Bullet graph series type for Highcharts

 (c) 2010-2021 Kacper Madej

 License: www.highcharts.com/license
*/
(function(b){"object"===typeof module&&module.exports?(b["default"]=b,module.exports=b):"function"===typeof define&&define.amd?define("highcharts/modules/bullet",["highcharts"],function(e){b(e);b.Highcharts=e;return b}):b("undefined"!==typeof Highcharts?Highcharts:void 0)})(function(b){function e(b,d,a,r){b.hasOwnProperty(d)||(b[d]=r.apply(null,a),"function"===typeof CustomEvent&&window.dispatchEvent(new CustomEvent("HighchartsModuleLoaded",{detail:{path:d,module:b[d]}})))}b=b?b._modules:{};e(b,"Series/Bullet/BulletPoint.js",
[b["Series/Column/ColumnSeries.js"]],function(b){var d=this&&this.__extends||function(){var b=function(a,c){b=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(b,a){b.__proto__=a}||function(b,a){for(var c in a)a.hasOwnProperty(c)&&(b[c]=a[c])};return b(a,c)};return function(a,c){function d(){this.constructor=a}b(a,c);a.prototype=null===c?Object.create(c):(d.prototype=c.prototype,new d)}}();return function(b){function a(){var a=null!==b&&b.apply(this,arguments)||this;a.options=void 0;
a.series=void 0;return a}d(a,b);a.prototype.destroy=function(){this.targetGraphic&&(this.targetGraphic=this.targetGraphic.destroy());b.prototype.destroy.apply(this,arguments)};return a}(b.prototype.pointClass)});e(b,"Series/Bullet/BulletSeries.js",[b["Series/Bullet/BulletPoint.js"],b["Core/Series/SeriesRegistry.js"],b["Core/Utilities.js"]],function(b,d,a){var e=this&&this.__extends||function(){var b=function(a,f){b=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(b,a){b.__proto__=a}||
function(b,a){for(var f in a)a.hasOwnProperty(f)&&(b[f]=a[f])};return b(a,f)};return function(a,f){function k(){this.constructor=a}b(a,f);a.prototype=null===f?Object.create(f):(k.prototype=f.prototype,new k)}}(),c=d.seriesTypes.column,t=a.extend,l=a.isNumber,u=a.merge,p=a.pick,v=a.relativeLength;a=function(b){function a(){var a=null!==b&&b.apply(this,arguments)||this;a.data=void 0;a.options=void 0;a.points=void 0;a.targetData=void 0;return a}e(a,b);a.prototype.drawPoints=function(){var a=this,k=a.chart,
c=a.options,d=c.animationLimit||250;b.prototype.drawPoints.apply(this,arguments);a.points.forEach(function(b){var f=b.options,e=b.target,q=b.y,g=b.targetGraphic;if(l(e)&&null!==e){var h=u(c.targetOptions,f.targetOptions);var r=h.height;var m=b.shapeArgs;b.dlBox&&m&&!l(m.width)&&(m=b.dlBox);var n=v(h.width,m.width);var t=a.yAxis.translate(e,!1,!0,!1,!0)-h.height/2-.5;n=a.crispCol.apply({chart:k,borderWidth:h.borderWidth,options:{crisp:c.crisp}},[m.x+m.width/2-n/2,t,n,r]);g?(g[k.pointCount<d?"animate":
"attr"](n),l(q)&&null!==q?g.element.point=b:g.element.point=void 0):b.targetGraphic=g=k.renderer.rect().attr(n).add(a.group);k.styledMode||g.attr({fill:p(h.color,f.color,a.zones.length&&(b.getZone.call({series:a,x:b.x,y:e,options:{}}).color||a.color)||void 0,b.color,a.color),stroke:p(h.borderColor,b.borderColor,a.options.borderColor),"stroke-width":h.borderWidth,r:h.borderRadius});l(q)&&null!==q&&(g.element.point=b);g.addClass(b.getClassName()+" highcharts-bullet-target",!0)}else g&&(b.targetGraphic=
g.destroy())})};a.prototype.getExtremes=function(a){a=b.prototype.getExtremes.call(this,a);var c=this.targetData;c&&c.length&&(c=b.prototype.getExtremes.call(this,c),l(c.dataMin)&&(a.dataMin=Math.min(p(a.dataMin,Infinity),c.dataMin)),l(c.dataMax)&&(a.dataMax=Math.max(p(a.dataMax,-Infinity),c.dataMax)));return a};a.defaultOptions=u(c.defaultOptions,{targetOptions:{width:"140%",height:3,borderWidth:0,borderRadius:0},tooltip:{pointFormat:'<span style="color:{series.color}">\u25cf</span> {series.name}: <b>{point.y}</b>. Target: <b>{point.target}</b><br/>'}});
return a}(c);t(a.prototype,{parallelArrays:["x","y","target"],pointArrayMap:["y","target"]});a.prototype.pointClass=b;d.registerSeriesType("bullet",a);"";return a});e(b,"masters/modules/bullet.src.js",[],function(){})});
//# sourceMappingURL=bullet.js.map