(function () {
    'use strict';

    window.dTuiEditorPlugins = window.dTuiEditorPlugins || {};

    function normalizeString(value) {
        return typeof value === 'string' ? value.trim() : '';
    }

    function intValue(value, fallback) {
        var parsed = parseInt(value, 10);
        return Number.isFinite(parsed) && parsed > 0 ? parsed : fallback;
    }

    function ensureLeadingSlash(value) {
        if (!value) {
            return '';
        }
        return value[0] === '/' ? value : '/' + value;
    }

    function buildHref(item, options) {
        if (!item) {
            return '';
        }
        if (options.outputMode === 'relative' && item.uri) {
            return ensureLeadingSlash(item.uri);
        }
        if (options.outputMode === 'absolute' && item.url) {
            return item.url;
        }
        if (options.encodePlaceholderUrls !== false) {
            return '%5B~' + item.id + '~%5D';
        }
        return '[~' + item.id + '~]';
    }

    function modeFromIndex(index) {
        var map = ['', 'lightness', 'light', 'dark', 'darkness'];
        var parsed = parseInt(index, 10);
        return map[parsed] || '';
    }

    function modeFromCookie() {
        var match = document.cookie.match(/(?:^|;\s*)EVO_themeMode=([^;]+)/);
        if (!match) {
            match = document.cookie.match(/(?:^|;\s*)MODX_themeMode=([^;]+)/);
        }
        return match ? modeFromIndex(decodeURIComponent(match[1])) : '';
    }

    function modeFromStorage() {
        try {
            return window.localStorage ? modeFromIndex(window.localStorage.getItem('EVO_themeMode')) : '';
        } catch (e) {
            return '';
        }
    }

    function modeFromDocument() {
        var modes = ['lightness', 'light', 'dark', 'darkness'];
        for (var i = 0; i < modes.length; i += 1) {
            if (
                (document.body && document.body.classList.contains(modes[i])) ||
                document.documentElement.classList.contains(modes[i])
            ) {
                return modes[i];
            }
        }
        return '';
    }

    function currentThemeMode(options) {
        if (options.followManagerTheme === false) {
            return options.themeMode || 'light';
        }
        return modeFromDocument() || modeFromStorage() || modeFromCookie() || options.themeMode || 'light';
    }

    function fetchJson(url) {
        return new Promise(function (resolve) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState !== 4) {
                    return;
                }
                if (xhr.status < 200 || xhr.status >= 300) {
                    resolve([]);
                    return;
                }
                try {
                    var data = JSON.parse(xhr.responseText);
                    resolve(Array.isArray(data) ? data : []);
                } catch (e) {
                    resolve([]);
                }
            };
            xhr.send();
        });
    }

    function search(query, options, cache) {
        if (cache[query]) {
            return Promise.resolve(cache[query]);
        }
        var params = [
            'q=' + encodeURIComponent(query),
            'limit=' + encodeURIComponent(options.limit)
        ];
        if (options.includeUnpublished) {
            params.push('includeUnpublished=1');
        }
        if (options.includeHidden) {
            params.push('includeHidden=1');
        }
        var url = options.searchUrl + (options.searchUrl.indexOf('?') === -1 ? '?' : '&') + params.join('&');
        return fetchJson(url).then(function (items) {
            cache[query] = items;
            return items;
        });
    }

    function openDialog(options, insert) {
        var overlay = document.createElement('div');
        overlay.className = 'dtui-evolinks-overlay';
        overlay.dataset.dtuiThemeMode = currentThemeMode(options);
        overlay.style.position = 'fixed';
        overlay.style.inset = '0';
        overlay.style.zIndex = '10000';

        var dialog = document.createElement('div');
        dialog.className = 'dtui-evolinks-dialog';
        dialog.style.position = 'absolute';
        dialog.style.top = '15%';
        dialog.style.left = '50%';
        dialog.style.transform = 'translateX(-50%)';
        dialog.style.width = 'min(560px, calc(100vw - 32px))';

        var title = document.createElement('div');
        title.textContent = 'EVO link';
        title.style.fontWeight = '600';
        title.style.marginBottom = '12px';

        var input = document.createElement('input');
        input.type = 'search';
        input.className = 'form-control';
        input.placeholder = 'Search in EVO';
        input.style.width = '100%';
        input.style.marginBottom = '10px';

        var results = document.createElement('select');
        results.className = 'form-control';
        results.size = 8;
        results.style.width = '100%';
        results.style.marginBottom = '10px';

        var text = document.createElement('input');
        text.type = 'text';
        text.className = 'form-control';
        text.placeholder = 'Text';
        text.style.width = '100%';
        text.style.marginBottom = '12px';

        var actions = document.createElement('div');
        actions.style.textAlign = 'right';

        var cancel = document.createElement('button');
        cancel.type = 'button';
        cancel.className = 'btn btn-secondary';
        cancel.textContent = 'Cancel';
        cancel.style.marginRight = '8px';

        var submit = document.createElement('button');
        submit.type = 'button';
        submit.className = 'btn btn-primary';
        submit.textContent = 'Insert';
        submit.disabled = true;

        actions.appendChild(cancel);
        actions.appendChild(submit);
        dialog.appendChild(title);
        dialog.appendChild(input);
        dialog.appendChild(results);
        dialog.appendChild(text);
        dialog.appendChild(actions);
        overlay.appendChild(dialog);
        document.body.appendChild(overlay);

        var map = {};
        var cache = {};
        var timer = null;

        function close() {
            document.body.removeChild(overlay);
        }

        function fill(items) {
            results.innerHTML = '';
            map = {};
            items.forEach(function (item) {
                if (!item || typeof item.id === 'undefined') {
                    return;
                }
                var label = (item.title || item.pagetitle || item.alias || String(item.id)) + ' (' + item.id + ')';
                var option = document.createElement('option');
                option.value = String(item.id);
                option.textContent = label;
                results.appendChild(option);
                map[option.value] = item;
            });
            submit.disabled = !results.options.length;
        }

        input.addEventListener('input', function () {
            var value = normalizeString(input.value);
            window.clearTimeout(timer);
            if (value.length < options.minChars) {
                fill([]);
                return;
            }
            timer = window.setTimeout(function () {
                search(value, options, cache).then(fill);
            }, options.debounce);
        });

        results.addEventListener('change', function () {
            var item = map[results.value];
            if (item && !text.value) {
                text.value = item.title || item.pagetitle || item.alias || '';
            }
        });

        cancel.addEventListener('click', close);
        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) {
                close();
            }
        });
        submit.addEventListener('click', function () {
            var item = map[results.value];
            if (!item) {
                return;
            }
            insert({
                linkUrl: buildHref(item, options),
                linkText: normalizeString(text.value) || item.title || item.pagetitle || item.alias || String(item.id)
            });
            close();
        });

        input.focus();
    }

    window.dTuiEditorPlugins.evolinks = function (rawOptions) {
        var options = Object.assign({
            searchUrl: '',
            minChars: 2,
            debounce: 250,
            limit: 10,
            outputMode: 'placeholder',
            encodePlaceholderUrls: true,
            includeUnpublished: false,
            includeHidden: false
        }, rawOptions || {});

        options.minChars = intValue(options.minChars, 2);
        options.debounce = intValue(options.debounce, 250);
        options.limit = intValue(options.limit, 10);

        return function (context) {
            var eventEmitter = context.eventEmitter;
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'toastui-editor-toolbar-icons dtui-evolinks-button';
            button.textContent = 'E';
            button.title = 'EVO link';
            button.style.backgroundImage = 'none';
            button.style.fontSize = '13px';
            button.style.fontWeight = '700';

            button.addEventListener('click', function () {
                if (!options.searchUrl) {
                    window.alert('EVO link search URL is not configured.');
                    return;
                }
                openDialog(options, function (payload) {
                    eventEmitter.emit('command', 'addLink', payload);
                    eventEmitter.emit('closePopup');
                });
            });

            return {
                toolbarItems: [{
                    groupIndex: 3,
                    itemIndex: 4,
                    item: {
                        name: 'dtuiEvoLink',
                        tooltip: 'EVO link',
                        el: button
                    }
                }]
            };
        };
    };
})();
