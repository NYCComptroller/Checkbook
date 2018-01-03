(function ($) {
	if (typeof Drupal != "undefined") {
    	Drupal.behaviors.cookieBreadcrumbs = {
            attach:function (context, settings) {
				var MaximumNumberOfLinks = 7;
				var CookieName = "checkbookHistoryLinks";
				var CookieDomainName = "checkbookHistoryDomain";
				var HistoryLink = [];
				var HistoryTitle = [];
				
				function GetCookie() {
					var cookiecontent = '';
					if(document.cookie.length > 0) {
						var cookiename = CookieName ;
						cookiecontent = $.cookie(cookiename);						
					}
					if(cookiecontent == null)  { return; }
					cookiecontent = unescape(cookiecontent);
					var historyList = cookiecontent.split('@@');
					for( var i = 0; i < historyList.length; i++ ) {
						var link = historyList[i].split('::',2);
						HistoryLink.push(link[0]);
						HistoryTitle.push(link[1]);
					}
				}
				
				function PutCookie() {
					if( HistoryLink.length < 1 ) { return; }
					var len = HistoryLink.length;
					var pairs = [];
					var breadcrumbHTML = '<a href=\'/\' class=\'homeLink\' >Home</a>';
					for( var i = 0; i < len; i++ ) {
						pairs.push(encodeURI(HistoryLink[i])+'::'+HistoryTitle[i]);
						if(i == len -1 ){
							breadcrumbHTML =  breadcrumbHTML + ' >> <span class=\'inline\'>' + HistoryTitle[i] + '<span>' ;
						}else{
							breadcrumbHTML =  breadcrumbHTML + ' >> <span class=\'inline\'><a href=\'' + encodeURI(HistoryLink[i]) +  '\'>'  + HistoryTitle[i] + '</a></span>' ;
						}
						
					}
					var value = pairs.join('@@');				
					$.cookie(CookieName,value,{ path: '/' });
					$('#breadcrumb').html(breadcrumbHTML);
				}
				
				function RecordCurrentPage() {
					var link = window.location.pathname ;
					var title =  $('#breadcrumb-title-hidden').html();
					var len = HistoryLink.length;
					
					link = link + window.location.search;
					if(HistoryLink[len -1] !== link){
						if( HistoryLink.length === MaximumNumberOfLinks ) {
							HistoryLink[MaximumNumberOfLinks - 1 ] = link ;
							HistoryTitle[MaximumNumberOfLinks - 1 ] = title ;
						}else{
							HistoryLink.push(link);
							HistoryTitle.push(title);	
						}
						
					}
				}
				var link = window.location.pathname;
				if((link.match(/contract/) === 'contract')||(link.match(/payroll/) === 'payroll')||(link.match(/spending/) === 'spending')||(link.match(/budget/) === 'budget')||(link.match(/revenue/) === 'revenue')){
					if(link.match(/gridview/) !== "gridview" && link.match(/createalert/) !== "createalert" && link.match(/newwindow/) !== "newwindow" && link.match(/admin/) !== "admin" && link.match(/-api/) !== "-api" ){
						if(link.match(/contract/) === 'contract' && link.match(/datasource/) !== 'datasource' && link.match(/checkbook_oge/) !== 'checkbook_oge'){
							if($.cookie(CookieDomainName) !== 'contract')
								$.cookie(CookieName,null,{ path: '/' });
							$.cookie(CookieDomainName,'contract',{ path: '/' });
						}else if(link.match(/payroll/) === 'payroll'){
							if($.cookie(CookieDomainName) !== 'payroll')
								$.cookie(CookieName,null,{ path: '/' });
							$.cookie(CookieDomainName,'payroll',{ path: '/' });
						}else if(link.match(/spending/) === 'spending' && link.match(/datasource/) !== 'datasource' && link.match(/checkbook_oge/) !== 'checkbook_oge'){
							if($.cookie(CookieDomainName) !== 'spending')
								$.cookie(CookieName,null,{ path: '/' });
							$.cookie(CookieDomainName,'spending',{ path: '/' });
						}else if(link.match(/budget/) === 'budget'){
                            if($.cookie(CookieDomainName) !== 'budget')
                                $.cookie(CookieName,null,{ path: '/' });
                            $.cookie(CookieDomainName,'budget',{ path: '/' });
                        }else if(link.match(/revenue/) === 'revenue'){
                            if($.cookie(CookieDomainName) !== 'revenue')
                                $.cookie(CookieName,null,{ path: '/' });
                            $.cookie(CookieDomainName,'revenue',{ path: '/' });
                        }else if(link.match(/spending/) === 'spending' && link.match(/datasource/) === 'datasource' && link.match(/checkbook_oge/) === 'checkbook_oge'){
                            if($.cookie(CookieDomainName) !== 'oge_spending')
                                $.cookie(CookieName,null,{ path: '/' });
                            $.cookie(CookieDomainName,'oge_spending',{ path: '/' });
                        }else if(link.match(/contract/) === 'contract' && link.match(/datasource/) === 'datasource' && link.match(/checkbook_oge/) === 'checkbook_oge'){
                            if($.cookie(CookieDomainName) !== 'oge_contract')
                                $.cookie(CookieName,null,{ path: '/' });
                            $.cookie(CookieDomainName,'oge_contract',{ path: '/' });
                        }
						GetCookie();		
						RecordCurrentPage();
						PutCookie();
					}
				}	
				$('#node-widget-472 .top-navigation-left a').click(function (event) {
	                $.cookie(CookieName,null,{ path: '/' });
	            });				
				$('#year_list_chzn li').live("click",function (event) {
					$.cookie(CookieName,null,{ path: '/' });					
	            });	
				$('#year_list').change(function (event) {
					$.cookie(CookieName,null,{ path: '/' });					
	            });					
				$('.nice-menu a').click(function (event) {
					$.cookie(CookieName,null,{ path: '/' });
	            });		
				$('.region-branding a').click(function (event) {
					$.cookie(CookieName,null,{ path: '/' });
	            });	
				$('a.homeLink').click(function (event) {
					$.cookie(CookieName,null,{ path: '/' });
	            });					
            }
    	};	
	}
    	
}(jQuery));


