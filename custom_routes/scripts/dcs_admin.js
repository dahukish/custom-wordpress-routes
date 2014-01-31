(function($, window, document) {

$(document).ready(function(){
	$('[data-action="save-location"]').live('click', function(e){

	var $parent = $(this).parents('tr');

	$inputs = $parent.find(':input');

	var form_data = {id:$parent.data('id')};

	$inputs.each(function(index, element){
		var regex_dump = /(\w+)_\d+/.exec($(element).attr('name'));
		var element_name = regex_dump[1];
		form_data[element_name] = $(element).val();
	});

	var href = $(this).attr('href');

	var params = form_data;

	var ajax_cb = function(ajax_obj, data)
	{
		console.log(ajax_obj);
		alert(data.msg);
	};

	var ajax_fail_cb = function(ajax_obj, data)
	{
		console.log(ajax_obj);
		alert(data.msg);
		return false;
	};

	var ajax_obj = new AjaxObj(href, params, 'POST', 'json', ajax_cb, ajax_fail_cb);

	ajax_obj.execute();
  	e.preventDefault();
  });

$('[data-action="add-location"]').live('click', function(e){

	var href = $(this).attr('href');
	var form_data = {itemCount:$('[data-scope="location_item"]').length};
	var params = form_data;

	var ajax_cb = function(ajax_obj, data)
	{
		var template = data.msg;
		var table = $('[data-scope="location-table"]');
		table.append(template);
	};

	var ajax_fail_cb = function(ajax_obj, data)
	{
		console.log('error');
		console.log(data);
		return false;
	};
	var ajax_obj = new AjaxObj(href, params, 'POST', 'json', ajax_cb, ajax_fail_cb);
	ajax_obj.execute();
  	e.preventDefault();
  });

});


$('[data-action="remove-location"]').live('click', function(e) {
	//
	//
	//
	//
	// THIS IS PRETTY MUCH NOT WORKING YET
	//
	//
	//
	// DONT FORGET
	//
	//
	//
	$(this).parents('tr').remove();
	e.preventDefault();
});



function AjaxObj(path,params,action_type,return_type,s_cb_func,e_cb_func,alert)
{
	this.path = path;
	this.params = params;
	this.action_type = (typeof action_type !== 'undefined')? action_type : 'GET';
	this.return_type = (typeof return_type !== 'undefined')? return_type : 'json';
	this.s_cb_func = (typeof s_cb_func !== 'undefined')? s_cb_func : null;
	this.e_cb_func = (typeof e_cb_func !== 'undefined')? e_cb_func : null;
	this.alert = (typeof alert !== 'undefined')? alert : false;
	this.success = false;
	this.return_data = null;
};

AjaxObj.prototype.execute = function(path,params,action_type,return_type,s_cb_func,e_cb_func,queue)
{
	//the setup
	var _path = (typeof path !== 'undefined' || path != null)? path : this.path;
	var _params = (typeof params !== 'undefined' || params != null)? params : this.params;
	var _action_type = (typeof action_type !== 'undefined' || action_type != null)? action_type : this.action_type;
	var _return_type = (typeof return_type !== 'undefined' || return_type != null)? return_type : this.return_type;
	var _s_cb_func = (typeof s_cb_func !== 'undefined' || s_cb_func != null)? s_cb_func : this.s_cb_func;
	var _e_cb_func = (typeof e_cb_func !== 'undefined' || e_cb_func != null)? e_cb_func : this.e_cb_func;

	//ref to use within ajax call back function
	var that = this;

	var deferreds = [];

	console.log(_path);

	deferreds.push(($.ajax({
	  type: _action_type,
	  url: _path,
	  data: _params,
	  dataType: _return_type
	})));

	// TODO: add queing of promises -SH
	// if(queue)
	// {
	// 	$.each(queue, function(i, item)
	// 	{
 	//    deferreds.push(item);
 	//  });
	// }

	var successFunc = function(s_data)
  	{
		that.success = true;

		//quick fix for now until I find out why the response is not coming back as a json object -SH
		if((typeof s_data === 'string') && (_return_type === 'json')) s_data = $.parseJSON(s_data);

		if(! s_data.status)
		{
			if(_e_cb_func != null && (typeof _e_cb_func === 'function')) _e_cb_func(that,s_data);
			if(that.alert) alert(s_data.msg);
		}
		else
		{
			if(_s_cb_func != null && (typeof _s_cb_func === 'function')) _s_cb_func(that,s_data);
		}

		that.return_data = s_data;
	};

	var failFunc = function(jqXHR, textStatus, errorThrown)
	{
        alert("Status: " + textStatus); alert("Error: " + errorThrown);
    };

    // mind blown -SH
	$.when.apply(null,deferreds).then(successFunc,failFunc);

	return this;
};

AjaxObj.prototype.isSuccess = function()
{
	return this.success;
};

AjaxObj.prototype.parseResults = function(parseFunc)
{
	return parseFunc(this.s_data);
};


})(jQuery, window, document);
