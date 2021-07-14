function jankx_find_element_parent(element, selector) {
    var e = element, s = selector;

    const parent = e.matches(s);
    if (parent) {
        return e;
    }

    if (e.parentElement) {
        return jankx_find_element_parent(e.parentElement, s);
    }
}

HTMLElement.prototype.findParent = function(selector) {
    return jankx_find_element_parent(this, selector);
}

HTMLElement.prototype.parent = function() {
    return this.parentElement;
}

HTMLElement.prototype.find = function(selector) {
    return this.querySelector(selector);
}

function jankx_create_form_data(body) {
    if (body instanceof FormData) {
        return body;
    }
    formData = new FormData();
    dataKeys = Object.keys(body);
    for (i = 0; i < dataKeys.length; i++) {
        dataKey = dataKeys[i];
        formData.append(dataKey, body[dataKey]);
    }

    return formData;
}

function jankx_ajax(url, method = 'GET', body = {}, options = {}, headers = {}) {
    var jankx_xhr = window.XMLHttpRequest
        ? new XMLHttpRequest() :
        new ActiveXObject("Microsoft.XMLHTTP");

    options = Object.assign({
        beforeSend: function() {},
        complete: function() {}
    }, options);

    queryString = new URLSearchParams(jankx_create_form_data(body));

    if (method.toUpperCase() === 'GET') {
        url += '?' + queryString;
    }

    header_keys = Object.keys(headers);
    if ( header_keys.length > 0) {
        for(i = 0; i < header_keys.length; i++) {
            header = header_keys[i];
            jankx_xhr.setRequestHeader(header, header_keys[header]);
        }
    }

    jankx_xhr.addEventListener('loadstart', options.beforeSend);
    jankx_xhr.onreadystatechange = function () {
        // In local files, status is 0 upon success in Mozilla Firefox
        if(jankx_xhr.readyState === XMLHttpRequest.DONE) {
            options.complete();
        }
    }

    jankx_xhr.open(method, url);

    method.toUpperCase() === 'GET'
        ? jankx_xhr.send()
        : jankx_xhr.send(queryString);

    return jankx_xhr;
}
window.ajax = jankx_ajax;
