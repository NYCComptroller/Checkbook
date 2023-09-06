(function ( $, Drupal, cookies) {
	if (typeof Drupal != "undefined") {
    Drupal.behaviors.cookieBreadcrumbs = {
      attach:function (context, settings) {
        let MaximumNumberOfLinks = 7;
        let CookieName = "checkbookHistoryLinks";
        let CookieDomainName = "checkbookHistoryDomain";
        let HistoryLink = [];
        let HistoryTitle = [];

        function GetCookie() {
          let cookieContent = '';
          if(document.cookie.length > 0) {
            cookieContent = cookies.get(CookieName);
          }
          if(cookieContent == null || cookieContent == 'null')  { return; }
          cookieContent = unescape(cookieContent);
          let historyList = cookieContent.split('@@');
          for( let i = 0; i < historyList.length; i++ ) {
            let link = historyList[i].split('::',2);
            HistoryLink.push(link[0]);
            HistoryTitle.push(link[1]);
          }
        }

        function PutCookie() {
          if( HistoryLink.length < 1 ) { return; }
          let len = HistoryLink.length;
          let pairs = [];
          let breadcrumbHTML = '<a href=\'/\' class=\'homeLink\' >Home</a>';
          for( let i = 0; i < len; i++ ) {
            pairs.push(encodeURI(HistoryLink[i])+'::'+HistoryTitle[i]);
            let delimiter = '<span class="delimiter">»</span>';
            if(i === len -1 ){
              breadcrumbHTML =  breadcrumbHTML + ' ' + delimiter + ' <span class=\'inline\'>' + HistoryTitle[i] + '<span>' ;
            }else{
              breadcrumbHTML =  breadcrumbHTML + ' ' + delimiter + ' <span class=\'inline\'><a href=\'' + encodeURI(HistoryLink[i]) +  '\'>'  + HistoryTitle[i] + '</a></span>' ;
            }

          }
          let value = pairs.join('@@');
          cookies.set(CookieName,value, '/');
          $('#breadcrumb').html('<span class="breadcrumb-inner">' + breadcrumbHTML + '</span>');
        }

        function RecordCurrentPage() {
          let link = window.location.pathname ;
          let title = drupalSettings.checkbook_custom_breadcrumbs ? drupalSettings.checkbook_custom_breadcrumbs.breadcrumbTitle : '';
          let len = HistoryLink.length;

          if (!title) {
            title = $('.js-breadcrumb-title').html();
          }

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

        function ResetCookie() {
          HistoryLink = [];
          HistoryTitle = [];
          RecordCurrentPage();
          PutCookie();
        }

        let link = window.location.pathname;
        if ((link.match(/contract/)) || (link.match(/payroll/)) || (link.match(/spending/)) || (link.match(/budget/)) || (link.match(/revenue/)) || (link == '/')) {
          if (!link.match(/gridview/) && !link.match(/createalert/) && !link.match(/newwindow/) && !link.match(/admin/) && !link.match(/-api/)) {
            if (link.match(/contract/) && !link.match(/datasource/) && !link.match(/checkbook_oge/) && !link.match(/checkbook_nycha/)) {
              if (cookies.get(CookieDomainName) !== 'contract'){
                cookies.set(CookieName, null, '/');
              }
              cookies.set(CookieDomainName, 'contract', '/');
            } else if (link.match(/payroll/) && !link.match(/datasource/) && !link.match(/checkbook_nycha/)) {
              if (cookies.get(CookieDomainName) !== 'payroll') {
                cookies.set(CookieName, null, '/');
              }
              cookies.set(CookieDomainName, 'payroll', '/');
            } else if ((link.match(/spending/) || link == '/') && !link.match(/datasource/) && !link.match(/checkbook_oge/) && !link.match(/checkbook_nycha/)) {
              if (cookies.get(CookieDomainName) !== 'spending') {
                cookies.set(CookieName, null, '/');
              }
              cookies.set(CookieDomainName, 'spending', '/');
            } else if (link.match(/budget/)) {
              if (cookies.get(CookieDomainName) !== 'budget') {
                cookies.set(CookieName, null, '/');
              }
              cookies.set(CookieDomainName, 'budget', '/');
            } else if (link.match(/revenue/)) {
              if (cookies.get(CookieDomainName) !== 'revenue') {
                cookies.set(CookieName, null, '/');
              }
              cookies.set(CookieDomainName, 'revenue', '/');
            } else if (link.match(/spending/) && link.match(/datasource/) && link.match(/checkbook_oge/)) {
              if (cookies.get(CookieDomainName) !== 'oge_spending') {
                cookies.set(CookieName, null, '/');
              }
              cookies.set(CookieDomainName, 'oge_spending', '/');
            } else if (link.match(/contract/) && link.match(/datasource/) && link.match(/checkbook_oge/)) {
              if (cookies.get(CookieDomainName) !== 'oge_contract') {
                cookies.set(CookieName, null, '/');
              }
              cookies.set(CookieDomainName, 'oge_contract', '/');
            } else if (link.match(/nycha_contracts/)) {
              if (cookies.get(CookieDomainName) !== 'nycha_contract') {
                cookies.set(CookieName, null, '/');
              }
              cookies.set(CookieDomainName, 'nycha_contract', '/');
            } else if (link.match(/nycha_spending/)) {
              if (cookies.get(CookieDomainName) !== 'nycha_spending') {
                cookies.set(CookieName, null, '/');
              }
              cookies.set(CookieDomainName, 'nycha_spending', '/');
            } else if (link.match(/nycha_revenue/)) {
              if (cookies.get(CookieDomainName) !== 'nycha_revenue') {
                cookies.set(CookieName, null, '/');
              }
              cookies.set(CookieDomainName, 'nycha_revenue', '/');
            }else if (link.match(/nycha_budget/)) {
              if (cookies.get(CookieDomainName) !== 'nycha_budget') {
                cookies.set(CookieName, null, '/');
              }
              cookies.set(CookieDomainName, 'nycha_budget', '/');
            } else if (link.match(/payroll/) && link.match(/datasource/) && link.match(/checkbook_nycha/)) {
              if (cookies.get(CookieDomainName) !== 'nycha_payroll') {
                cookies.set(CookieName, null, '/');
              }
              cookies.set(CookieDomainName, 'nycha_payroll', '/');
            }

            let homepages = [ '/'];
            if (homepages.indexOf(window.location.pathname) >= 0 && document.location.search.length == 0) {
              ResetCookie();
            } else {
              GetCookie();
              RecordCurrentPage();
              PutCookie();
            }
          }
        }
        $('#node-widget-472 .top-navigation-left a').click(function (event) {
          cookies.set(CookieName,null,'/');
        });
        $('#year_list_chzn li').on("click",function (event) {
          cookies.set(CookieName,null,'/');
              });
        $('#year_list').change(function (event) {
          cookies.set(CookieName,null,'/');
              });
        $('.nice-menu a').click(function (event) {
          cookies.set(CookieName,null,'/');
              });
        $('.region-branding a').click(function (event) {
          cookies.set(CookieName,null,'/');
              });
        $('a.homeLink').click(function (event) {
          cookies.set(CookieName,null,'/');
        });
      }
    };
	}

}(jQuery, Drupal, window.Cookies));


