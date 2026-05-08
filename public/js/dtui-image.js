(function () {
    'use strict';

    window.dTuiEditorPlugins = window.dTuiEditorPlugins || {};

    function normalizeString(value) {
        return typeof value === 'string' ? value.trim() : '';
    }

    function normalizeBaseUrl(value) {
        var base = normalizeString(value).replace(/\/+$/, '');
        if (base && base[0] !== '/') {
            base = '/' + base;
        }
        return base;
    }

    function stripSignedQuery(url) {
        var parts = url.split('?');
        if (parts.length < 2) {
            return url;
        }
        var query = parts.slice(1).join('?').toLowerCase();
        if (
            query.indexOf('signature=') === -1 &&
            query.indexOf('x-amz-signature=') === -1 &&
            query.indexOf('expires=') === -1
        ) {
            return url;
        }
        return parts[0];
    }

    function normalizeSelectedUrl(url, options) {
        var normalized = normalizeString(url);
        if (!normalized) {
            return '';
        }
        if (!options.allowSignedUrls) {
            normalized = stripSignedQuery(normalized);
        }
        if (options.urlStrategy === 'absolute') {
            try {
                return new URL(normalized, window.location.origin).href;
            } catch (e) {
                return normalized;
            }
        }
        try {
            var parsed = new URL(normalized, window.location.origin);
            if (parsed.origin === window.location.origin) {
                return parsed.pathname + parsed.search + parsed.hash;
            }
        } catch (e) {
        }
        return normalized;
    }

    function buildFileManagerUrl(options) {
        var fm = options.fileManager || {};
        var prefix = normalizeString(fm.urlPrefix || 'filemanager').replace(/^\/+/, '');
        var base = normalizeBaseUrl(options.baseUrl || '');
        return (base ? base : '') + '/' + prefix + '?type=Images';
    }

    function insertImage(eventEmitter, url, meta) {
        if (!url) {
            return;
        }
        eventEmitter.emit('command', 'addImage', {
            imageUrl: url,
            altText: normalizeString((meta && (meta.alt || meta.title || meta.name)) || 'image')
        });
        eventEmitter.emit('closePopup');
    }

    function openLegacyMcpuk(options, callback) {
        var managerUrl = (window.evo && window.evo.EVO_MANAGER_URL)
            || (window.modx && (window.modx.EVO_MANAGER_URL || window.modx.MODX_MANAGER_URL))
            || window.EVO_MANAGER_URL
            || window.MODX_MANAGER_URL
            || '';
        var url = managerUrl + 'media/browser/mcpuk/browse.php?opener=dtui&field=src&type=images';
        var previousKCFinder = window.KCFinder;
        var previousTinyUrl = window.tinymceCallBackURL;
        var finished = false;
        window.tinymceCallBackURL = '';

        function cleanup() {
            window.KCFinder = previousKCFinder;
            window.tinymceCallBackURL = previousTinyUrl;
        }

        function pick(url) {
            if (finished) {
                return;
            }
            finished = true;
            window.clearInterval(timer);
            cleanup();
            if (popup && !popup.closed) {
                popup.close();
            }
            callback(normalizeSelectedUrl(url, options));
        }

        window.KCFinder = {
            callBack: pick,
            callBackMultiple: function (urls) {
                if (urls && urls.length) {
                    pick(urls[0]);
                }
            }
        };

        var popup = window.open(url, 'dtuiImageBrowser', 'width=900,height=650,resizable=yes,scrollbars=yes');
        var timer = window.setInterval(function () {
            if (window.tinymceCallBackURL) {
                pick(window.tinymceCallBackURL);
                return;
            }
            if (!popup || popup.closed) {
                cleanup();
                window.clearInterval(timer);
            }
        }, 300);
    }

    function openEFilemanager(options, callback) {
        var popup = window.open(buildFileManagerUrl(options), 'dtuiImageBrowser', 'width=900,height=650,resizable=yes,scrollbars=yes');
        var listener = function (event) {
            if (event.origin && event.origin !== window.location.origin) {
                return;
            }
            var payload = event.data || {};
            if (!payload || typeof payload !== 'object') {
                return;
            }
            var action = normalizeString(payload.mceAction || payload.action);
            if (action !== 'insert') {
                return;
            }
            var url = normalizeSelectedUrl(payload.content || payload.url || '', options);
            if (url) {
                callback(url, payload.meta || {});
            }
            window.removeEventListener('message', listener);
            if (popup && !popup.closed) {
                popup.close();
            }
        };
        window.addEventListener('message', listener);
    }

    function openPicker(options, callback) {
        var fm = options.fileManager || {};
        if (options.whichBrowser === 'efilemanager' && fm.enabled) {
            openEFilemanager(options, callback);
            return;
        }
        if (options.whichBrowser === 'mcpuk' || fm.allowMcpukFallback) {
            openLegacyMcpuk(options, callback);
            return;
        }
        window.alert('File manager is disabled.');
    }

    window.dTuiEditorPlugins.image = function (rawOptions) {
        var options = Object.assign({
            urlStrategy: 'relative',
            allowSignedUrls: false,
            allowMcpukFallback: true
        }, rawOptions || {});

        return function (context) {
            var eventEmitter = context.eventEmitter;
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'toastui-editor-toolbar-icons dtui-image-button';
            button.textContent = 'Img';
            button.title = 'EVO image';
            button.style.backgroundImage = 'none';
            button.style.fontSize = '11px';
            button.style.fontWeight = '600';

            button.addEventListener('click', function () {
                openPicker(options, function (url, meta) {
                    if (!url) {
                        return;
                    }
                    insertImage(eventEmitter, url, meta);
                });
            });

            return {
                toolbarItems: [{
                    groupIndex: 3,
                    itemIndex: 3,
                    item: {
                        name: 'dtuiImage',
                        tooltip: 'EVO image',
                        el: button
                    }
                }]
            };
        };
    };
})();
