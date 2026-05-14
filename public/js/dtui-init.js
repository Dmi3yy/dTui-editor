(function () {
    'use strict';

    var instances = window.dTuiEditorInstances = window.dTuiEditorInstances || {};
    var themedContainers = [];
    var themeObserverStarted = false;
    var themeModes = ['lightness', 'light', 'dark', 'darkness'];

    function normalizeString(value) {
        return typeof value === 'string' ? value.trim() : '';
    }

    function escapeAttributeValue(value) {
        return String(value).replace(/\\/g, '\\\\').replace(/"/g, '\\"');
    }

    function resolveTextareas(selector, name) {
        var textareas = [];

        function add(el) {
            if (!el || !el.tagName || el.tagName.toLowerCase() !== 'textarea') {
                return;
            }
            if (textareas.indexOf(el) === -1) {
                textareas.push(el);
            }
        }

        selector = normalizeString(selector);
        if (selector) {
            try {
                Array.prototype.forEach.call(document.querySelectorAll(selector), add);
            } catch (e) {
                if (selector.charAt(0) === '#') {
                    add(document.getElementById(selector.slice(1)));
                }
            }

            if (!textareas.length && selector.charAt(0) === '#') {
                add(document.getElementById(selector.slice(1)));
            }
        }

        if (!textareas.length && name) {
            try {
                Array.prototype.forEach.call(
                    document.querySelectorAll('textarea[name="' + escapeAttributeValue(name) + '"]'),
                    add
                );
            } catch (e) {
                // Ignore invalid legacy names; the selector branch already had a chance to resolve them.
            }
        }

        return textareas;
    }

    function resolveTextarea(target) {
        if (!target) {
            return null;
        }

        if (target.tagName && target.tagName.toLowerCase() === 'textarea') {
            return target;
        }

        if (typeof target === 'string') {
            var selector = target.charAt(0) === '#' ? target : '#' + target;
            var textareas = resolveTextareas(selector, target);
            return textareas[0] || null;
        }

        return null;
    }

    function editorFor(target) {
        var textarea = resolveTextarea(target);
        var id = textarea && textarea.dataset ? textarea.dataset.dtuiEditorId : '';

        return id && instances[id] ? instances[id] : null;
    }

    function makeId(item, index, textarea, textareaIndex) {
        var base = normalizeString(textarea.id || item.name || item.selector || ('editor-' + index));
        var suffix = index + '-' + textareaIndex;
        return 'dtui-editor-' + base.replace(/[^a-zA-Z0-9_-]+/g, '-') + '-' + suffix;
    }

    function ensureContainer(textarea, id) {
        if (textarea.dataset && textarea.dataset.dtuiEditorContainerId) {
            var current = document.getElementById(textarea.dataset.dtuiEditorContainerId);
            if (current) {
                return current;
            }
        }

        var existing = document.getElementById(id);
        if (existing) {
            if (textarea.dataset) {
                textarea.dataset.dtuiEditorContainerId = id;
            }
            return existing;
        }

        var container = document.createElement('div');
        container.id = id;
        container.className = 'dtui-editor';
        textarea.parentNode.insertBefore(container, textarea.nextSibling);
        if (textarea.dataset) {
            textarea.dataset.dtuiEditorContainerId = id;
        }
        return container;
    }

    function pluginOptions(globalConfig, name) {
        var plugins = globalConfig.plugins || {};
        var cfg = plugins[name] || {};
        var options = {};
        if (cfg.options && typeof cfg.options === 'object') {
            options = Object.assign({}, cfg.options);
        }
        return options;
    }

    function imageOptions(globalConfig) {
        return pluginOptions(globalConfig, 'image');
    }

    function normalizeEditorMode(value) {
        var mode = normalizeString(value).toLowerCase();

        if (mode === 'markdown-only' || mode === 'md') {
            return 'markdown';
        }
        if (mode === 'wysiwyg-only' || mode === 'ww') {
            return 'wysiwyg';
        }
        if (mode === 'vertical') {
            return 'split';
        }

        return ['markdown', 'split', 'wysiwyg'].indexOf(mode) !== -1 ? mode : 'wysiwyg';
    }

    function applyEditorModeOptions(options) {
        var mode = normalizeEditorMode(options.editorMode);

        options.hideModeSwitch = true;
        if (mode === 'markdown') {
            options.initialEditType = 'markdown';
            options.previewStyle = 'tab';
        } else if (mode === 'split') {
            options.initialEditType = 'markdown';
            options.previewStyle = 'vertical';
        } else {
            options.initialEditType = 'wysiwyg';
            options.previewStyle = 'tab';
        }

        delete options.editorMode;
        return mode;
    }

    function encodeEvoPlaceholderUrls(value) {
        if (typeof value !== 'string' || value === '') {
            return value || '';
        }

        return value
            .replace(/\]\(\[~([0-9]+)~\]\)/g, '](%5B~$1~%5D)')
            .replace(/(href=["'])\[~([0-9]+)~\](["'])/g, '$1%5B~$2~%5D$3');
    }

    function decodeEvoPlaceholderUrls(value) {
        if (typeof value !== 'string' || value === '') {
            return value || '';
        }

        return value
            .replace(/\]\(%5B~([0-9]+)~%5D\)/gi, ']([~$1~])')
            .replace(/(href=["'])%5B~([0-9]+)~%5D(["'])/gi, '$1[~$2~]$3');
    }

    function escapeHtmlAttribute(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function utf8ToBinary(value) {
        var bytes;
        var binary = '';
        var i;

        if (window.TextEncoder) {
            bytes = new window.TextEncoder().encode(String(value));
            for (i = 0; i < bytes.length; i += 1) {
                binary += String.fromCharCode(bytes[i]);
            }
            return binary;
        }

        return unescape(encodeURIComponent(String(value)));
    }

    function binaryToUtf8(value) {
        var bytes;
        var i;

        if (window.TextDecoder) {
            bytes = new Uint8Array(value.length);
            for (i = 0; i < value.length; i += 1) {
                bytes[i] = value.charCodeAt(i);
            }
            return new window.TextDecoder('utf-8').decode(bytes);
        }

        return decodeURIComponent(escape(value));
    }

    function base64UrlEncode(value) {
        return window.btoa(utf8ToBinary(value))
            .replace(/\+/g, '-')
            .replace(/\//g, '_')
            .replace(/=+$/g, '');
    }

    function base64UrlDecode(value) {
        var normalized = String(value || '').replace(/-/g, '+').replace(/_/g, '/');
        while (normalized.length % 4) {
            normalized += '=';
        }
        return binaryToUtf8(window.atob(normalized));
    }

    function safeBase64UrlDecode(value) {
        try {
            return base64UrlDecode(value);
        } catch (e) {
            return '';
        }
    }

    function unescapeMarkdownImagePart(value) {
        return String(value).replace(/\\+([\\`*{}\[\]()#+\-.!_|<>])/g, '$1');
    }

    function imageMarkdownToHtml(value) {
        if (typeof value !== 'string' || value === '') {
            return value || '';
        }

        var toImage = function (match, alt, url) {
            var imageUrl = unescapeMarkdownImagePart(url).trim();
            var altText = unescapeMarkdownImagePart(alt).trim() || 'image';
            if (!imageUrl || imageUrl.indexOf('"') !== -1 || imageUrl.indexOf('<') !== -1 || imageUrl.indexOf('>') !== -1) {
                return match;
            }
            return '<img src="' + escapeHtmlAttribute(imageUrl) + '" alt="' + escapeHtmlAttribute(altText) + '">';
        };

        return value
            .replace(/\\+!\\+\[([\s\S]*?)\\+\]\\+\(([\s\S]*?)\\+\)/g, toImage)
            .replace(/!\[([^\]]*)\]\(([^)\s]+)(?:\s+["'][^"']*["'])?\)/g, toImage);
    }

    function extractUmlBlocks(markdown) {
        var blocks = [];
        var source = typeof markdown === 'string' ? markdown.replace(/\r\n/g, '\n') : '';
        var pattern = /(^|\n)\$\$uml[^\n]*\n([\s\S]*?)\n\$\$(?=\n|$)/g;
        var match;

        while ((match = pattern.exec(source)) !== null) {
            blocks.push(match[2]);
        }

        return blocks;
    }

    function isUmlCustomBlock(block) {
        var info = block.querySelector('.tool .info, .info');
        return info && normalizeString(info.textContent).toLowerCase() === 'uml';
    }

    function makePersistentUmlFigure(source, imageSrc) {
        var figure = document.createElement('figure');
        var img = document.createElement('img');
        var encoded = base64UrlEncode(source);

        figure.className = 'dtui-uml';
        figure.setAttribute('data-uml', encoded);
        figure.setAttribute('data-uml-encoding', 'base64url');

        img.src = imageSrc || '';
        img.alt = 'uml';
        img.setAttribute('data-dtui-uml', encoded);
        figure.appendChild(img);

        return figure;
    }

    function persistUmlBlocks(html, markdown) {
        var sources = extractUmlBlocks(markdown);
        var root;
        var changed = false;

        if (!sources.length || typeof html !== 'string' || html.indexOf('toastui-editor-custom-block') === -1) {
            return html;
        }

        root = document.createElement('div');
        root.innerHTML = html;

        Array.prototype.forEach.call(root.querySelectorAll('.toastui-editor-custom-block'), function (block) {
            var source;
            var image;

            if (!sources.length || !isUmlCustomBlock(block)) {
                return;
            }

            source = sources.shift();
            image = block.querySelector('img');
            block.parentNode.replaceChild(makePersistentUmlFigure(source, image ? image.getAttribute('src') : ''), block);
            changed = true;
        });

        return changed ? root.innerHTML : html;
    }

    function storedUmlNodes(root) {
        return Array.prototype.filter.call(
            root.querySelectorAll('figure.dtui-uml[data-uml], img[data-dtui-uml]'),
            function (node) {
                return node.tagName.toLowerCase() !== 'img' || !node.closest('figure.dtui-uml[data-uml]');
            }
        );
    }

    function prepareStoredUmlBlocks(value) {
        var root = document.createElement('div');
        var sources = [];
        var changed = false;

        if (typeof value !== 'string' || value.indexOf('dtui-uml') === -1) {
            return { value: value || '', umlSources: sources };
        }

        root.innerHTML = value;

        storedUmlNodes(root).forEach(function (node, index) {
            var encoded = node.getAttribute('data-uml') || node.getAttribute('data-dtui-uml');
            var source = safeBase64UrlDecode(encoded);
            var placeholder;
            var paragraph;

            if (!source) {
                return;
            }

            placeholder = 'DTUIUMLSOURCE' + Date.now() + index;
            paragraph = document.createElement('p');
            paragraph.textContent = placeholder;
            sources.push({ placeholder: placeholder, source: source });
            node.parentNode.replaceChild(paragraph, node);
            changed = true;
        });

        return {
            value: changed ? root.innerHTML : value,
            umlSources: sources
        };
    }

    function restoreStoredUmlBlocks(editor, umlSources) {
        var markdown;

        if (!umlSources.length || typeof editor.getMarkdown !== 'function' || typeof editor.setMarkdown !== 'function') {
            return;
        }

        markdown = editor.getMarkdown();
        umlSources.forEach(function (item) {
            markdown = markdown.split(item.placeholder).join('$$uml\n' + item.source + '\n$$');
        });
        editor.setMarkdown(markdown, false);
    }

    function prepareInitialValue(value) {
        return prepareStoredUmlBlocks(encodeEvoPlaceholderUrls(stripEditorRuntimeAttributes(imageMarkdownToHtml(value || ''))));
    }

    function looksLikeHtml(value) {
        return typeof value === 'string' && /<\/?[a-z][\s\S]*>/i.test(value);
    }

    function stripEditorRuntimeAttributes(value) {
        if (typeof value !== 'string' || value === '') {
            return value || '';
        }

        return value
            .replace(/\scontenteditable=(["'])false\1/gi, '')
            .replace(/<div\s+class=(["'])tool\1>\s*<span\s+class=(["'])info\2>uml<\/span>\s*<button\b[^>]*><\/button>\s*<\/div>/gi, '')
            .replace(/<span\s+class=(["'])info\1>uml<\/span>/gi, '');
    }

    function serializeEditorValue(editor) {
        var markdown = typeof editor.getMarkdown === 'function' ? editor.getMarkdown() : '';
        var value = typeof editor.getHTML === 'function' ? editor.getHTML() : markdown;
        value = imageMarkdownToHtml(decodeEvoPlaceholderUrls(value));
        value = persistUmlBlocks(value, markdown);
        return stripEditorRuntimeAttributes(value);
    }

    function pushToastPlugin(list, name, options) {
        var registry = window.toastui && window.toastui.Editor && window.toastui.Editor.plugin;
        var plugin = registry && registry[name];
        if (!plugin) {
            console.warn('dTui.editor plugin missing: ' + name);
            return;
        }
        if (options && Object.keys(options).length) {
            list.push([plugin, options]);
        } else {
            list.push(plugin);
        }
    }

    function umlToolbarPlugin() {
        return function (context) {
            var eventEmitter = context.eventEmitter;
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'toastui-editor-toolbar-icons dtui-uml-button';
            button.textContent = 'UML';
            button.title = 'UML';
            button.style.backgroundImage = 'none';
            button.style.fontSize = '10px';
            button.style.fontWeight = '700';

            button.addEventListener('click', function () {
                eventEmitter.emit('command', 'customBlock', { info: 'uml' });
                eventEmitter.emit('closePopup');
            });

            return {
                toolbarItems: [{
                    groupIndex: 4,
                    itemIndex: 2,
                    item: {
                        name: 'dtuiUml',
                        tooltip: 'UML',
                        el: button
                    }
                }]
            };
        };
    }

    function uploadImage(blob, globalConfig) {
        var uploadUrl = normalizeString(globalConfig.imageUploadUrl || '');
        if (!uploadUrl) {
            return Promise.reject(new Error('Image upload URL is not configured.'));
        }

        var form = new FormData();
        form.append('image', blob, blob.name || 'pasted-image.png');

        return window.fetch(uploadUrl, {
            method: 'POST',
            body: form,
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        }).then(function (response) {
            return response.json().catch(function () {
                return {};
            }).then(function (data) {
                if (!response.ok || !data.success || !data.url) {
                    throw new Error(data.message || 'Image upload failed.');
                }
                return data;
            });
        });
    }

    function applyImageUploadHook(options, globalConfig) {
        var imgOptions = imageOptions(globalConfig);
        if (imgOptions.pasteUpload === false || !globalConfig.imageUploadUrl) {
            return;
        }

        options.hooks = Object.assign({}, options.hooks || {});
        options.hooks.addImageBlobHook = function (blob, callback) {
            uploadImage(blob, globalConfig).then(function (data) {
                callback(data.url, data.alt || blob.name || 'image');
            }).catch(function (error) {
                console.warn('dTui.editor image upload failed:', error);
                window.alert(error && error.message ? error.message : 'Image upload failed.');
            });
        };
    }

    function applyUmlThemeOptions(options, globalConfig) {
        if (options.darkTheme === false || !globalConfig.plantUmlRendererUrl) {
            return options;
        }

        var mode = currentThemeMode(globalConfig);
        if (!isDarkMode(mode, globalConfig)) {
            return options;
        }

        var themedOptions = Object.assign({}, options);
        themedOptions.rendererURL = String(globalConfig.plantUmlRendererUrl).replace(/\/?$/, '/') + '?theme=' + encodeURIComponent(mode) + '&uml=';
        return themedOptions;
    }

    function defineLanguageAlias(languages, alias, grammar) {
        if (!alias || languages[alias] || !grammar) {
            return;
        }

        Object.defineProperty(languages, alias, {
            value: grammar,
            enumerable: false,
            configurable: true
        });
    }

    function createPrismHighlighter(options) {
        if (!window.Prism) {
            return null;
        }

        var prism = window.Prism;
        var requested = Array.isArray(options.languages) && options.languages.length
            ? options.languages
            : ['html', 'css', 'scss', 'javascript', 'typescript', 'php', 'blade', 'sql', 'json', 'markdown', 'bash', 'yaml'];
        var aliases = options.aliases || {};
        var languages = {};

        requested.forEach(function (name) {
            var grammar = prism.languages[name];
            if (!grammar) {
                return;
            }

            languages[name] = grammar;
            (aliases[name] || []).forEach(function (alias) {
                defineLanguageAlias(languages, alias, grammar);
            });
        });

        return {
            languages: languages,
            highlight: prism.highlight.bind(prism),
            tokenize: prism.tokenize.bind(prism)
        };
    }

    function buildPlugins(item, globalConfig) {
        var requested = item.plugins || [];
        var list = [];

        requested.forEach(function (name) {
            var options = pluginOptions(globalConfig, name);
            if (name === 'chart') {
                pushToastPlugin(list, 'chart', options);
            } else if (name === 'codeSyntaxHighlight') {
                if (!options.highlighter) {
                    options.highlighter = createPrismHighlighter(options);
                }
                pushToastPlugin(list, 'codeSyntaxHighlight', options);
            } else if (name === 'colorSyntax') {
                pushToastPlugin(list, 'colorSyntax', options);
            } else if (name === 'tableMergedCell') {
                pushToastPlugin(list, 'tableMergedCell', options);
            } else if (name === 'uml') {
                pushToastPlugin(list, 'uml', applyUmlThemeOptions(options, globalConfig));
                list.push(umlToolbarPlugin());
            } else if (name === 'image') {
                if (window.dTuiEditorPlugins && typeof window.dTuiEditorPlugins.image === 'function') {
                    list.push(window.dTuiEditorPlugins.image(Object.assign({}, options, {
                        fileManager: globalConfig.fileManager || {},
                        baseUrl: globalConfig.baseUrl || '',
                        whichBrowser: globalConfig.whichBrowser || 'mcpuk',
                        opener: globalConfig.opener || 'tinymce'
                    })));
                }
            } else if (name === 'evolinks') {
                if (window.dTuiEditorPlugins && typeof window.dTuiEditorPlugins.evolinks === 'function') {
                    list.push(window.dTuiEditorPlugins.evolinks(Object.assign({}, options, {
                        searchUrl: globalConfig.evoLinkSearchUrl || '',
                        baseUrl: globalConfig.baseUrl || '',
                        themeMode: globalConfig.themeMode || globalConfig.theme || 'light',
                        followManagerTheme: globalConfig.followManagerTheme !== false,
                        darkThemes: globalConfig.darkThemes || ['dark', 'darkness']
                    })));
                }
            }
        });

        return list;
    }

    function normalizeToolbarItems(items) {
        if (!Array.isArray(items)) {
            return items;
        }

        return items
            .map(function (group) {
                if (!Array.isArray(group)) {
                    return group;
                }
                return group.filter(function (item) {
                    return item !== 'scrollSync';
                });
            })
            .filter(function (group) {
                return !Array.isArray(group) || group.length > 0;
            });
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
        var root = document.documentElement;
        var body = document.body;
        for (var i = 0; i < themeModes.length; i += 1) {
            var mode = themeModes[i];
            if ((body && body.classList.contains(mode)) || (root && root.classList.contains(mode))) {
                return mode;
            }
        }
        return '';
    }

    function isDarkMode(mode, globalConfig) {
        var darkThemes = globalConfig.darkThemes || ['dark', 'darkness'];
        return darkThemes.indexOf(mode) !== -1;
    }

    function currentThemeMode(globalConfig) {
        if (globalConfig.followManagerTheme === false) {
            return globalConfig.themeMode || globalConfig.theme || 'light';
        }
        return modeFromDocument() || modeFromStorage() || modeFromCookie() || globalConfig.themeMode || globalConfig.theme || 'light';
    }

    function applyTheme(container, globalConfig) {
        var mode = currentThemeMode(globalConfig);
        var dark = isDarkMode(mode, globalConfig);
        var ui = container.querySelector('.toastui-editor-defaultUI');

        container.dataset.dtuiThemeMode = mode;
        container.classList.toggle('toastui-editor-dark', dark);

        if (ui) {
            ui.dataset.dtuiThemeMode = mode;
            ui.classList.toggle('toastui-editor-dark', dark);
        }
    }

    function syncThemes() {
        themedContainers.forEach(function (entry) {
            applyTheme(entry.container, entry.config);
        });
    }

    function startThemeObserver() {
        if (themeObserverStarted) {
            return;
        }
        themeObserverStarted = true;

        if (window.MutationObserver) {
            var observer = new MutationObserver(syncThemes);
            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            if (document.body) {
                observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
            }
        }

        window.addEventListener('storage', function (event) {
            if (!event || event.key === 'EVO_themeMode') {
                syncThemes();
            }
        });
        window.setInterval(syncThemes, 1000);
    }

    function registerTheme(container, globalConfig) {
        themedContainers.push({ container: container, config: globalConfig });
        applyTheme(container, globalConfig);
        startThemeObserver();
    }

    function modeLabel(mode) {
        if (mode === 'markdown') {
            return 'Markdown';
        }
        if (mode === 'split') {
            return 'Split';
        }
        return 'WYSIWYG';
    }

    function applyMarkdownPreviewStyle(container, mode) {
        var mdContainer = container.querySelector('.toastui-editor-md-container');
        if (!mdContainer) {
            return;
        }

        if (mode === 'split') {
            mdContainer.classList.add('toastui-editor-md-vertical-style');
            mdContainer.classList.remove('toastui-editor-md-tab-style');
        } else {
            mdContainer.classList.add('toastui-editor-md-tab-style');
            mdContainer.classList.remove('toastui-editor-md-vertical-style');
        }
    }

    function syncModeSwitcher(container, mode) {
        Array.prototype.forEach.call(container.querySelectorAll('.dtui-editor-mode-switcher button'), function (button) {
            var active = button.dataset.dtuiMode === mode;
            button.classList.toggle('active', active);
            button.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
    }

    function setEditorMode(editor, container, mode) {
        mode = normalizeEditorMode(mode);
        if (!editor || !container) {
            return mode;
        }

        var editType = mode === 'wysiwyg' ? 'wysiwyg' : 'markdown';
        if (typeof editor.changeMode === 'function') {
            editor.changeMode(editType);
        }

        container.dataset.dtuiEditorMode = mode;
        applyMarkdownPreviewStyle(container, mode);
        window.setTimeout(function () {
            applyMarkdownPreviewStyle(container, mode);
        }, 0);
        syncModeSwitcher(container, mode);

        return mode;
    }

    function addEditorModeSwitcher(container, editor, initialMode) {
        var defaultUi = container.querySelector('.toastui-editor-defaultUI');
        if (!defaultUi || defaultUi.querySelector('.dtui-editor-mode-switcher')) {
            return;
        }

        var switcher = document.createElement('div');
        switcher.className = 'dtui-editor-mode-switcher';
        switcher.setAttribute('role', 'group');
        switcher.setAttribute('aria-label', 'Editor mode');

        ['markdown', 'split', 'wysiwyg'].forEach(function (mode) {
            var button = document.createElement('button');
            button.type = 'button';
            button.dataset.dtuiMode = mode;
            button.textContent = modeLabel(mode);
            button.title = modeLabel(mode);
            button.addEventListener('click', function () {
                setEditorMode(editor, container, mode);
            });
            switcher.appendChild(button);
        });

        defaultUi.appendChild(switcher);
        setEditorMode(editor, container, initialMode);
    }

    function syncEditor(editor, textarea) {
        textarea.value = serializeEditorValue(editor);
        if (typeof window.documentDirty !== 'undefined') {
            window.documentDirty = true;
        }
    }

    function syncTextarea(target) {
        var textarea = resolveTextarea(target);
        var editor = editorFor(textarea);

        if (!textarea || !editor) {
            return false;
        }

        textarea.value = serializeEditorValue(editor);
        return true;
    }

    function getValue(target) {
        var textarea = resolveTextarea(target);

        if (!textarea) {
            return '';
        }

        syncTextarea(textarea);
        return textarea.value || '';
    }

    function setMode(target, mode) {
        var textarea = resolveTextarea(target);
        var editor = editorFor(textarea);
        var containerId = textarea && textarea.dataset ? textarea.dataset.dtuiEditorContainerId : '';
        var container = containerId ? document.getElementById(containerId) : null;

        return setEditorMode(editor, container, mode);
    }

    function removeTextarea(target) {
        var textarea = resolveTextarea(target);
        var editor = editorFor(textarea);
        var editorId = textarea && textarea.dataset ? textarea.dataset.dtuiEditorId : '';
        var containerId = textarea && textarea.dataset ? textarea.dataset.dtuiEditorContainerId : '';

        if (editor && typeof editor.destroy === 'function') {
            editor.destroy();
        }

        if (editorId) {
            delete instances[editorId];
        }

        if (containerId) {
            var container = document.getElementById(containerId);
            if (container && container.parentNode) {
                container.parentNode.removeChild(container);
            }
        }

        if (textarea) {
            textarea.style.display = '';
            if (textarea.dataset) {
                delete textarea.dataset.dtuiEditorId;
                delete textarea.dataset.dtuiEditorContainerId;
            }
        }

        themedContainers = themedContainers.filter(function (entry) {
            return entry.container && entry.container.id !== containerId;
        });
    }

    function initTextarea(item, index, globalConfig, textarea, textareaIndex) {
        if (!window.toastui || !window.toastui.Editor) {
            console.warn('dTui.editor: TOAST UI Editor is not loaded.');
            return;
        }

        if (textarea.dataset && textarea.dataset.dtuiEditorId && instances[textarea.dataset.dtuiEditorId]) {
            return;
        }

        var id = makeId(item, index, textarea, textareaIndex);
        if (instances[id]) {
            var staleContainer = document.getElementById(id);
            if (staleContainer && document.body.contains(staleContainer)) {
                return;
            }

            if (typeof instances[id].destroy === 'function') {
                try {
                    instances[id].destroy();
                } catch (e) {
                    // The DOM node may already be gone after a Livewire remount.
                }
            }
            delete instances[id];
        }

        var container = ensureContainer(textarea, id);
        if (textarea.dataset) {
            textarea.dataset.dtuiEditorId = id;
        }
        textarea.style.display = 'none';

        var options = Object.assign({}, item.options || {});
        var editorMode = applyEditorModeOptions(options);
        container.dataset.dtuiEditorMode = editorMode;
        var preparedInitial = prepareInitialValue(textarea.value || '');
        var initialValue = preparedInitial.value;
        var initialIsHtml = looksLikeHtml(initialValue);
        options.el = container;
        options.initialValue = initialIsHtml ? '' : initialValue;
        options.plugins = buildPlugins(item, globalConfig);
        options.toolbarItems = normalizeToolbarItems(options.toolbarItems);
        applyImageUploadHook(options, globalConfig);
        if (isDarkMode(currentThemeMode(globalConfig), globalConfig)) {
            options.theme = 'dark';
        } else if (options.theme === 'dark') {
            delete options.theme;
        }

        var editor = new window.toastui.Editor(options);
        if (initialIsHtml && typeof editor.setHTML === 'function') {
            editor.setHTML(initialValue, false);
        }
        restoreStoredUmlBlocks(editor, preparedInitial.umlSources || []);
        registerTheme(container, globalConfig);
        addEditorModeSwitcher(container, editor, editorMode);
        editor.on('change', function () {
            syncEditor(editor, textarea);
        });

        var form = textarea.form;
        if (form) {
            form.addEventListener('submit', function () {
                textarea.value = serializeEditorValue(editor);
            });
        }

        instances[id] = editor;
    }

    function initOne(item, index, globalConfig) {
        var textareas = resolveTextareas(item.selector, item.name);
        if (!textareas.length) {
            console.warn('dTui.editor target missing: ' + item.selector);
            return;
        }

        textareas.forEach(function (textarea, textareaIndex) {
            initTextarea(item, index, globalConfig, textarea, textareaIndex);
        });
    }

    function boot(config) {
        config = config || window.dTuiEditorConfig || {};
        if (config.__dtuiBooted) {
            return;
        }

        var queue = config.queue || [];
        if (!queue.length) {
            return;
        }
        config.__dtuiBooted = true;

        var run = function () {
            queue.forEach(function (item, index) {
                initOne(item, index, config);
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', run);
        } else {
            run();
        }
    }

    function enqueue(config) {
        window.dTuiEditorConfigQueue = window.dTuiEditorConfigQueue || [];
        if (config && config.queue && config.queue.length) {
            window.dTuiEditorConfigQueue.push(config);
        }
        flush();
    }

    function flush() {
        window.dTuiEditorConfigQueue = window.dTuiEditorConfigQueue || [];

        while (window.dTuiEditorConfigQueue.length) {
            boot(window.dTuiEditorConfigQueue.shift());
        }

        if (
            window.dTuiEditorConfig
            && window.dTuiEditorConfig.queue
            && window.dTuiEditorConfig.queue.length
            && !window.dTuiEditorConfig.__dtuiBooted
        ) {
            boot(window.dTuiEditorConfig);
        }
    }

    window.dTuiEditor = window.dTuiEditor || {};
    window.dTuiEditor.boot = boot;
    window.dTuiEditor.enqueue = enqueue;
    window.dTuiEditor.flush = flush;
    window.dTuiEditor.get = editorFor;
    window.dTuiEditor.sync = syncTextarea;
    window.dTuiEditor.getValue = getValue;
    window.dTuiEditor.setMode = setMode;
    window.dTuiEditor.remove = removeTextarea;
    flush();
})();
