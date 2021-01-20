document.addEventListener('DOMContentLoaded', function(){
    let urlParams = new URLSearchParams(window.location.search);

    if(urlParams.has('v')){
        search.selector.querySelector('input').value = 'https://www.youtube.com/watch?v=' + urlParams.get('v');
        search.selector.querySelector('button').click();
    }
    else if(urlParams.has('q')){
        search.selector.querySelector('input').value = urlParams.get('q');
        search.selector.querySelector('button').click();
    }
});

/**
 * loader
 * @type {{hide: loader.hide, show: loader.show}}
 */
var loader = {
    show: function (el) {
        if ( el.querySelector('.icon') )
            el.querySelector('.icon').classList.add('hide');

        if ( el.querySelector('.text') )
            el.querySelector('.text').classList.add('hide');

        if ( el.querySelector('.loader') )
            el.querySelector('.loader').classList.add('show');
    },
    hide: function (el) {
        if ( el.querySelector('.icon') )
            el.querySelector('.icon').classList.remove('hide');

        if ( el.querySelector('.text') )
            el.querySelector('.text').classList.remove('hide');

        if ( el.querySelector('.loader') )
            el.querySelector('.loader').classList.remove('show');
    }
}


/**
 * msg
 * @type {{hide: msg.hide, selector: HTMLElement, error: msg.error}}
 */
var msg = {
    selector: document.getElementById('message'),
    error: function (messages) {
        if ( typeof messages == "string" )
        {
            console.log('string')
            msg.selector.innerHTML = '<div class="error">' + messages + '</div>';
        }
        else if ( typeof messages == "object" )
        {
            var h = '';

            Object.keys(messages).map(function(objectKey) {
                h += '<div class="error">' + messages[objectKey] + '</div>';
            });

            msg.selector.innerHTML = h;
        }

        msg.selector.classList.remove('hide');
    },
    hide: function () {
        msg.selector.classList.add('hide');
    }
}


/**
 * search
 * @type {{valid: search.valid, init: search.init, submit: search.submit, before: search.before, query: null, selector: Element, finish: search.finish, error: (function(*=): boolean), validate: search.validate}}
 */
var search = {
    selector: document.querySelectorAll('[data-form="search"]')[0],
    query: null,
    init: function () {
        if ( search.selector ) {
            search.selector.addEventListener('submit', function (e) {
                search.submit(e);
            });
        }
    },
    submit: function (e) {
        e.preventDefault();

        search.query = search.selector.getElementsByTagName('input')[0].value;

        search.validate();
    },
    validate: function () {
        if ( !search.query ) {
            return search.error('Keyword cant not be empty');
        }

        if ( search.query.length < 3 ) {
            return search.error('Keyword min 3 symbol');
        }

        search.valid();

        request.init(
            '/search/',
            'POST',
            {term: search.query},
            function () {search.before()},
            function () {search.finish()},
            {"Content-Type" : "application/x-www-form-urlencoded; charset=UTF-8", "X-Requested-With" : "XMLHttpRequest"}
        );
    },
    before: function() {
        this.selector.querySelector('input').setAttribute("disabled", true);
        this.selector.querySelector('button').setAttribute("disabled", true);

        loader.show(this.selector.querySelector('button'));
    },
    finish: function() {
        this.selector.querySelector('input').removeAttribute("disabled");
        this.selector.querySelector('button').removeAttribute("disabled");

        loader.hide(this.selector.querySelector('button'));

        document.querySelector('.content').innerHTML = request.response.data.content;
    },
    error: function (message) {
        msg.error(message);

        search.selector.classList.add('error');

        return false;
    },
    valid: function () {
        msg.hide();
        search.selector.classList.remove('error');
    },
}


/**
 * request
 * @type {{init: request.init, setHeaders: request.setHeaders, xhr: null, before: request.before, response: null, finish: request.finish, type: null, params: null, setType: request.setType, url: null, setUrl: request.setUrl, setParams: request.setParams}}
 */
var request = {
    xhr: null,
    url: null,
    type: null,
    params: null,
    response: null,
    init: function (url, type, params, callbackBefore = null, callbackFinish = null, headers = null) {
        this.setUrl(url);
        this.setType(type);
        this.setParams(params);

        // init request
        request.xhr = new XMLHttpRequest();

        // before request
        this.before(callbackBefore);

        // on success request
        request.xhr.onreadystatechange = function() {
            if(request.xhr.readyState === 4 && request.xhr.status === 200) {
                request.response = JSON.parse(request.xhr.responseText);

                request.finish(callbackFinish);
            }
        };

        // open request
        request.xhr.open(this.type, this.url);

        // set headers
        this.setHeaders(headers);

        //send request
        request.xhr.send(this.params);
    },
    setHeaders: function(headers) {
        if ( typeof headers == "object" )
        {
            Object.keys(headers).map(function(key) {
                request.xhr.setRequestHeader(key, headers[key]);
            });
        }
    },
    before: function (callbackBefore) {
        if ( callbackBefore instanceof Function)
        {
            callbackBefore();
        }
    },
    finish: function (callbackFinish) {
        if ( callbackFinish instanceof Function)
        {
            callbackFinish();
        }

        if ( request.response.errors )
        {
            msg.error(request.response.errors);
        }
    },
    setUrl: function(url) {
        this.url = url;
    },
    setType: function(type) {
        this.type = type;
    },
    setParams: function (params) {
        if ( typeof params === 'object' ) {
            this.params = Object.keys(params).map(function(key) {
                return key + '=' + params[key];
            }).join('&');
        }
    }
}


/**
 * act
 * @type {{init: act.init, selector: NodeListOf<Element>, click: act.click}}
 */
var act = {
    selector: document.querySelectorAll('[data-action]'),
    init: function() {
        act.selector.forEach(function (e) {
            e.addEventListener('click', function () {
                act.click(e);
            });
        });
    },
    click: function (e) {
        e.getElementsByClassName('text')[0].classList.add('hide');
        e.getElementsByClassName('loader')[0].classList.add('show');
    }
}




act.init();
search.init();