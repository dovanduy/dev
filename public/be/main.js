function paging(url, div) {
    $.get(url, {}, function (result) {
        if (result) {
            $('#' + div).html(result);
        }
    });
}
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ')
            c = c.substring(1);
        if (c.indexOf(name) == 0)
            return c.substring(name.length, c.length);
    }
    return "";
}
function urldecode(str) {
    return decodeURIComponent((str + '').replace(/\+/g, '%20'));
}
function isValidDate(dateStr, format) {
    if (format == null) {
        format = "MDY";
    }
    format = format.toUpperCase();
    if (format.length != 3) {
        format = "MDY";
    }
    if ((format.indexOf("M") == -1) || (format.indexOf("D") == -1) || (format.indexOf("Y") == -1)) {
        format = "MDY";
    }
    if (format.substring(0, 1) == "Y") {
        var reg1 = /^\d{2}(\-|\/|\.)\d{1,2}\1\d{1,2}$/
        var reg2 = /^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/
    } else if (format.substring(1, 2) == "Y") {
        var reg1 = /^\d{1,2}(\-|\/|\.)\d{2}\1\d{1,2}$/
        var reg2 = /^\d{1,2}(\-|\/|\.)\d{4}\1\d{1,2}$/
    } else {
        var reg1 = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{2}$/
        var reg2 = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/
    }
    if ((reg1.test(dateStr) == false) && (reg2.test(dateStr) == false)) {
        return false;
    }
    var parts = dateStr.split(RegExp.$1);
    if (format.substring(0, 1) == "M") {
        var mm = parts[0];
    } else if (format.substring(1, 2) == "M") {
        var mm = parts[1];
    } else {
        var mm = parts[2];
    }
    if (format.substring(0, 1) == "D") {
        var dd = parts[0];
    } else if (format.substring(1, 2) == "D") {
        var dd = parts[1];
    } else {
        var dd = parts[2];
    }
    if (format.substring(0, 1) == "Y") {
        var yy = parts[0];
    } else if (format.substring(1, 2) == "Y") {
        var yy = parts[1];
    } else {
        var yy = parts[2];
    }
    if (parseFloat(yy) <= 50) {
        yy = (parseFloat(yy) + 2000).toString();
    }
    if (parseFloat(yy) <= 99) {
        yy = (parseFloat(yy) + 1900).toString();
    }
    var dt = new Date(parseFloat(yy), parseFloat(mm) - 1, parseFloat(dd), 0, 0, 0, 0);
    if (parseFloat(dd) != dt.getDate()) {
        return false;
    }
    if (parseFloat(mm) - 1 != dt.getMonth()) {
        return false;
    }
    return true;
}
function isPhoneNumber(str) {
    var alphaExp = /^((\(\+?84\)[\-\.\s]?)|(\+?84[\-\.\s]?)|(0))((\d{3}[\-\.\s]?\d{6})|(\d{2}[\-\.\s]?\d{8}))$/;
    if (str.match(alphaExp)) {
        return true;
    }
    return false;
}
function isAlphabet(str) {
    var alphaExp = /^[a-zA-Z]+$/;
    if (str.match(alphaExp)) {
        return true;
    }
    return false;
}
function isAlphabetAndNumber(str) {
    var alphaExp = /^[a-zA-Z0-9_]+$/;
    if (str.match(alphaExp)) {
        return true;
    }
    return false;
}
function isUserName(str) {
    var alphaExp = /^[a-zA-Z0-9_.\-]+$/;
    if (str.match(alphaExp)) {
        return true;
    }
    return false;
}
function isEmail(email) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(email);
}
;
function isNumber(str) {
    var alphaExp = /^[0-9]+$/;
    if (str.match(alphaExp)) {
        return true;
    }
    return false;
}
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}
function strip_tags(str) {
    str = str.replace(/&nbsp;/g, '');
    str = jQuery.trim(str);
    allowed_tags = '';
    var key = '', allowed = false;
    var matches = [];
    var allowed_array = [];
    var allowed_tag = '';
    var i = 0;
    var k = '';
    var html = '';
    var replacer = function (search, replace, str) {
        return str.split(search).join(replace);
    };
    // Build allowes tags associative array
    if (allowed_tags) {
        allowed_array = allowed_tags.match(/([a-zA-Z]+)/gi);
    }
    str += '';
    // Match tags
    matches = str.match(/(<\/?[\S][^>]*>)/gi);
    // Go through all HTML tags
    for (key in matches) {
        if (isNaN(key)) {
// IE7 Hack
            continue;
        }
// Save HTML tag
        html = matches[key].toString();
        // Is tag not in allowed list? Remove from str!
        allowed = false;
        // Go through all allowed tags
        for (k in allowed_array) {
// Init
            allowed_tag = allowed_array[k];
            i = -1;
            if (i != 0) {
                i = html.toLowerCase().indexOf('<' + allowed_tag + '>');
            }
            if (i != 0) {
                i = html.toLowerCase().indexOf('<' + allowed_tag + ' ');
            }
            if (i != 0) {
                i = html.toLowerCase().indexOf('</' + allowed_tag);
            }

// Determine
            if (i == 0) {
                allowed = true;
                break;
            }
        }
        if (!allowed) {
            str = replacer(html, "", str); // Custom replace. No regexing
        }
    }
    return str;
}
function checkAll(strItemName, value) {
    var x = document.getElementsByName(strItemName);
    for (var i = 0; i < x.length; i++) {
        if (value == 1 && !x[i].disabled) {
            if (!x[i].checked)
                x[i].checked = 'checked';
        } else {
            if (x[i].checked)
                x[i].checked = '';
        }
    }
}
function getItemsChecked(strItemName, sep) {
    var x = document.getElementsByName(strItemName);
    var p = "";
    for (var i = 0; i < x.length; i++) {
        if (x[i].checked) {
            p += x[i].value + sep;
        }
    }
    var result = (p != '' ? p.substr(0, p.length - 1) : '');
    return result;
}

