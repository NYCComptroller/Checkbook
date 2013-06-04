Sample configuration to instantiate the widget

{
  "widgetType": "highcharts",
  "defaultParameters": { "agencyAbbrev": "DOD","measureId":26},
  "allowedParams": ["goalId","measureId"],
  "useList": "true",
  "dataset": "performance:goal.measure.status.history",
  "columns": ["measure_id", "goal_id", "agency_id", "target_date", "status_id", "actual_value", "target_value", "unit_of_measurement"],
  "orderBy": ["target_date"],
  "datasets" : [
       {"name": "targetDate","column":"target_date", "type":"date", "isArray":true},
       {"name":"targetValue", "column":"target_value", "type":"float", "isArray":true},
       {"name":"actualValue", "column":"actual_value", "type":"float", "isArray":true},
       {"name": "measurement", "column":"unit_of_measurement", "type":"string","isArray":false}
],
  "chartConfig" : {
	   "xAxis": {
		      "categories": {"ds":"targetDate"},
		      "type" : "datetime",
		      "title": {
		         "text": "Quarter"
		      },
		      "labels": {
		      	"rotation": -45,
		      	"align": "right",
		      	"style": {
		           "font": "normal 13px Verdana, sans-serif"
		      	}
		      }
		   },   		   
		"series": [{
		     "name": "Target Values",
		     "type": "line",
		      "data": {"ds":"targetValue"}
		  	}, {
		      "name": "Actual Values",
		      "type": "column",
		      "data": {"ds":"actualValue"}
    		}
	    ],			
	   "title": {
	      "text": "HPPG Graph",
	      "style": {
	         "margin": "10px 100px 0 0"
	      }
	   },
	   "subtitle": {
	      "text": "Source: OMB",
	      "style": {
	         "margin": "0 100px 0 0"
	      }
	   },
	   "yAxis": {
	      "title": {
	         "text": {"ds":"measurement"}
	      },
	      "function":"functionA"
	   } ,	   
	   "legend": {
	      "layout": "vertical",
	      "style": {
	         "left": "auto",
	         "bottom": "auto",
	         "right": "10px",
	         "top": "10px"
	      }
	   }
}

}
<function>
functionA^^template : function{} {....};##
functionB^^formatter : function{} {....};
</function>

