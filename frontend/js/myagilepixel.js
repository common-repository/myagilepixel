/**
 * MyAgilePixel JS
 * version 1.3.23
 * wordpress function
 */

(function() {
	'use strict';

	const version = '1.3.23';

	var MyAgilePixel = {

		/* -----------------------------------
		 *
		 * get version function
		 *
		 * -------------------------------- */
		getVersion : function (){
			return version;
		},

		/* -----------------------------------
		 *
		 * greet function
		 *
		 * -------------------------------- */
		greet: function(){
			console.log("%c    \u256D\u2501\u256E\u256D\u2501\u256E\u2571\u2571\u2571\u2571\u256D\u2501\u2501\u2501\u256E\u2571\u2571\u2571\u256D\u256E\u2571\u2571\u2571\u2571\u256D\u2501\u2501\u2501\u256E\u2571\u2571\u2571\u2571\u2571\u2571\u256D\u256E    \r\n    \u2503\u2503\u2570\u256F\u2503\u2503\u2571\u2571\u2571\u2571\u2503\u256D\u2501\u256E\u2503\u2571\u2571\u2571\u2503\u2503\u2571\u2571\u2571\u2571\u2503\u256D\u2501\u256E\u2503\u2571\u2571\u2571\u2571\u2571\u2571\u2503\u2503    \r\n    \u2503\u256D\u256E\u256D\u256E\u2523\u256E\u2571\u256D\u256E\u2503\u2503\u2571\u2503\u2523\u2501\u2501\u2533\u252B\u2503\u256D\u2501\u2501\u256E\u2503\u2570\u2501\u256F\u2523\u2533\u256E\u256D\u2533\u2501\u2501\u252B\u2503    \r\n    \u2503\u2503\u2503\u2503\u2503\u2503\u2503\u2571\u2503\u2503\u2503\u2570\u2501\u256F\u2503\u256D\u256E\u2523\u252B\u2503\u2503\u2503\u2501\u252B\u2503\u256D\u2501\u2501\u254B\u254B\u254B\u254B\u252B\u2503\u2501\u252B\u2503    \r\n    \u2503\u2503\u2503\u2503\u2503\u2503\u2570\u2501\u256F\u2503\u2503\u256D\u2501\u256E\u2503\u2570\u256F\u2503\u2503\u2570\u252B\u2503\u2501\u252B\u2503\u2503\u2571\u2571\u2503\u2523\u254B\u254B\u252B\u2503\u2501\u252B\u2570\u256E   \r\n    \u2570\u256F\u2570\u256F\u2570\u253B\u2501\u256E\u256D\u256F\u2570\u256F\u2571\u2570\u253B\u2501\u256E\u2523\u253B\u2501\u253B\u2501\u2501\u256F\u2570\u256F\u2571\u2571\u2570\u253B\u256F\u2570\u253B\u2501\u2501\u253B\u2501\u256F   \r\n    \u2571\u2571\u2571\u2571\u2571\u256D\u2501\u256F\u2503\u2571\u2571\u2571\u2571\u2571\u256D\u2501\u256F\u2503                       \r\n   \u2571\u2571\u2571\u2571\u2571\u2570\u2501\u2501\u256F\u2571\u2571\u2571\u2571\u2571\u2570\u2501\u2501\u256F                        \r\n\r\n%chttps://www.myagilepixel.com/\r\n","color: black; font-size: 12px; background-color: #f44c13","color: black; font-size: 12px;"
			);
		},
		showNotificationBar: function( message = null, success = null )
		{
			let body = document.querySelector('body');
			let bar  = document.querySelector('#mapx_notification_bar');

			let prev_message = "<span class='mapx_close_notification_bar'>Close</span>";

			if( bar )
			{
				prev_message = bar.innerHTML  + "<br>";
			}
			else
			{
				bar = document.createElement('div');
				bar.setAttribute('id','mapx_notification_bar');
				body.append( bar );


				document.addEventListener('click', function (event) {
					if (!event.target.matches('.mapx_close_notification_bar')) return;

					event.preventDefault();

					bar.parentNode.removeChild( bar );

				}, false);

			}

			let final_message = prev_message + '<b>[MyAgilePixel admin-only notification]</b> ' + message;

			if( success == 1 )
			{
				final_message = final_message + '&nbsp;<span class="mapx_proxification_success_true">OK!</span>';
			}

			if( success == 2 )
			{
				final_message = final_message + '&nbsp;<span class="mapx_proxification_success_false">ERROR!</span>';
			}

			if( success == null )
			{
				final_message = final_message;
			}

			bar.innerHTML = final_message;
		}

	};

	let do_greet = true;

	if( ( typeof map_wl  !== 'undefined' && map_wl  == 1 ) ||
		( typeof map_full_config !== 'undefined' && typeof map_full_config?.map_wl  !== 'undefined' && map_full_config?.map_wl  == 1 )
	)
	{
		do_greet = false;
	}

	if( do_greet ) MyAgilePixel.greet();

	var MAPX_Cookie = {
		setDayDuration: function (name, value, days) {
			try {
				if (days) {
					var date = new Date();
					date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
					var expires = "; expires=" + date.toGMTString();
				} else
					var expires = "";
				document.cookie = name + "=" + value + expires + "; path=/";
			}
			catch( e )
			{
				console.debug( e );
				return null;
			}
		},
		setMinuteDuration: function (name, value, minute) {
			try {
				if (minute) {
					var date = new Date();
					date.setTime(date.getTime() + (minute *  60 * 1000));
					var expires = "; expires=" + date.toGMTString();
				} else
					var expires = "";
				document.cookie = name + "=" + value + expires + "; path=/";
			}
			catch( e )
			{
				console.debug( e );
				return null;
			}
		},
		setGMTString: function (name, value, GMTString) {
			try {

				var expires = "; expires=" + GMTString;
				document.cookie = name + "=" + value + expires + "; path=/";
			}
			catch( e )
			{
				console.debug( e );
				return null;
			}
		},
		read: function (name) {
			try {
				var nameEQ = name + "=";
				var ca = document.cookie.split(';');
				for (var i = 0; i < ca.length; i++) {
					var c = ca[i];
					while (c.charAt(0) == ' ') {
						c = c.substring(1, c.length);
					}
					if (c.indexOf(nameEQ) === 0) {
						return c.substring(nameEQ.length, c.length);
					}
				}
				return null;
			}
			catch( e )
			{
				console.debug( e );
				return null;
			}
		},
		exists: function( name ) {
			return ( this.read( name ) !== null);
		},
		delete: function( name ) {

			var expirationDate = new Date();
			expirationDate.setTime( expirationDate.getTime() - 3600000 ); // One hour ago

			// Set the cookie with an expired expiration time
			document.cookie = name + "=; expires=" + expirationDate.toUTCString() + "; path=/";

		},
		getCookieNameList: function (){
				var cookies = document.cookie.split( ';' );

				var return_data = [];

				for( var i = 0; i < cookies.length; i++ )
				{
					var cookie = cookies[i].trim(); // Remove any leading/trailing spaces
					var separatorIndex = cookie.indexOf( '=' );
					if( separatorIndex === -1)
					{
				  		continue; // Skip if the cookie is malformed (doesn't contain '=')
					}

					var name = cookie.substring( 0, separatorIndex );

					return_data.push( name );
				}

				return return_data;
		}
	};

	//invoked by frontend to call tracking function checking periodically if they are defined
	var MAPX_Call_TrackFunc = {
		doInvokeTrackingFunction: function ( realm, ...args )
		{

			console.groupCollapsed( '[doInvokeTrackingFunction] -> realm=' + realm );
			console.table( ...args );

			let is_defined = false;
			let function_name = null;
			let alt_function_name = null;

			if( realm === 'MyAgilePixelRetrasmitBeacon' )
			{
				if( typeof window['myagilepixel_gtag'] !== 'undefined' )
				{
					function_name = 'MyAgilePixelRetrasmitBeacon';
				}

				if( !!function_name )
				{
					is_defined = ( typeof window[ function_name ] !== 'undefined' );
				}
			}

			if( realm === 'MyAgilePixelRetrasmitBeaconGADS' )
			{
				if( typeof window['myagilepixel_gtag'] !== 'undefined' )
				{
					function_name = 'MyAgilePixelRetrasmitBeaconGADS';
				}

				if( !!function_name )
				{
					is_defined = ( typeof window[ function_name ] !== 'undefined' );
				}
			}

			if( realm === 'gtag' )
			{
				function_name = 'gtag';

				if( typeof window['myagilepixel_gtag'] !== 'undefined' &&
					! ( typeof mpx_settings !== 'undefined' && mpx_settings?.analytics?.use_ga_advanced_features )
				)
				{
					function_name = 'myagilepixel_gtag' ;
				}
				else if( typeof window['gtag'] !== 'undefined' )
				{
					function_name = 'gtag' ;
				}

				is_defined = ( typeof window[ function_name ] !== 'undefined' );

				//ga4_loaded_check
				if( mpx_settings?.analytics?.ga4_loaded_check && is_defined )
				{
					is_defined = window?.dataLayer?.find(item => typeof item === 'object' && item[0] === 'config');
				}

			}

			if( realm === 'fbq' )
			{
				function_name = 'fbq';

				if( typeof window['myagilepixel_fbq'] !== 'undefined' )
				{
					function_name = 'myagilepixel_fbq' ;
				}

				is_defined = ( typeof window[ function_name ] !== 'undefined' );
			}

			if( realm === 'ttq' )
			{
				function_name = 'ttq';

				if( typeof window['myagilepixel_ttq'] !== 'undefined' )
				{
					function_name = 'myagilepixel_ttq' ;
				}

				is_defined = ( typeof window[ function_name ] !== 'undefined' );

				alt_function_name = 'track';
			}

			if( is_defined )
			{
				console.debug( 'function is_defined, function_name='+function_name + ', alt_function_name='+ alt_function_name );
				console.groupEnd();

				if( alt_function_name )
				{
					window[ function_name ][ alt_function_name ]( ...args );
				}
				else
				{
					window[ function_name ]( ...args );
				}
			}
			else
			{
				console.debug( 'function not defined, function_name='+function_name + ', alt_function_name='+ alt_function_name );
				console.groupEnd();

				setTimeout( function(){

					MAPX_Call_TrackFunc.doInvokeTrackingFunction( realm, ...args );

				}, 500 );
			}
		}
	};

	/*** Register plugin in window object */
	window.MyAgilePixel = MyAgilePixel;
	window.MAPX_Cookie = MAPX_Cookie;
	window.MAPX_Call_TrackFunc = MAPX_Call_TrackFunc;

}());