$(function () {
    if ($('.form-body .js-thumb').length > 0) {
        $('.form-body .js-thumb').lightBox();
    }
    var collapse = getCookie('collapse');
    if (collapse == '') {
        collapse = '1';
    }
    if (collapse == '0') {
        $('.search-body').show();
        if ($(".search-collapse").length > 0) {            
            $(".search-collapse").click();
        }
    }
    $(".search-collapse").click(function () {
        if (collapse == '1') {
            collapse = '0';
        } else {
            collapse = '1';
        }
        setCookie('collapse', collapse, 1);
        return true;
    });  
    initJsAjaxSubmit();
    initJsAjaxChange();
    initJsModalDialog();
    initJs();
});

initJsModalDialog = function () {
    if ($('.show-model').length > 0) {  
        $('.show-model').click(function(){
            var btn = this;
            var modelid = $(this).data('modelid');
            if ($(modelid).length > 0) {  
                $(modelid).modal('show');     
                var url = $(this).data('url');
                if (url !== undefined) {
                    $.ajax({
                        cache: false,
                        async: false,
                        type: 'post',
                        url: url,
                        data: {},
                        success: function (response) {
                            $(modelid + ' .modal-content').html(response);
                            initJsAjaxSubmit();
                        }
                    });
                }
                $(modelid + ' .form-group').each(function() {
                    var element = $(this).find('.form-control');
                    if (element) {
                        var value = $(btn).data(element.attr('id'));
                        if (value !== undefined) {
                            if (element.attr('id') == 'state_code' && $(btn).data('country_code')) {                               
                                localeState($(btn).data('country_code'));
                                setTimeout(function() { element.val(value); }, 1000);
                            } else if (element.attr('id') == 'city_code' && $(btn).data('state_code')) {                               
                                localeCity($(btn).data('state_code'));
                                setTimeout(function() { element.val(value); }, 1000);
                            } else {
                                element.val(value);
                            }                           
                        }
                    }
                });
                if ($(modelid).find('#_id').length > 0 && $(btn).data('_id') !== 'undefined') {
                    $(modelid).find('#_id').val($(btn).data('_id'));
                }
            }
            return false;
        });
    }
}

