

/**
 * Return the settings object for a particular table
 *  @param {node} nTable table we are using as a dataTable
 *  @returns {object} Settings object - or null if not found
 *  @memberof DataTable#oApi
 */
function _fnSettingsFromNode ( nTable )
{
	for ( var i=0 ; i<DataTable.settings.length ; i++ )
	{
		if ( DataTable.settings[i].nTable === nTable )
		{
			return DataTable.settings[i];
		}
	}
	
	return null;
}


/**
 * Return an array with the TR nodes for the table
 *  @param {object} oSettings dataTables settings object
 *  @returns {array} TR array
 *  @memberof DataTable#oApi
 */
function _fnGetTrNodes ( oSettings )
{
	var aNodes = [];
	var aoData = oSettings.aoData;
	for ( var i=0, iLen=aoData.length ; i<iLen ; i++ )
	{
		if ( aoData[i].nTr !== null )
		{
			aNodes.push( aoData[i].nTr );
		}
	}
	return aNodes;
}


/**
 * Return an flat array with all TD nodes for the table, or row
 *  @param {object} oSettings dataTables settings object
 *  @param {int} [iIndividualRow] aoData index to get the nodes for - optional 
 *    if not given then the return array will contain all nodes for the table
 *  @returns {array} TD array
 *  @memberof DataTable#oApi
 */
function _fnGetTdNodes ( oSettings, iIndividualRow )
{
	var anReturn = [];
	var iCorrector;
	var anTds;
	var iRow, iRows=oSettings.aoData.length,
		iColumn, iColumns, oData, sNodeName, iStart=0, iEnd=iRows;
	
	/* Allow the collection to be limited to just one row */
	if ( iIndividualRow !== undefined )
	{
		iStart = iIndividualRow;
		iEnd = iIndividualRow+1;
	}

	for ( iRow=iStart ; iRow<iEnd ; iRow++ )
	{
		oData = oSettings.aoData[iRow];
		if ( oData.nTr !== null )
		{
			/* get the TD child nodes - taking into account text etc nodes */
			anTds = [];
			for ( iColumn=0, iColumns=oData.nTr.childNodes.length ; iColumn<iColumns ; iColumn++ )
			{
				sNodeName = oData.nTr.childNodes[iColumn].nodeName.toLowerCase();
				if ( sNodeName == 'td' || sNodeName == 'th' )
				{
					anTds.push( oData.nTr.childNodes[iColumn] );
				}
			}

			iCorrector = 0;
			for ( iColumn=0, iColumns=oSettings.aoColumns.length ; iColumn<iColumns ; iColumn++ )
			{
				if ( oSettings.aoColumns[iColumn].bVisible )
				{
					anReturn.push( anTds[iColumn-iCorrector] );
				}
				else
				{
					anReturn.push( oData._anHidden[iColumn] );
					iCorrector++;
				}
			}
		}
	}

	return anReturn;
}


/**
 * Log an error message
 *  @param {object} oSettings dataTables settings object
 *  @param {int} iLevel log error messages, or display them to the user
 *  @param {string} sMesg error message
 *  @memberof DataTable#oApi
 */
function _fnLog( oSettings, iLevel, sMesg )
{
	var sAlert = (oSettings===null) ?
		"DataTables warning: "+sMesg :
		"DataTables warning (table id = '"+oSettings.sTableId+"'): "+sMesg;
	
	if ( iLevel === 0 )
	{
		if ( DataTable.ext.sErrMode == 'alert' )
		{
			alert( sAlert );
		}
		else
		{
			throw sAlert;
		}
		return;
	}
	else if ( console !== undefined && console.log )
	{
		console.log( sAlert );
	}
}


/**
 * See if a property is defined on one object, if so assign it to the other object
 *  @param {object} oRet target object
 *  @param {object} oSrc source object
 *  @param {string} sName property
 *  @param {string} [sMappedName] name to map too - optional, sName used if not given
 *  @memberof DataTable#oApi
 */
function _fnMap( oRet, oSrc, sName, sMappedName )
{
	if ( sMappedName === undefined )
	{
		sMappedName = sName;
	}
	if ( oSrc[sName] !== undefined )
	{
		oRet[sMappedName] = oSrc[sName];
	}
}


/**
 * Extend objects - very similar to jQuery.extend, but deep copy objects, and shallow
 * copy arrays. The reason we need to do this, is that we don't want to deep copy array
 * init values (such as aaSorting) since the dev wouldn't be able to override them, but
 * we do want to deep copy arrays.
 *  @param {object} oOut Object to extend
 *  @param {object} oExtender Object from which the properties will be applied to oOut
 *  @returns {object} oOut Reference, just for convenience - oOut === the return.
 *  @memberof DataTable#oApi
 *  @todo This doesn't take account of arrays inside the deep copied objects.
 */
