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
            contentType = jankx_xhr.getResponseHeader("Content-Type");
            if (contentType.indexOf('application/json') > -1) {
                jankx_xhr.responseJSON = JSON.parse(jankx_xhr.response);
            }

            options.complete(jankx_xhr);
        }
    }

    jankx_xhr.open(method, url);

    method.toUpperCase() === 'GET'
        ? jankx_xhr.send()
        : jankx_xhr.send(queryString);

    return jankx_xhr;
}
window.ajax = jankx_ajax;

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

HTMLElement.prototype.appendHTML = function(html) {
    this.innerHTML += html;
}

HTMLElement.prototype.html = function(html) {
    this.innerHTML = html;
}

HTMLElement.prototype.removeClass = function(clsName) {
    // For modern browers
    if (this.classList) {
        this.classList.remove(clsName);
    } else {
        // This case for old IE browser
        var classes = this.className.split(" ");
        var i = classes.indexOf(clsName);
        if (i >= 0) {
            classes.splice(i, 1);
            this.className = classes.join(" ");
        }
    }
}

HTMLElement.prototype.addClass = function(clsName) {
    // For modern browers
    if (this.classList) {
        // Add class when the class is not exists
        if (!this.classList.contains(clsName)) {
            this.classList.add(clsName);
        }
    } else {
        // This case for old IE browser
        var classes = this.className.split(" ");
        var i = classes.indexOf(clsName);
        if (i < 0 ) {
            classes.push(clsName);
            this.className = classes.join(" ");
        }
    }
}