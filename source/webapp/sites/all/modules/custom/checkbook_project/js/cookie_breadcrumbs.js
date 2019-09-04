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
                if ((link.match(/contract/)) || (link.match(/payroll/)) || (link.match(/spending/)) || (link.match(/budget/)) || (link.match(/revenue/))) {
                    if (!link.match(/gridview/) && !link.match(/createalert/) && !link.match(/newwindow/) && !link.match(/admin/) && !link.match(/-api/)) {
                        if (link.match(/contract/) && !link.match(/datasource/) && !link.match(/checkbook_oge/) && !link.match(/checkbook_nycha/)) {
                            if ($.cookie(CookieDomainName) !== 'contract')
                                $.cookie(CookieName, null, {path: '/'});
                            $.cookie(CookieDomainName, 'contract', {path: '/'});
                        } else if (link.match(/payroll/) && !link.match(/datasource/) && !link.match(/checkbook_nycha/)) {
                            if ($.cookie(CookieDomainName) !== 'payroll')
                                $.cookie(CookieName, null, {path: '/'});
                            $.cookie(CookieDomainName, 'payroll', {path: '/'});
                        } else if (link.match(/spending/) && !link.match(/datasource/) && !link.match(/checkbook_oge/) && !link.match(/checkbook_nycha/)) {
                            if ($.cookie(CookieDomainName) !== 'spending')
                                $.cookie(CookieName, null, {path: '/'});
                            $.cookie(CookieDomainName, 'spending', {path: '/'});
                        } else if (link.match(/budget/)) {
                            if ($.cookie(CookieDomainName) !== 'budget')
                                $.cookie(CookieName, null, {path: '/'});
                            $.cookie(CookieDomainName, 'budget', {path: '/'});
                        } else if (link.match(/revenue/)) {
                            if ($.cookie(CookieDomainName) !== 'revenue')
                                $.cookie(CookieName, null, {path: '/'});
                            $.cookie(CookieDomainName, 'revenue', {path: '/'});
                        } else if (link.match(/spending/) && link.match(/datasource/) && link.match(/checkbook_oge/)) {
                            if ($.cookie(CookieDomainName) !== 'oge_spending')
                                $.cookie(CookieName, null, {path: '/'});
                            $.cookie(CookieDomainName, 'oge_spending', {path: '/'});
                        } else if (link.match(/contract/) && link.match(/datasource/) && link.match(/checkbook_oge/)) {
                            if ($.cookie(CookieDomainName) !== 'oge_contract')
                                $.cookie(CookieName, null, {path: '/'});
                            $.cookie(CookieDomainName, 'oge_contract', {path: '/'});
                        } else if (link.match(/nycha_contracts/)) {
                          if ($.cookie(CookieDomainName) !== 'nycha_contract')
                            $.cookie(CookieName, null, {path: '/'});
                          $.cookie(CookieDomainName, 'nycha_contract', {path: '/'});
                        } else if (link.match(/nycha_spending/)) {
                          if ($.cookie(CookieDomainName) !== 'nycha_spending')
                            $.cookie(CookieName, null, {path: '/'});
                          $.cookie(CookieDomainName, 'nycha_spending', {path: '/'});
                        } else if (link.match(/payroll/) && link.match(/datasource/) && link.match(/checkbook_nycha/)) {
                          if ($.cookie(CookieDomainName) !== 'nycha_payroll')
                            $.cookie(CookieName, null, {path: '/'});
                          $.cookie(CookieDomainName, 'nycha_payroll', {path: '/'});
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


