# Pursuit Secrets

Pursuit Secrets is Pursuit's branded deployment of
[PrivateBin](https://privatebin.info/), a zero-knowledge paste service. Message
contents are encrypted and decrypted in the browser; the server stores only the
encrypted payload. The decryption key is carried in the URL fragment and is not
sent to the server.

## Deployment shape

- Candidate PrivateBin release: `2.1.0` with the two focused backports in
  `deploy/patches/`, pending upstream `2.1.1`
- Runtime: the official `privatebin/nginx-fpm-alpine` image, pinned by release
  and multi-platform digest
- Render service: Docker web service, Starter plan, Oregon region, one instance
- Persistent storage: 1 GB disk mounted at `/srv/data`
- Complete encrypted JSON request limit: 50 MB (approximately 35-40 MB for typical binary
  attachments after browser-side encoding and encryption)
- Health check: `GET /`
- Container port: `8080`

The child image overlays only the Pursuit configuration, adapted Bootstrap 5
template, CSS, and mark. The candidate also applies a request-size backport and
guards for disabled optional frontend controls. It does not change encryption.

The application files used at runtime come from the pinned image plus these
patches. The historical upstream source elsewhere in this checkout is not
copied into the image. Run deployment checks against the built image; the old
checkout's unit tests do not validate the upgraded deployment.

The persistent disk is required. Without it, encrypted pastes, server salt,
traffic-limiter state, and purge state disappear on restart or deploy. Render
disks also make this a single-instance service and disable zero-downtime deploys.

The child image raises Nginx and PHP request ceilings to 64 MB and PHP's memory
limit to 256 MB. Keep those ceilings above `main.sizelimit` in `cfg/conf.php`.
The 50 MB PrivateBin limit now includes JSON metadata and encoding overhead,
not just `ct`. A ciphertext at exactly the old limit will no longer fit. The
limit is deliberately below the proxy and runtime limits; larger attachments
should use a storage flow designed for chunked uploads.

## October 2026 security candidate

This is a local review candidate, not a deployed change. The base manifest is
`sha256:0f80fb240c3c3073c53a82893da7c7d62f03f4e05403855b720e4371541ba412`.
Local arm64 and linux/amd64 builds passed the PHP `8.5.12` floor, PHP syntax,
and PHP-FPM configuration checks as the runtime user. The amd64 checks ran
locally under emulation; no Render build or deployment was performed.

- PHP `8.5.12` fixes [CVE-2026-109618 and CVE-2026-109619](https://www.php.net/archive/2026.php#2026-10-21-5).
- `session.upload_progress.enabled=0` follows PHP's October 23 advice. This
  image does not load the session extension; the setting protects a future
  image that adds it. Browser-side encrypted attachments do not need it.
- `request-size.patch` backports the full request-byte check and four-element
  paste metadata check from upstream's October 28 `master` source, addressing
  [GHSA-9rmv-j9gp-6vc5](https://github.com/PrivateBin/PrivateBin/security/advisories/GHSA-9rmv-j9gp-6vc5).
  The advisory lists `<=2.0.6` as affected and `2.1.1` as fixed; the regression
  reproduces the issue in `2.1.0` as well. Actual body length is checked before
  format validation and storage. Like the upstream backport, this still parses
  JSON before applying the application limit; Nginx's 64 MiB cap remains the
  outer request bound. This does not prevent all forms of storage exhaustion.
- `optional-controls.patch` backports upstream guards needed with discussion
  and email disabled. Its modified `privatebin.js` keeps SRI protection via
  the override in `cfg/conf.php`. Update that hash whenever the patch changes.
- The adapted template loads `purify-3.4.17.js` and removes the obsolete jQuery
  reference, matching the released assets.

The build applies patches without fuzz and removes the patch tool afterward.
When `2.1.1` is released, replace the base manifest, remove the backports and
their SRI override when upstream includes them, and rerun these checks.

## Local verification

```sh
docker build --tag pursuit-secrets:security-review .
docker run --rm --entrypoint php \
  --volume "$PWD/deploy/tests:/tests:ro" \
  pursuit-secrets:security-review /tests/security.php
```

This checks the actual image's PHP floor, upload-progress setting, all SRI
hashes, oversized paste/comment metadata over POST/PUT/DELETE, and the exact
full-request byte boundary. `deploy/tests/http_smoke.py` compares actual HTTP
storage behavior using disposable localhost instances with `sizelimit=1024`
and `[traffic] limit=0`; pass `--baseline` for the unpatched `2.1.0` image.
The bundled Nginx rejects PUT/DELETE with HTTP 405, so the HTTP test uses POST;
the PHP regression also covers those methods at the application boundary.
Never apply these reduced test settings to production.

Candidate validation on October 28:

- Both architecture builds and `security.php` passed.
- HTTP baseline stored and re-served oversized outer and nested cipher metadata;
  the candidate rejected both, plus oversized comment metadata. Normal API
  create/read/delete passed on both images.
- Browser creation, reading after container restart, password decryption,
  single-use confirmation, and rejection after consumption passed. Discussion
  and email remained disabled.
- The browser file-picker check did not select the synthetic attachment, so
  attachment upload/preview remains unverified. Complete it before deployment.
- The full upstream test suite was not run; these checks target the actual
  deployment image. Runtime CVE closure is evidenced by the patched PHP package,
  not by attempting remote code execution.

For manual browser checks, use a disposable local volume:

```sh
docker build --tag pursuit-secrets:local .
docker volume create pursuit-secrets-data
docker run --rm --name pursuit-secrets \
  --publish 8080:8080 \
  --volume pursuit-secrets-data:/srv/data \
  pursuit-secrets:local
```

Open `http://localhost:8080`. The insecure-context warning is expected locally;
production must use HTTPS so the browser Web Crypto API and PrivateBin threat
model remain valid.

## Render

`render.yaml` documents the complete production shape and can be checked with:

```sh
render blueprints validate render.yaml
```

The installed Render CLI creates and deploys the web service, but version 2.21
does not expose persistent-disk creation. The disk must therefore be attached
through the Render API or dashboard before the instance is treated as
production-ready. Its mount path must be exactly `/srv/data`.

After a deploy:

1. Confirm the service reports `live` and `GET /` returns 200 over HTTPS.
2. Confirm HTTP redirects to HTTPS.
3. Confirm `Strict-Transport-Security` is present.
4. Create a short-lived paste, open it from its complete URL, and delete it.
5. Restart the service and confirm an unexpired test paste remains readable.

Do not enable Render's "cache all files" option. Dynamic PrivateBin responses
must not be cached. Default edge caching off is safe.

## Security notes

- Users must keep the complete generated link private; its fragment contains
  the decryption key.
- Password protection adds defense when a link may be exposed.
- The configured request limiter reads `X-Forwarded-For` so users do not all
  appear as Render's proxy. This is abuse throttling, not an authentication or
  authorization boundary; edge enforcement is required if stronger rate
  limiting becomes necessary.
- Do not add analytics, third-party scripts, remote fonts, or tag managers. A
  modified frontend can exfiltrate document keys, so same-origin asset delivery
  is part of the security model.

## Updating PrivateBin

The organization fork is `origin`; the official project is `upstream`.

```sh
git fetch upstream
git merge upstream/master
```

For each upstream release:

1. Review PrivateBin's changelog and container release notes.
2. Update both the version and manifest digest in `Dockerfile`.
3. Rebase the adapted `tpl/bootstrap5.php` changes onto the released template.
4. Run the PHP, container, and browser checks.
5. Deploy from a reviewed commit and repeat the live paste/persistence smoke
   test.

Before reopening this service, review the diff and amd64 image, back up the
existing `/srv/data` disk, and finish testing the
Pursuit UI (text, password, attachment, preview, delete, and single-use links).
Deployment and resuming the service are separate, explicitly authorized steps.
Changing the pinned base digest is required: rebuilding the old Dockerfile
cannot pick up a PHP security update.

PrivateBin and its original Bootstrap 5 template remain under the bundled
zlib/libpng license. Pursuit's template modifications are identified in the
template header, and the original project notices remain intact.
