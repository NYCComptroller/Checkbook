
(function ($) {
	if (typeof Drupal != "undefined") {
	    Drupal.behaviors.contractDetailClickOnLoad = {
	    	      attach:function (context, settings) {
	    	    	  $(".clickOnLoad").click();
	    	      }
	    };
	    
		 Drupal.behaviors.contractListPager = {
			      attach:function (context, settings) {
			   	  
			    if($('#contListContainer').length > 0){    	  
			    	$('#contListContainer')
			    	.after('<div class="cont-list-pager"></div>')
			    	.cycle({
		              fx: 'none',
		              speed:  1000,
		              timeout: 0,
		              pause: true,
		              pauseOnPagerHover: 0,
		              pager: '.cont-list-pager',
		              prev:   '#prev',
		              next:   '#next',
		              startingSlide: 0
		            });
		         }
			 }
       };  
	    
	}
	    
		$("a.showHide").live("click", function(event){
                    $(this).toggleClass('open');
                    jQuery(this).parent().parent().parent().next().toggle();
                    if(jQuery(this).parent().parent().siblings().find('a').length){
                        if(jQuery(this).hasClass("open")){
                           jQuery(this).parent().parent().siblings().find('a').text('View All>>');
                        }else{
                           jQuery(this).parent().parent().siblings().find('a').text('Hide All<<');
                        }
                    }
		});
                
                jQuery('a.subContractViewAll').live("click", function(event){
                    jQuery(this).parent().parent().siblings().find('a').click();
                });
                       
	    $('#master_assoc_cta_expand').live( 'click', function () {
	        var nTr = this.parentNode.parentNode;
	        if ( jQuery(this).attr('class').match('loader') ){
	           //ignore
	           return;
	        }

	        if ( jQuery(this).attr('class').match('expanded') ){
	            jQuery(this).removeClass('expanded').addClass('collapsed');
	            jQuery(this).parent().next().html("");
	            //oTable.fnClose( nTr );
	        }else{
	            jQuery(this).removeClass('collapsed').addClass('loader');
	            var magid = jQuery(this).attr('agid');
	            fnAddChildAgreementDetails( this);
	        }
	    } );

		$( document ).ready(function() {

			//create tool tops for contract notes
			var contractNotesToolTip = function(){
				// add processed class to all anchors after ajax
				$(".contractLinkNote a").each(function(){
					if (!$(this).hasClass('processed')) { 
					    $('<div class="toolTip">' + $(this).attr('alt') + '<div class="bottom"></div></div>').insertBefore(this);
						$(this).addClass('processed');
					}
				});
				$('.contractLinkNote').hover(
					function(){
					$(this).find('.toolTip').css('display', 'block')},
					function(){
					$(this).find('.toolTip').css('display', 'none');
					}
				);
			}

            $('.activeExpenseContract').hover(
                function(){
                    $('.activeExpenseContractNote').css('display', 'block')},
                function(){
                    $('.activeExpenseContractNote').css('display', 'none');
                }
            );

            //check if ajax is being fired on the page or not
            if ($.active > 0) {
                $( document ).ajaxComplete(function() {
                    contractNotesToolTip();
                });
            }
            else {
                contractNotesToolTip();
            }
        });

}(jQuery));

	function fnAddChildAgreementDetails (expander)
	{
	    var agurl = jQuery(expander).attr('agurl');
	    //alert(agurl);
	   //fallback for error
	    //agurl = (agurl == null || agurl.length == 0) ?  '0' : magid; 
	    jQuery.ajax({
	        url: agurl,
	        dataType: 'html',
	        type: 'GET',
	        success: function(results) {
	        	var $container = jQuery(expander).parent().next();
                jQuery($container).html(results);
	            if(eval(results['aaData']) && results['aaData'].length > 0){
	                
	            }
	            jQuery(expander).removeClass('loader').addClass('expanded');
                contractsAddPadding($container);
	        }
	    });
	
	    return '';
	}

function contractsAddPadding(container) {
    var $outerTables = container.find('table.outerTable');
    $outerTables.each(function(i,val){
        var $outerNumberDiv = jQuery(this).find('tr:first,tr.outer').find('th.number div'),
            $outerRows = jQuery(this).find('tr.outer');
        $outerNumberDiv.each(function(j,val1){
            var $this = jQuery(this),
                colwidth = jQuery(this).parent().width(),
                textwidth = jQuery(this).width(),
                maxDataWidth = 0,
                currentWidth = 0,
                margin;
            if((colwidth - textwidth)/2 > 1){
                margin = Math.floor((colwidth - textwidth)/2);
                $this.css('margin-right',margin);

                for(var k = 0; $outerRows.length > k; k++){
                    $this = jQuery($outerRows[k]);
                    currentWidth = $this.find(".number").eq(j).width();
                    if( currentWidth >  maxDataWidth){
                        maxDataWidth = currentWidth;
                    }
                }
                // check widest td div width against TH div width, to see which is wider. If th div is wider, then td div gets its own center margin. Otherwise it uses TH div margin.
                if(textwidth > maxDataWidth){
                    margin = (colwidth - maxDataWidth)/2;
                }
                for(var l = 0; $outerRows.length > l; l++){
                    $this = jQuery($outerRows[l]);
                    $this.find(".number div").eq(j).css("margin-right", margin);
                };
            }
        });
    });
}
	
	/**
	 * To change HTML text based on Div Id.
	 *
	 * @param {String} divId  ID
	 * @param {String} divText Search Text
	 */
	function changeLinkTextContracts(divId, divText) {
	    var existingText = document.getElementById(divId).innerHTML;
	    if (existingText.indexOf('Show Only Top 10') > -1) {
	        document.getElementById(divId).innerHTML = 'Show more ' + divText;
	    } else {
	        document.getElementById(divId).innerHTML = 'Show Only Top 10 ' + divText;
	    }
	}	
	