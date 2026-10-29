#!/usr/bin/env python3
"""Exercise an isolated instance configured with sizelimit=1024, traffic.limit=0.

Usage: python3 deploy/tests/http_smoke.py http://127.0.0.1:18984 [--baseline]
The baseline mode demonstrates the metadata bypass in upstream 2.1.0.
"""
import base64
import json
import os
import sys
import urllib.parse
import urllib.request

base = sys.argv[1].rstrip('/') + '/'
if urllib.parse.urlparse(base).hostname not in ('localhost', '127.0.0.1', '::1'):
    raise SystemExit('This smoke test is restricted to localhost.')
baseline = '--baseline' in sys.argv[2:]


def paste():
    return {
        'v': 2,
        'adata': [
            ['gMSNoLOk4z0RnmsYwXZ8mw==', 'TZO+JWuIuxs=', 100000, 256, 128, 'aes', 'gcm', 'zlib'],
            'plaintext', 0, 0,
        ],
        'meta': {'expire': '5min'},
        'ct': base64.b64encode(os.urandom(32)).decode(),
    }


def request(method, data=None, query=''):
    body = None if data is None else json.dumps(data).encode()
    req = urllib.request.Request(base + query, data=body, method=method, headers={
        'Content-Type': 'application/json',
        'X-Requested-With': 'JSONHttpRequest',
    })
    with urllib.request.urlopen(req, timeout=15) as response:
        return json.load(response)


for slot in ('outer', 'cipher', 'comment'):
    data = paste()
    if slot == 'outer':
        data['adata'].append('A' * 10000)
    else:
        data['adata'][0].append('B' * 10000)
    if slot == 'comment':
        if baseline:
            continue  # Discussions are disabled in the target configuration.
        data['adata'] = data['adata'][0]
        del data['meta']
        data['pasteid'] = data['parentid'] = '5b65a01b43987bc2'
    response = request('POST', data)
    if baseline:
        assert response['status'] == 0, ('POST', slot, response)
        stored = request('GET', query='?' + response['id'])
        assert stored['adata'] == data['adata'], 'Baseline did not persist junk'
        request('POST', {'pasteid': response['id'], 'deletetoken': response['deletetoken']})
    else:
        assert response['status'] == 1 and 'limited' in response['message'], ('POST', slot, response)
    print(('REPRODUCED' if baseline else 'REJECTED'), 'POST', slot)

data = paste()
created = request('POST', data)
assert created['status'] == 0, created
stored = request('GET', query='?' + created['id'])
assert stored['status'] == 0 and stored['ct'] == data['ct'], stored
deleted = request('POST', {'pasteid': created['id'], 'deletetoken': created['deletetoken']})
assert deleted['status'] == 0, deleted
assert request('GET', query='?' + created['id'])['status'] == 1
print('PASS: valid encrypted paste stored, read back unchanged, deleted, and no longer readable.')