function _fnExtend( oOut, oExtender )
{
	for ( var prop in oOut )
	{
		if ( oOut.hasOwnProperty(prop) && oExtender[prop] !== undefined )
		{
			if ( typeof oInit[prop] === 'object' && $.isArray(oExtender[prop]) === false )
			{
				$.extend( true, oOut[prop], oExtender[prop] );
			}
			else
			{
				oOut[prop] = oExtender[prop];
			}
		}
	}

	return oOut;
}


/**
 * Bind an event handers to allow a click or return key to activate the callback.
 * This is good for accessability since a return on the keyboard will have the
 * same effect as a click, if the element has focus.
 *  @param {element} n Element to bind the action to
 *  @param {object} oData Data object to pass to the triggered function
 *  @param {function) fn Callback function for when the event is triggered
 *  @memberof DataTable#oApi
 */
function _fnBindAction( n, oData, fn )
{
	$(n)
		.bind( 'click.DT', oData, function (e) {
				fn(e);
				n.blur(); // Remove focus outline for mouse users
			} )
		.bind( 'keypress.DT', oData, function (e){
			if ( e.which === 13 ) {
				fn(e);
			} } )
		.bind( 'selectstart.DT', function () {
			/* Take the brutal approach to cancelling text selection */
			return false;
			} );
}


/**
 * Register a callback function. Easily allows a callback function to be added to
 * an array store of callback functions that can then all be called together.
 *  @param {object} oSettings dataTables settings object
 *  @param {string} sStore Name of the array storeage for the callbacks in oSettings
 *  @param {function} fn Function to be called back
 *  @param {string) sName Identifying name for the callback (i.e. a label)
 *  @memberof DataTable#oApi
 */
function _fnCallbackReg( oSettings, sStore, fn, sName )
{
	if ( fn )
	{
		oSettings[sStore].push( {
			"fn": fn,
			"sName": sName
		} );
	}
}


/**
 * Fire callback functions and trigger events. Note that the loop over the callback
 * array store is done backwards! Further note that you do not want to fire off triggers
 * in time sensitive applications (for example cell creation) as its slow.
 *  @param {object} oSettings dataTables settings object
 *  @param {string} sStore Name of the array storeage for the callbacks in oSettings
 *  @param {string} sTrigger Name of the jQuery custom event to trigger. If null no trigger
 *    is fired
 *  @param {array) aArgs Array of arguments to pass to the callback function / trigger
 *  @memberof DataTable#oApi
 */
function _fnCallbackFire( oSettings, sStore, sTrigger, aArgs )
{
	var aoStore = oSettings[sStore];
	var aRet =[];

	for ( var i=aoStore.length-1 ; i>=0 ; i-- )
	{
		aRet.push( aoStore[i].fn.apply( oSettings.oInstance, aArgs ) );
	}

	if ( sTrigger !== null )
	{
		$(oSettings.oInstance).trigger(sTrigger, aArgs);
	}

	return aRet;
}


/**
 * JSON stringify. If JSON.stringify it provided by the browser, json2.js or any other
 * library, then we use that as it is fast, safe and accurate. If the function isn't 
 * available then we need to built it ourselves - the insperation for this function comes
 * from Craig Buckler ( http://www.sitepoint.com/javascript-json-serialization/ ). It is
 * not perfect and absolutely should not be used as a replacement to json2.js - but it does
 * do what we need, without requiring a dependency for DataTables.
 *  @param {object} o JSON object to be converted
 *  @returns {string} JSON string
 *  @memberof DataTable#oApi
 */
var _fnJsonString = (window.JSON) ? JSON.stringify : function( o )
{
	/* Not an object or array */
	var sType = typeof o;
	if (sType !== "object" || o === null)
	{
		// simple data type
		if (sType === "string")
		{
			o = '"'+o+'"';
		}
		return o+"";
	}

	/* If object or array, need to recurse over it */
	var
		sProp, mValue,
		json = [],
		bArr = $.isArray(o);
	
	for (sProp in o)
	{
		mValue = o[sProp];
		sType = typeof mValue;

		if (sType === "string")
		{
			mValue = '"'+mValue+'"';
		}
		else if (sType === "object" && mValue !== null)
		{
			mValue = _fnJsonString(mValue);
		}

		json.push((bArr ? "" : '"'+sProp+'":') + mValue);
	}

	return (bArr ? "[" : "{") + json + (bArr ? "]" : "}");
};