(function() {
	'use strict';

	// internal variables
	var initted = false;

	//var reconfigurable variables
	var _config = {
		'ajax_url'								: null,
		'sec_token'								: null,
		'realm'									: 'tiktokapi',
		'internal_debug'						: false,
		'remove_click_id' 						: true,
		'remove_utm_tag' 						: true,
		'remove_user_agent' 					: true,
		'send_desktop_mobile_user_agent' 		: true,
		'enable_session_life_cookie_duration' 	: true,
		'version_number'						: '1.3.23',
	};

	//costants
	const clientKey = 'MYPX_t_cid';
	const eventKeys = {
		'PageView' 				: 'PageView',
		'ClickButton'  			: 'ClickButton',
		'Download'				: 'Download',
		'CompleteRegistration'	: 'CompleteRegistration',
		'AddPaymentInfo'		: 'AddPaymentInfo',
		'AddToCart'				: 'AddToCart',
		'AddToWishlist'			: 'AddToWishlist',
		'CompleteRegistration' 	: 'CompleteRegistration',
		'InitiateCheckout'		: 'InitiateCheckout',
		'SubmitForm'			: 'SubmitForm',
		'PlaceAnOrder'			: 'PlaceAnOrder',
		'Search' 				: 'Search',
		'ViewContent' 			: 'ViewContent',

	};
	const utm_tags = [ 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'utm_creative_format', 'utm_creative_tactic', 'utm_id' ];
	const click_id = [ 'gclid', 'fbclid', 'ttclid' ];

	/*** Public Methods */

	/* -----------------------------------
	*
	* identify touch / desktop device
	*
	* -------------------------------- */
	function is_touch_enabled()
	{
		return( 'ontouchstart' in window ) ||
				( navigator.maxTouchPoints > 0 ) ||
				( navigator.msMaxTouchPoints > 0 );
	}

	/* -----------------------------------
	 *
	 * get tracking arguments
	 *
	 * -------------------------------- */
	function getArguments( args )
	{
		if( _config.internal_debug ) console.debug( '[MyAgilePixelTikTokCapi] -> getArguments, args=' + args );

		const type_key = typeof args[0] === 'string' ? args[0] : args[0][0] || 'PageView';
		const props = typeof args[0][1] === 'object' ? args[0][1] : args[1] || {};

		let type = eventKeys[type_key];

		/*
		console.debug( type_key );
		console.debug( type );
		console.debug( props );
		*/

		return [ { type: type, props } ];
	}


	/* -----------------------------------
	 *
	 * generate random id
	 *
	 * -------------------------------- */
	function getRandomId( length = 16 )
	{
		if( _config.internal_debug ) console.debug( '[MyAgilePixelTikTokCapi] -> getRandomId ');

		const randomId = `${Math.floor(Math.random() * 1e16)}`;

		length = length > 16 ? 16 : length;

		return randomId.padStart(length, '0').substring(-1, length);
	}

	/* -----------------------------------
	 *
	 * get and set client id
	 *
	 * -------------------------------- */
	function getClientId()
	{
		if( _config.internal_debug ) console.debug( '[MyAgilePixelTikTokCapi] -> getClientId' );

		let cookieDayDuration = 180;

		if( _config.enable_session_life_cookie_duration )
		{
			cookieDayDuration = 0;
		}

		const clientId = getRandomId();
		const storedValue = MAPX_Cookie.read( clientKey ) || null;

		if ( !storedValue )
		{
			MAPX_Cookie.setDayDuration( clientKey, clientId, cookieDayDuration );

			return clientId;
		}

		return storedValue;
	}


	/* -----------------------------------
	 *
	 * get document data
	 *
	 * -------------------------------- */
	function getDocument()
	{
		if( _config.internal_debug ) console.debug( '[MyAgilePixelTikTokCapi] -> getDocument' );

		let { hostname, origin, pathname, search } = document.location;
		let title = document.title;
		let referrer = '';

		var cleaned_search = search;

		//utm tags
		if( _config.remove_utm_tag == true )
		{
			var search_params = new URLSearchParams( cleaned_search );

			utm_tags.forEach( element => {
				search_params.delete( element );
			});

			cleaned_search = search_params.toString();
		}

		//click id
		if( _config.remove_click_id == true )
		{
			var search_params = new URLSearchParams( cleaned_search );

			click_id.forEach( element => {
				search_params.delete( element );
			});

			cleaned_search = search_params.toString();
		}

		let location = origin + pathname + cleaned_search;

		return { location: location,
			hostname,
			pathname,
			referrer,
			title
		};
	}

	/* -----------------------------------
	 *
	 * track event
	 *
	 * -------------------------------- */
	function track(...args )
	{
		if( _config.internal_debug ) console.debug( '[MyAgilePixelTikTokCapi] -> track, args=' + args );

		if( !initted )
		{
			if( _config.internal_debug ) console.debug( '[MyAgilePixelTikTokCapi] -> not initted' );

			return;
		}

		const [{ type, props }] = getArguments( args );

		const { location, referrer, title } = getDocument();

		let userAgent = '';

		if( _config.remove_user_agent == false )
		{
			userAgent = window.navigator.userAgent || '';
		}

		let __IS_TOUCH = null;

		if( _config.send_desktop_mobile_user_agent )
		{
			__IS_TOUCH = is_touch_enabled();
		}

		//send data
		let queryParams = {
			'event' 		: type,
			'external_id' 	: getClientId(),
			'event_id'		: getRandomId(),
			'url' 			: location,
			'user_agent'  	: userAgent,
			'__IS_TOUCH'	: __IS_TOUCH,
			'custom_data'	: props,
		};

		//console.debug( queryParams );

		let payload = {
			action 		: 	'mpx_send_data',
			realm 		: 	_config.realm,
			sec_token 	: 	_config.sec_token,
			data 		: 	JSON.stringify( queryParams )
		};

		sendPayLoad( payload );
	}

	/* -----------------------------------
	 *
	 * send payload function
	 *
	 * -------------------------------- */
	function sendPayLoad( payload )
	{
		let body =  new URLSearchParams( payload );

		fetch( _config.ajax_url, {
			method: "POST",
			credentials: 'same-origin',
			headers: new Headers({
				'Content-Type': 'application/x-www-form-urlencoded',
				'Cache-Control': 'no-cache',
			}),
			body: body
		}).then( function ( res ){

			if( res.ok )
			{
				return res.json();
			}
			else
			{
				if( _config.internal_debug )
				{
					console.groupCollapsed( '[MyAgilePixelTikTokCapi] -> error on sending data : ' );
					console.table( res );
					console.groupEnd();
				}

				if( _config.internal_debug && typeof MyAgilePixel.showNotificationBar === "function" )
				{
					MyAgilePixel.showNotificationBar( 'Tiktok error while sending data. Please verify your configuration.', 2 );
				}

				return null;
			}

		}).then( function ( data ) {

			if( data )
			{
				if( _config.internal_debug )
				{
					console.groupCollapsed( '[MyAgilePixelTikTokCapi] -> success sending data : ' );
					console.table( data );
					console.groupEnd();
				}

				console.debug( data.output_message );

				if( data.output_message && _config.internal_debug && typeof MyAgilePixel.showNotificationBar === "function" )
				{
					MyAgilePixel.showNotificationBar( data.output_message, 1 );
				}

				if( data?.detected_error && data?.error_description && _config.internal_debug && typeof MyAgilePixel.showNotificationBar === "function" )
				{
					MyAgilePixel.showNotificationBar( data.error_description, 2 );
				}

			}
		});
	}


	/* -----------------------------------
	 *
	 * init page view
	 *
	 * -------------------------------- */
	function initPageView()
	{
		if( _config.internal_debug ) console.debug( '[MyAgilePixelTikTokCapi] -> initPageView' );

		if( initted )
		{
			track( 'PageView' );
		}
	}

	/* -----------------------------------
	 *
	 * constructor
	 *
	 * -------------------------------- */
	function MyAgilePixelTikTokCapi( config = null )
	{
		if( config?.tiktokapi_enabled )
		{
			if( config )
			{
				if( typeof config.tiktokapi.internal_debug !== 'undefined' )
				{
					_config.internal_debug = config.tiktokapi.internal_debug;
				}

				if( typeof config.tiktokapi.remove_click_id !== 'undefined' )
				{
					_config.remove_click_id = config.tiktokapi.remove_click_id;
				}

				if( typeof config.tiktokapi.remove_utm_tag !== 'undefined')
				{
					_config.remove_utm_tag = config.tiktokapi.remove_utm_tag;
				}

				if( typeof config.tiktokapi.remove_user_agent !== 'undefined' )
				{
					_config.remove_user_agent = config.tiktokapi.remove_user_agent;
				}

				if( typeof config.tiktokapi.send_desktop_mobile_user_agent !== 'undefined' )
				{
					_config.send_desktop_mobile_user_agent = config.tiktokapi.send_desktop_mobile_user_agent;
				}

				if( typeof config.tiktokapi.enable_session_life_cookie_duration !== 'undefined' )
				{
					_config.enable_session_life_cookie_duration = config.tiktokapi.enable_session_life_cookie_duration;
				}

				if( typeof config.ajax_url !== 'undefined' )
				{
					_config.ajax_url = config.ajax_url;
				}

				if( typeof config.sec_token !== 'undefined' )
				{
					_config.sec_token = config.sec_token;
				}
			}

			let { hostname, origin, pathname, search } = document.location;
			let search_params = new URLSearchParams( search );
			if( search_params.has( 'myagilepixel_debug' ) )
			{
				_config.internal_debug = true;
			}

			if( _config.internal_debug )
			{
				console.groupCollapsed( '[MyAgilePixelTikTokCapi] -> config: ');
				console.table( _config );
				console.groupEnd();
			}

			//global object replacement
			var ttq = {
				track: function (...args) {
					return track( args );
				}
			};

			//global object expose
			window.ttq = ttq;
			window.myagilepixel_ttq = ttq;

			initted = true;

			initPageView();

		}
	}

	window.MyAgilePixelTikTokCapi = MyAgilePixelTikTokCapi;

}());

/**
replacement for jQuery on method
*/
function my_agile_on( eventType, selector, callback )
{
  document.addEventListener(eventType, function ( event ) {
	let target = event.target;
	let elements = document.querySelectorAll( selector );

	while (target && target !== document) {
		for (var i = 0; i < elements.length; i++) {
			if (target === elements[i]) {
				callback.call(target, event);
				return;
			}
		}
		target = target.parentElement;
	}
  });
}

//load event binding
window.addEventListener('load', function() {

	//facebook event configuration tool warning
	if( typeof window.FacebookIWL !== 'undefined' && typeof MyAgilePixel.showNotificationBar === "function" )
	{
		MyAgilePixel.showNotificationBar( 'You can\'t use Facebook Event Configuration Tool in a Server Side environment', 2 );
	}
});



