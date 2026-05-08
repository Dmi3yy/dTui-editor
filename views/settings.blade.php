@php
    $profiles = $profiles ?? [];
    $themes = $themes ?? [];
    $editorModes = $editorModes ?? [];
    $plugins = $plugins ?? [];
    $currentProfile = $currentProfile ?? '';
    $currentTheme = $currentTheme ?? 'auto';
    $currentEditorMode = $currentEditorMode ?? 'wysiwyg';
@endphp

<div class="row form-row form-element-select">
    <label for="dtui_profile" class="control-label col-5 col-md-3 col-lg-2">
        dTui.editor Profile:
        <small class="form-text text-muted">[(dtui_profile)]</small>
    </label>
    <div class="col-7 col-md-9 col-lg-10">
        <select class="form-control" name="dtui_profile" id="dtui_profile" onchange="documentDirty=true;" size="1">
            @foreach($profiles as $key => $label)
                <option value="{{ $key }}" @if($currentProfile === $key) selected @endif>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row form-row form-element-select">
    <label for="dtui_editor_mode" class="control-label col-5 col-md-3 col-lg-2">
        Editor Mode:
        <small class="form-text text-muted">[(dtui_editor_mode)]</small>
    </label>
    <div class="col-7 col-md-9 col-lg-10">
        <select class="form-control" name="dtui_editor_mode" id="dtui_editor_mode" onchange="documentDirty=true;" size="1">
            @foreach($editorModes as $key => $label)
                <option value="{{ $key }}" @if($currentEditorMode === $key) selected @endif>{{ $label }}</option>
            @endforeach
        </select>
        <small class="form-text text-muted">Markdown only, Markdown + preview, or WYSIWYG only.</small>
    </div>
</div>

<div class="row form-row form-element-select">
    <label for="dtui_editor_theme" class="control-label col-5 col-md-3 col-lg-2">
        Editor Theme:
        <small class="form-text text-muted">[(dtui_editor_theme)]</small>
    </label>
    <div class="col-7 col-md-9 col-lg-10">
        <select class="form-control" name="dtui_editor_theme" id="dtui_editor_theme" onchange="documentDirty=true;" size="1">
            @foreach($themes as $key => $label)
                <option value="{{ $key }}" @if($currentTheme === $key) selected @endif>{{ $label }}</option>
            @endforeach
        </select>
        <small class="form-text text-muted">Theme follows the manager when set to auto.</small>
    </div>
</div>

@foreach($plugins as $key => $plugin)
    <div class="row form-row form-element-checkbox">
        <label for="{{ $plugin['setting'] }}" class="control-label col-5 col-md-3 col-lg-2">
            {{ $plugin['label'] }}:
            <small class="form-text text-muted">[({{ $plugin['setting'] }})]</small>
        </label>
        <div class="col-7 col-md-9 col-lg-10">
            <input type="hidden" name="{{ $plugin['setting'] }}" value="0" />
            <label class="mb-0">
                <input
                    type="checkbox"
                    name="{{ $plugin['setting'] }}"
                    id="{{ $plugin['setting'] }}"
                    value="1"
                    onchange="documentDirty=true;"
                    @if(!empty($plugin['enabled'])) checked @endif
                />
                enabled
            </label>
        </div>
    </div>
@endforeach
