/**
 * @summary     DataTables
 * @description Paginate, search and sort HTML tables
 * @version     1.9.0
 * @file        jquery.dataTables.js
 * @author      Allan Jardine (www.sprymedia.co.uk)
 * @contact     www.sprymedia.co.uk/contact
 *
 * @copyright Copyright 2008-2012 Allan Jardine, all rights reserved.
 *
 * This source file is free software, under either the GPL v2 license or a
 * BSD style license, available at:
 *   http://datatables.net/license_gpl2
 *   http://datatables.net/license_bsd
 *
 * This source file is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the license files for details.
 *
 * For details please refer to: http://www.datatables.net
 */
jQuery.fn.dataTableExt.oPagination.input = {
    "fnInit": function ( oSettings, nPaging, fnCallbackDraw )
    {
        var nFirst = document.createElement( 'span' );
        var nPrevious = document.createElement( 'span' );
        var nNext = document.createElement( 'span' );
        var nLast = document.createElement( 'span' );
        var nInput = document.createElement( 'input' );
        var nPage = document.createElement( 'span' );
        var nOf = document.createElement( 'span' );

        nFirst.innerHTML = oSettings.oLanguage.oPaginate.sFirst;
        nPrevious.innerHTML = oSettings.oLanguage.oPaginate.sPrevious;
        nNext.innerHTML = oSettings.oLanguage.oPaginate.sNext;
        nLast.innerHTML = oSettings.oLanguage.oPaginate.sLast;

        nFirst.className = "paginate_button first";
        nPrevious.className = "paginate_button previous";
        nNext.className="paginate_button next";
        nLast.className = "paginate_button last";
        nOf.className = "paginate_of";
        nPage.className = "paginate_page";

        if ( oSettings.sTableId !== '' )
        {
            nPaging.setAttribute( 'id', oSettings.sTableId+'_paginate' );
            nPrevious.setAttribute( 'id', oSettings.sTableId+'_previous' );
            nPrevious.setAttribute( 'id', oSettings.sTableId+'_previous' );
            nNext.setAttribute( 'id', oSettings.sTableId+'_next' );
            nLast.setAttribute( 'id', oSettings.sTableId+'_last' );
        }

        nInput.type = "text";
        //nInput.style.width = "26px";
        //nInput.style.display = "inline";
        nPage.innerHTML = "Page ";

        nPaging.appendChild( nFirst );
        nPaging.appendChild( nPrevious );
        nPaging.appendChild( nPage );
        nPaging.appendChild( nInput );
        nPaging.appendChild( nOf );
        nPaging.appendChild( nNext );
        nPaging.appendChild( nLast );

        jQuery(nFirst).click( function () {
            oSettings.oApi._fnPageChange( oSettings, "first" );
            fnCallbackDraw( oSettings );
        } );

        jQuery(nPrevious).click( function() {
            oSettings.oApi._fnPageChange( oSettings, "previous" );
            fnCallbackDraw( oSettings );
        } );

        jQuery(nNext).click( function() {
            oSettings.oApi._fnPageChange( oSettings, "next" );
            fnCallbackDraw( oSettings );
        } );

        jQuery(nLast).click( function() {
            oSettings.oApi._fnPageChange( oSettings, "last" );
            fnCallbackDraw( oSettings );
        } );

        jQuery(nInput).keyup( function (e) {

            if ( e.which == 38 || e.which == 39 )
            {
                this.value++;
            }
            else if ( (e.which == 37 || e.which == 40) && this.value > 1 )
            {
                this.value--;
            }


            var pages = Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength );
            if ( this.value == "" || this.value.match(/[^0-9]/) || (this.value) > pages)
            {
                if(this.value.match(/[^0-9]/)){
                    alert('Only numbers are allowed.');
                }else if((this.value) > pages){
                    alert('Cannot exceed '+pages);
                }
                /* Nothing entered or non-numeric character or greater than available pages*/
                return;
            }

            var iNewStart = oSettings._iDisplayLength * (this.value - 1);
            if ( iNewStart > oSettings.fnRecordsDisplay() )
            {
                /* Display overrun */
                oSettings._iDisplayStart = (Math.ceil((oSettings.fnRecordsDisplay()-1) /
                    oSettings._iDisplayLength)-1) * oSettings._iDisplayLength;
                fnCallbackDraw( oSettings );
                return;
            }

            oSettings._iDisplayStart = iNewStart;
            fnCallbackDraw( oSettings );
        } );

        /* Take the brutal approach to cancelling text selection */
        jQuery('span', nPaging).bind( 'mousedown', function () { return false; } );
        jQuery('span', nPaging).bind( 'selectstart', function () { return false; } );
    },


    "fnUpdate": function ( oSettings, fnCallbackDraw )
    {
        if ( !oSettings.aanFeatures.p )
        {
            return;
        }
        var iPages = Math.ceil((oSettings.fnRecordsDisplay()) / oSettings._iDisplayLength);
        if(iPages <= 1){
            jQuery('.paging_input').hide();
            return;
        }
        var iCurrentPage = Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength) + 1;

        /* Loop over each instance of the pager */
        var an = oSettings.aanFeatures.p;
        for ( var i=0, iLen=an.length ; i<iLen ; i++ )
        {
            var spans = an[i].getElementsByTagName('span');
            var inputs = an[i].getElementsByTagName('input');
            spans[3].innerHTML = " of "+iPages
            inputs[0].value = iCurrentPage;
        }
    }




};