initJsAjaxSubmit = function (containerId) {
    var ajaxSubmitClass = '.ajax-submit';
    if (containerId !== undefined) {
        ajaxSubmitClass = containerId + ' ' + ajaxSubmitClass;
    }
    if ($(ajaxSubmitClass).length > 0) {
        $(ajaxSubmitClass).unbind("click");
        $(ajaxSubmitClass).click(function () {
            var url = window.location.href;
            if ($(this).data('url')) {
                url = $(this).data('url');
            }
            var ok = false;
            var btn = $(this);
            var frm = $(this).closest('form');   
            if (btn.data('confirmmessage')) {
                if (confirm(jQuery.trim(btn.data('confirmmessage'))) == false) {
                    return false;
                }
            }
            if (btn.data('beforesubmit')) {
                eval(btn.data('beforesubmit'));
            }
            $.ajax({
                cache: false,
                async: false,
                type: frm.attr('method'),
                url: url,
                data: frm.serialize(),
                beforeSend: function() {
                    if (btn.data('beforesend')) {
                        eval(btn.data('beforesend'));
                    }
                    return true;
                },
                success: function (response) {
                    //console.log(response);
                    var result = JSON.parse(response);                                  
                    if (result.status !== undefined && result.status == 'OK') {   
                        console.log('Data saved successfully');
                        // remove prev error
                        $('#' + frm.attr('id') + ' .form-group').each(function() {
                            if ($(this).find('ul')) {
                                $(this).find('ul').remove();
                            }                            
                        });
                        if (btn.data('callback')) {
                            eval(btn.data('callback'));
                            return false;                            
                        } else {
                            window.location.reload();
                        }
                    } else { 
                        // remove prev error
                        if ($('#modal-message').length > 0) {  
                            $('#modal-message').hide();
                        }
                        var error = JSON.parse(response);  
                        $('#' + frm.attr('id') + ' .form-group').each(function() {
                            if ($(this).find('ul')) {
                                $(this).find('ul').remove();
                            }                            
                        });
                        // add new error
                        $.each(error, function( index, value ) { 
                            var element = $(frm).find('.form-group-'+index);
                            if (element) {
                                if (!$('#'+index).hasClass('input-error')) {
                                    $('#'+index).removeClass('input-error');
                                }
                                if (element.find('ul')) {
                                    element.find('ul').remove();
                                }
                                element.append(value);
                            }
                        });
                    }                  
                }
            });   
            return ok;
        });              
    }
}

initJsAjaxChange = function (containerId) {
    var ajaxChangeClass = '.ajax-change';
    if (containerId !== undefined) {
        ajaxChangeClass = containerId + ' ' + ajaxChangeClass;
    }
    if ($(ajaxChangeClass).length > 0) {
        $(ajaxChangeClass).unbind("click");
        $(ajaxChangeClass).change(function () {
            var url = window.location.href;
            if ($(this).data('url')) {
                url = $(this).data('url');
            }
            var ok = false;
            var inp = $(this);
            var frm = $(this).closest('form');            
            if (inp.data('beforesubmit')) {
                eval(inp.data('beforesubmit'));
            }
            $.ajax({
                cache: false,
                async: false,
                type: frm.attr('method'),
                url: url,
                data: frm.serialize(),
                beforeSend: function() {
                    if (inp.data('beforesend')) {
                        eval(inp.data('beforesend'));                        
                    }
                    return true;
                },
                success: function (response) {
                    console.log(response);
                    var result = JSON.parse(response);                                  
                    if (result.status !== undefined && result.status == 'OK') {
                        console.log('Data saved successfully');                       
                        if (inp.data('callback')) {
                            eval(inp.data('callback'));
                            ok = true;
                        }
                    }                
                }
            });   
            return ok;
        });
    }
}

