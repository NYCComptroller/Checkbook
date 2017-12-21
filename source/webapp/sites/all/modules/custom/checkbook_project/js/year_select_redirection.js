jQuery(document).ready(function(){
    jQuery('#year_list,#fiscal_year_list').change(function(){
    	var link = jQuery('#year_list :selected').attr("link");
    	window.location = link;
		/*var selected_values = jQuery(this).val().split('~');
        var year_value = selected_values[0];
        var year_type = selected_values[1];
        var current_url_array;

        var parameters = new Array();
        if(year_type == 'B'){
            current_url_array = adjust_current_url_array('calyear'); //removing "calyear" from URL if Fiscal year is selected
            parameters[0] = {"param":"year","value":year_value};
        }else if(year_type == 'C'){
            current_url_array = adjust_current_url_array('year'); //removing "year" from URL if Fiscal year is selected
            parameters[0] = {"param":"calyear","value":year_value};
        }
        parameters[1] = {"param":"yeartype","value":year_type};
        redirect_on_selection(parameters, current_url_array);*/
    });
})


function redirect_on_selection(parameters, current_url_array){
    var parameters_size = parameters.length;
    var new_url_array = [];
    for(var i = 0; i < parameters_size; i++){
        new_url_array = year_list_generate_url(parameters[i].param, parameters[i].value, current_url_array)
    }

    var new_url = new_url_array.join('/');
    window.location = new_url;
}



function year_list_generate_url(parameter, value, current_url_array){
    if(jQuery.inArray(parameter,current_url_array) === -1){
        current_url_array.push(parameter);
        current_url_array.push(value);
    }else{
        if (!Array.prototype.indexOf) {
            Array.prototype.indexOf = function(obj) {
                for (var i = 0, j = this.length; i < j; i++) {
                    if (this[i] === obj) { return i; }
                }
                return -1;
            }
        }
        var index_actual_value = current_url_array.indexOf(parameter);
        current_url_array[index_actual_value+1] = value;
    }
    return current_url_array;
}


/*
This function is used to clear unwanted parameters from the current URL and replace them with new ones as necessary
Eg: if from the year drop down, if "Fiscal Year" is selected then the URL would be <URL>/year/22/yeartype/B
and if the user selects "Calendar Year", then the URL would be <URL>/calyear/22/yeartype/C
 */
function adjust_current_url_array(param_to_replace){
    var current_url = String(window.location);
    var current_url_array = current_url.split('/');
    if (!Array.prototype.indexOf) {
        Array.prototype.indexOf = function(obj) {
            for (var i = 0, j = this.length; i < j; i++) {
                if (this[i] === obj) { return i; }
            }
            return -1;
        }
    }
    var index_actual_value = current_url_array.indexOf(param_to_replace);

    if(index_actual_value != -1){
        return removeByIndex(current_url_array, index_actual_value);
    }else{
        return current_url_array;
    }


}

function removeByIndex(arr, index) {
    arr.splice(index, 2);
    return arr;
}
