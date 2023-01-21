var FuxHTTP = {
    RESOLVE_DATA: 1,
    RESOLVE_MESSAGE: 2,
    RESOLVE_RESPONSE: 3,
    REJECT_DATA: 4,
    REJECT_MESSAGE: 5,
    REJECT_RESPONSE: 6,
    STATUS_SUCCESS: 'OK',
    getCookie: function (cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    },
    doRequest: function (method, url, params, resolveMode, rejectMode) {
        var http = null;
        switch (method) {
            case 'GET':
                http = $.get;
                break;
            case 'POST':
                const xsrf_token = FuxHTTP.getCookie('XSRF-TOKEN');
                if (xsrf_token) params['_token'] = xsrf_token;
                http = $.post;
                break;
            default:
                http = $.get;
                break;
        }
        return new Promise(function (resolve, reject) {
            http(url, params, function (jsonResponse) {
                if (jsonResponse.status === FuxHTTP.STATUS_SUCCESS) {
                    switch (resolveMode) {
                        case FuxHTTP.RESOLVE_DATA:
                            resolve(jsonResponse.data);
                            break;
                        case FuxHTTP.RESOLVE_MESSAGE:
                            resolve(jsonResponse.message);
                            break;
                        case FuxHTTP.RESOLVE_RESPONSE:
                            resolve(jsonResponse);
                            break;
                    }
                } else {
                    switch (rejectMode) {
                        case FuxHTTP.REJECT_DATA:
                            reject(jsonResponse.data);
                            break;
                        case FuxHTTP.REJECT_MESSAGE:
                            reject(jsonResponse.message);
                            break;
                        case FuxHTTP.REJECT_RESPONSE:
                            reject(jsonResponse);
                            break;
                    }
                }
            });
        });
    },
    apiGetRequestWithPromise: function (url, params, resolveMode, rejectMode) {
        return FuxHTTP.doRequest('GET', url, params, resolveMode, rejectMode);
    },
    apiPostRequestWithPromise: function (url, params, resolveMode, rejectMode) {
        return FuxHTTP.doRequest('POST', url, params, resolveMode, rejectMode);
    },
    get: function (url, params, resolveMode, rejectMode) {
        return FuxHTTP.doRequest('GET', url, params, resolveMode, rejectMode);
    },
    post: function (url, params, resolveMode, rejectMode) {
        return FuxHTTP.doRequest('POST', url, params, resolveMode, rejectMode);
    }
};