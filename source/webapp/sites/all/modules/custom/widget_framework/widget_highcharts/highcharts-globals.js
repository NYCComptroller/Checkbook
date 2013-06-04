Highcharts.setOptions({
  global:{
    useUTC:false
  },
  chart:{
    margin:[10, 0, 60, 60],
    animation:false,
    height:315,
    width:800
  },
  plotOptions:{
    column:{
      minPointLength:3
    }
  },
  title:{
    style:{
      fontWeight:'bold'
    },
    x:35,
    align:'center'
  },
  colors:['#EB8E27', '#7DB7E5', '#B8D8EF', '#3D96AE',
    '#DB843D', '#92A8CD', '#A47D7C', '#B5CA92'],
  xAxis:{
    tickWidth:1,
    tickmarkPlacement:'on',
    labels:{
      enabled:false
    }
  },
  yAxis:{
    tickPixelInterval:50
  },
  legend:{
    x:40,
    y:0,
    itemStyle:{
      fontSize:'11px'
    }
  },

  exporting:{
    buttons:{
      exportButton:{
        menuItems:[
          {
            text:'Export to JPEG',
            onclick:function () {
              this.exportChart({
                width:250,
                type:'image/jpeg'
              });
            }
          },
          {
            text:'Export to PDF',
            onclick:function () {
              this.exportChart({
                type:'application/pdf'
              }); // 800px by default
            }
          },
          null,
          null
        ]
      }
    }
  }
});

function abbrNum(number, decPlaces, prefix) {
  if (number < 0) {
    var isNegative = true;
  }
  number = Math.abs(number);
  // 2 decimal places => 100, 3 => 1000, etc
  decPlaces = Math.pow(10, decPlaces);

  // Enumerate number abbreviations
  var abbrev = [ "K", "M", "B", "T" ];

  // Go through the array backwards, so we do the largest first
  for (var i = abbrev.length - 1; i >= 0; i--) {

    // Convert array index to "1000", "1000000", etc
    var size = Math.pow(10, (i + 1) * 3);

    // If the number is bigger or equal do the abbreviation
    if (size <= number) {
      // Here, we multiply by decPlaces, round, and then divide by decPlaces.
      // This gives us nice rounding to a particular decimal place.
      number = Math.round(number * decPlaces / size) / decPlaces;

      // Add the letter for the abbreviation
      number += abbrev[i];

      // We are done... stop
      break;
    }
  }
  if (isNegative) {
    return '-' + prefix + number;
  }
  return prefix + number;
}

//Return formatted string for y axis label
function yAxisLabelFormatter(context) {
  return abbrNum(context.value, 2, '$');
}

//An array to keep track of all Highcharts objects on a page to turn on/off series, etc.
Highcharts.chartarray = new Array();