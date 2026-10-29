;<?php http_response_code(403); /*
; Pursuit's PrivateBin configuration.

[main]
name = "Pursuit Secrets"
basepath = "https://pursuit-secrets.onrender.com/"
discussion = false
password = true
fileupload = true
burnafterreadingselected = true
defaultformatter = "plaintext"
sizelimit = 50000000
templateselection = false
template = "bootstrap5"
info = "Powered by PrivateBin 2.1.0 with Pursuit security backports."
notice = "Only people with the generated link will be able to see this message."
languageselection = false
qrcode = true
email = false
httpwarning = true
compression = "zlib"

[expire]
default = "1day"

[expire_options]
5min = 300
1hour = 3600
1day = 86400
1week = 604800
1month = 2592000

[formatter_options]
plaintext = "Plain Text"
syntaxhighlighting = "Source Code"
markdown = "Markdown"

[traffic]
limit = 10
header = "X_FORWARDED_FOR"

[purge]
limit = 300
batchsize = 10

[model]
class = Filesystem

[model_options]
dir = PATH "data"

; Keep SRI enabled for the optional-controls backport in the built image.
[sri]
js/privatebin.js = "sha512-yYyAIdSfYpOz7fZ3/g4unChe8/ltEZxY3mxZLOjEcrZpF2wNB0yGelFCI5XNtdn3omBInOjxQ9EPJDXY1N6Gww=="