initJs = function () {
        
    $('.confirm').click(function(){
        if (confirm('Are you sure?') === false) {
            return false;
        }
    });
    
	$(".number").keypress(function(evt) {        
        return isNumberKey(evt);        
    });
	
    $(".td_sort input").attr('readonly', true);
    
    $('.toggle').show();
    $(".toggle-event").change(function(){
        return toggleEvent(this);
    });
    
    toggleEvent = function($this) {
        var frm = $($this).closest('form');   
        var data = {
            _id: $($this).val(),            
            field: $($this).data('field'),            
            value: ($($this).prop('checked') ? 1 : 0)
        }
        $.ajax({
           type: frm.attr('method'),
           url: window.location.href,
           data: data,
           success: function (response) {
                if (response == 'OK') {
                    console.log('Data saved successfully');   
                    return true;
                }
                var result = JSON.parse(response);
                console.log(result);                  
                if (result.status != 'undefined' && result.status == 'OK') {
                    console.log('Data saved successfully');                       
                    if ($($this).data('callback')) {
                        eval($($this).data('callback'));
                    }
                }
            }
        });
    }
    
    if ($('.box-body .js-thumb').length > 0) {
        $('.box-body .js-thumb').lightBox();
    }
    
    $('.box-body .bg-no-image').click(function () {
        return false;
    });
    
    $(".image input[type=file]").change(function () {
        var previewDiv = $(this).parent().find('div').find('a');
        var file = this.files[0];
        var imageType = /image.*/;
        if (file.type.match(imageType)) {
            var reader = new FileReader();
            reader.onload = function(e) {
                previewDiv.html('');                
                var img = new Image();
                img.src = reader.result;
                previewDiv.html(img);
            }
            reader.readAsDataURL(file);
        } else {
            previewDiv.html('File not supported!');
        }
    });
    
    // sortable
    if ($('.table tbody .td_sort').length > 0) {         
        var fixHelperModified = function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index) {
                $(this).width($originals.eq(index).width())
            });
            return $helper;
        };
        $(".table tbody").sortable({
            helper: fixHelperModified,
            stop: function(event, ui) {
                $(".table tbody tr").each(function() {
                    count = $(this).parent().children().index($(this)) + 1;
                    $(this).find('.td_sort input').val(count);
                });
                var frm = $('.box-body-table').parent();
                $.ajax({
                   type: frm.attr('method'),
                   url: window.location.href,
                   data: frm.serialize(),
                   success: function (response) {
                       if (response == 'OK') {
                           console.log('Data saved successfully');
                       }
                   }
                });
            }
        }).disableSelection();        
    }
    
    if ($('.table thead .sortable').length > 0) {  
        $('.table thead .sortable').click(function(){
            var url = $(this).data('sort');
            location.href = url;
        });
    }
    
    if ($('.datetimepicker').length > 0) {  
        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            showTodayButton:true,
            showClear:true,
            showClose:true,
            locale:'en',
            stepping:1
        }); 
    }
    
    if ($('.datepicker').length > 0) {  
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',                           
            clearBtn: true,
            todayHighlight: true
        }); 
    }
    
    if ($('.price').length > 0) {  
        $('.price').autoNumeric('init');
    }
    
    if ($('#productorders_add #user_id').length > 0) {  
        
    }
      
    if ($('.image-thumb a').length > 0 && $('.image-view img').length > 0) {  
        $('.image-thumb a').click(function(){
            var image_url = $(this).attr('href');
            $('.image-view img').attr('src', image_url);
            /*
            $('.image-view img').elevateZoom({
                zoomType: "inner",
                cursor: "crosshair",
                zoomWindowFadeIn: 500,
                zoomWindowFadeOut: 750
           });
            */
            return false;
        });
        /*
        $('.image-view img').elevateZoom({
            zoomType: "inner",
            cursor: "crosshair",
            zoomWindowFadeIn: 500,
            zoomWindowFadeOut: 750
       }); 
        */
    }
    

    /*
    var last_sub_category_id = JSON.parse($('#last_sub_category_id').val());  
    $.each(last_sub_category_id, function( index, value ) {
        //alert( index + ": " + value );
    });
    $("#category_id > option").each(function() {
        if (last_sub_category_id.indexOf(this.value) < 0) {
            $(this).attr('disabled', 'disabled');
        }         
    });
    $('#category_id').select2({
        debug: true,
        templateResult: function (result) {
            return result.text;
        },
        templateSelection: function (selection) {
            var re = new RegExp("\\|----", "g");
            var text = selection.text.replace(re, '');
            return text;
        }
    });    
    */
	//Delete button in table rows
    /*
	$('table').on('click','.btn-delete', function() {
		tableID = '#' + $(this).closest('table').attr('id');
		r = confirm('Delete this item?');
		if(r) {
			$(this).closest('tr').remove();
			renumber_table(tableID);
			}
	});*/
    
}

showMessage = function(msg, type) { // type: error
    if (msg === undefined) {
        msg = 'Data saved successfully';
    } 
    if (msg === undefined) {
        type = '';
    }
    if (msg !== undefined) {
        showNotification({
            message: msg,
            autoClose: true,
            duration: 5,
            type: type
        });
    }
}

