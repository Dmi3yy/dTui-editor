// Run with: node --test tests/image-upload-csrf.js
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const { test } = require('node:test');

function runtime(metaToken, response = { success: true, url: '/assets/images/image.png' }, ok = true) {
    const calls = [];
    const context = {
        FormData,
        console,
        document: {
            querySelector(selector) {
                assert.equal(selector, 'meta[name="csrf-token"]');
                return metaToken === null ? null : { getAttribute: () => metaToken };
            }
        },
        window: {
            fetch: async (url, options) => {
                calls.push({ url, options });
                return { ok, json: async () => response };
            }
        }
    };
    const source = fs.readFileSync(path.join(__dirname, '../public/js/dtui-init.js'), 'utf8');
    // Expose the private upload function inside the VM without changing the public API.
    vm.runInNewContext(source.replace(/\}\)\(\);\s*$/, 'window.testUploadImage = uploadImage;})();'), context);
    return { calls, upload: context.window.testUploadImage };
}

const config = { imageUploadUrl: '/dtui-image-upload/', csrfToken: 'config-token' };
const image = new Blob(['image'], { type: 'image/png' });

test('posts the current manager token and image as multipart data', async () => {
    const { calls, upload } = runtime('manager-token');
    const result = await upload(image, config);
    assert.equal(result.url, '/assets/images/image.png');
    assert.equal(calls.length, 1);
    assert.equal(calls[0].url, config.imageUploadUrl);
    const options = calls[0].options;
    assert.equal(options.method, 'POST');
    assert.equal(options.credentials, 'same-origin');
    assert.equal(options.body.get('_token'), 'manager-token');
    assert.equal(options.body.get('image').name, 'pasted-image.png');
    assert.equal(options.body.get('image').type, 'image/png');
    assert.equal(options.headers.Accept, 'application/json');
    assert.equal(options.headers['Content-Type'], undefined);
});

test('uses the server-generated token on standalone editor pages', async () => {
    for (const meta of [null, '', '   ']) {
        const { calls, upload } = runtime(meta);
        await upload(image, config);
        assert.equal(calls[0].options.body.get('_token'), 'config-token');
    }
});

test('does not send an upload when neither token source exists', async () => {
    const { calls, upload } = runtime(null);
    await assert.rejects(upload(image, { imageUploadUrl: config.imageUploadUrl }), /Security token expired/);
    assert.equal(calls.length, 0);
});

test('reports a rejected upload without returning an image URL', async () => {
    const { upload } = runtime('stale-token', { message: 'Security token expired.' }, false);
    await assert.rejects(upload(image, config), /Security token expired/);
});
