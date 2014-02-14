var GO = {
	baseUrl: "/trunk/www/",
	securityToken: "token",
	url: function(relativeUrl, params) {
		if (!relativeUrl && !params)
			return GO.baseUrl;

		var url = GO.baseUrl + "index.php?r=" + relativeUrl + "&security_token=" + GO.securityToken;
		if (params) {
			for (var name in params) {
				url += "&" + name + "=" + encodeURIComponent(params[name]);
			}
		}
		return url;
	}

}