concat = function (str1, str2) {
    return (str1 + str2);
}
autocomplete = function (id, url, func) {
    $("#" + id).autocomplete({
        source: function (request, response) {
            $.ajax({
                url: url,
                dataType: "jsonp",
                data: {
                    q: request.term
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        minLength: 1,
        select: function (event, ui) {
            func(ui.item);
        },
        open: function () {
            $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
        },
        close: function () {
            $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
        }
    });
}
localeState = function(country_code) {
    $.ajax({
        url: '/ajax/localestate',
        type: "POST",
        data: {
            country_code: country_code
        },
        success: function (response) {
            $('#state_code').html(response);
        }
    });
    return false;
};
localeCity = function(state_code) {
    $.ajax({
        url: '/ajax/localecities',
        type: "POST",
        data: {
            state_code: state_code,
            country_code: typeof $('#country_code').val() != 'undefined' ? $('#country_code').val() : ''
        },
        success: function (response) {
            $('#city_code').html(response);
        }
    });
    return false;
};
localeAddress = function(userId) {
    $.ajax({
        url: '/ajax/localeaddress',
        type: "POST",
        data: {
            user_id: userId
        },
        success: function (response) {
            $('#address_id').html(response);
        }
    });
    return false;
};

saveNewOrder = function() {
    var ok = true;
    if ($('#cartForm').length > 0) {
        var cartForm = $('#cartForm');
        $.ajax({
            cache: false,
            async: false,
            type: cartForm.attr('method'),
            url: window.location.href,
            data: cartForm.serialize(),
            success: function (response) {
                var result = JSON.parse(response);         
                if (result.status != 'undefined' && result.status == 'OK') {
                    ok = true;
                } else {
                    ok = false;
                }                               
            }
        });    
    } 
    return ok;
}

loadCart = function(openDropdown) {
    var ok = false;
    $.ajax({
        cache: false,
        async: false,
        type: 'post',
        url: '/carts',
        data: {},
        success: function (response) {
            $('#dropdown-cart').html(response);             
            if (openDropdown !== undefined 
                && !$("#dropdown-cart").hasClass('open')
            ) {                
                $("#dropdown-cart .dropdown-toggle").click();
            }            
            ok = true;			
        }
    });  
    if (ok) {
		initJs();
        initJsAjaxSubmit('#dropdown-cart');
    }
}

db_int = function(str) {
    str = str.replace(/,/g, "");    
    str = str.replace(/\./g, "");
    return parseInt(str);     
}

db_float = function(str) { 
    str = str.replace(/,/g, ""); 
    str = str.replace(/\./g, "");
    return parseInt(str);    
}

function money_format(number) {
    return number_format(number, 0, '.', ',');
}

function number_format(number, decimals, dec_point, thousands_sep) {
  //  discuss at: http://phpjs.org/functions/number_format/
  // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: davook
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Theriault
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: Michael White (http://getsprink.com)
  // bugfixed by: Benjamin Lupton
  // bugfixed by: Allan Jensen (http://www.winternet.no)
  // bugfixed by: Howard Yeend
  // bugfixed by: Diogo Resende
  // bugfixed by: Rival
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  //  revised by: Luke Smith (http://lucassmith.name)
  //    input by: Kheang Hok Chin (http://www.distantia.ca/)
  //    input by: Jay Klehr
  //    input by: Amir Habibi (http://www.residence-mixte.com/)
  //    input by: Amirouche
  //   example 1: number_format(1234.56);
  //   returns 1: '1,235'
  //   example 2: number_format(1234.56, 2, ',', ' ');
  //   returns 2: '1 234,56'
  //   example 3: number_format(1234.5678, 2, '.', '');
  //   returns 3: '1234.57'
  //   example 4: number_format(67, 2, ',', '.');
  //   returns 4: '67,00'
  //   example 5: number_format(1000);
  //   returns 5: '1,000'
  //   example 6: number_format(67.311, 2);
  //   returns 6: '67.31'
  //   example 7: number_format(1000.55, 1);
  //   returns 7: '1,000.6'
  //   example 8: number_format(67000, 5, ',', '.');
  //   returns 8: '67.000,00000'
  //   example 9: number_format(0.9, 0);
  //   returns 9: '1'
  //  example 10: number_format('1.20', 2);
  //  returns 10: '1.20'
  //  example 11: number_format('1.20', 4);
  //  returns 11: '1.2000'
  //  example 12: number_format('1.2000', 3);
  //  returns 12: '1.200'
  //  example 13: number_format('1 000,50', 2, '.', ' ');
  //  returns 13: '100 050.00'
  //  example 14: number_format(1e-8, 8, '.', '');
  //  returns 14: '0.00000001'

    number = (number + '')
            .replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                        .toFixed(prec);
            };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
            .split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '')
            .length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1)
                .join('0');
    }
    return s.join(dec);
}

saveOrderDetail = function($this) {
    var id = $($this).data('product_id');
    var quantity = db_int($($this).val());    
    $('.td_price .price').each(function() {
        var product_id = $(this).data('product_id');
        if (product_id == id) {
            var money = money_format(db_float($(this).val()) * quantity);
            $('.total-money-'+id).html(money);
        }
    });
    //sumTotalMoney();
}

sumTotalMoney  = function() {
    var total_money = 0;
    $('.td_total_money span').each(function() {
        total_money += db_float($(this).html());
    });
    $('#row-order-detail .total-money span').html(money_format(total_money));
}