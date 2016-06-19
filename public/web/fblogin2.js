/**
 * Functions for login page
 */
window.onload = function(){
    // Login by Facebook
    $('#fbLogin').on('click', function(){
        // fix for chrome on iOS
        if (navigator.userAgent.match('CriOS')) {
            window.open('https://www.facebook.com/dialog/oauth?client_id=' + facebook_app_id + '&redirect_uri=' + document.location.href + '&scope=email,public_profile&response_type=token', '', null);
            return false;
        }        
        FB.login(function (response) {
            if (response.authResponse) {
                showLoading();
				/*
                var accessToken = response.authResponse.accessToken; 
				FB.api('/me/family', function(response) {
					console.log(response)					
				});
				return false;
				
				FB.api('/me/friends', function(response) {
					console.log(response)
					if (!response || response.error) {						
						console.log(read_err_text);						
					} else {
						var read_ok_text = response.summary.total_count;
						console.log(read_ok_text);
					}
				});
				return false;				
				FB.api(
					"/me/taggable_friends?fields=name,id&limit=1000",
					function (response) {
						if (response && !response.error) {							
							console.log(response)
							for(var i=0; i<response.data.length; i++){
								var data = response.data;
								friendsIDarray.push(data[i].id);    
							}
							//user_friend_list = friendsIDarray.join();
							//console.log(user_friend_list);
						}						
					}
				);
				return false;
				*/
                var fields = 'fields=id,email,birthday,first_name,gender,last_name,link,locale,name,timezone,updated_time,verified';            
                FB.api('/me?' + fields, function (response) {						
                    var url = '/fblogin?backurl='+location.href+'&accessToken='+accessToken;
                    $.ajax({
                        cache: false,
                        async: true,
                        data: response,
						type: 'post',
                        url: url,
                        success: function (json) {
                            var result = jQuery.parseJSON(json);
                            if (result.error !== 0) {
                                alert(result.message);
                            } else {
                                location.href = result.backUrl;
                            }
                        }
                    });
                });
            } else {
                // User cancelled
            }
        }, {scope: 'email,user_likes,user_friends,user_photos,public_profile,user_tagged_places,user_posts,manage_pages,publish_pages,publish_actions'});
        return false;
    });
};
