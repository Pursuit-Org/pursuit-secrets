<?php declare(strict_types=1);
/**
 * Pursuit theme, adapted from PrivateBin 2.1.0's Bootstrap 5 template.
 * PrivateBin is distributed under the zlib/libpng license; see LICENSE.md.
 */
use PrivateBin\I18n;
?><!DOCTYPE html>
<html lang="<?php echo I18n::getLanguage(); ?>"<?php echo I18n::isRtl() ? ' dir="rtl"' : ''; ?> class="h-100">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="Content-Security-Policy" content="<?php echo I18n::encode($CSPHEADER); ?>">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="noindex" />
		<meta name="google" content="notranslate">
		<title><?php echo I18n::_($NAME); ?></title>
		<link type="text/css" rel="stylesheet" href="css/bootstrap5/bootstrap<?php echo I18n::isRtl() ? '.rtl' : ''; ?>-5.3.8.css" />
		<link type="text/css" rel="stylesheet" href="css/bootstrap5/privatebin.css?<?php echo rawurlencode($VERSION); ?>" />
		<link type="text/css" rel="stylesheet" href="css/pursuit.css?<?php echo rawurlencode($VERSION); ?>-pursuit-16" />
<?php
if ($SYNTAXHIGHLIGHTING) :
?>
		<link type="text/css" rel="stylesheet" href="css/prettify/prettify.css?<?php echo rawurlencode($VERSION); ?>" />
<?php
    if (!empty($SYNTAXHIGHLIGHTINGTHEME)) :
?>
		<link type="text/css" rel="stylesheet" href="css/prettify/<?php echo rawurlencode($SYNTAXHIGHLIGHTINGTHEME); ?>.css?<?php echo rawurlencode($VERSION); ?>" />
<?php
    endif;
endif;
?>
		<noscript><link type="text/css" rel="stylesheet" href="css/noscript.css" /></noscript>
		<?php $this->_linkTag('js/zlib-1.3.2.js'); ?>
<?php
if ($QRCODE) :
?>
		<?php $this->_scriptTag('js/kjua-0.10.0.js', 'defer'); ?>
<?php
endif;
?>
		<?php $this->_scriptTag('js/zlib.js', 'defer'); ?>
		<?php $this->_scriptTag('js/base-x-5.0.1.js', 'defer'); ?>
		<?php $this->_scriptTag('js/bootstrap-5.3.8.js', 'defer'); ?>
<?php
if ($SYNTAXHIGHLIGHTING) :
?>
		<?php $this->_scriptTag('js/prettify.js', 'defer'); ?>
<?php
endif;
if ($MARKDOWN) :
?>
		<?php $this->_scriptTag('js/showdown-2.1.0.js', 'defer'); ?>
<?php
endif;
?>
		<?php $this->_scriptTag('js/purify-3.4.17.js', 'defer'); ?>
		<?php $this->_scriptTag('js/legacy.js', 'defer'); ?>
		<?php $this->_scriptTag('js/privatebin.js', 'defer'); ?>
		<script defer type="text/javascript" data-cfasync="false" src="js/pursuit.js?<?php echo rawurlencode($VERSION); ?>-pursuit-15" crossorigin="anonymous"></script>
		<!-- icon -->
		<link rel="icon" type="image/svg+xml" href="img/pursuit-mark.svg?<?php echo rawurlencode($VERSION); ?>-pursuit-11" />
		<link rel="manifest" href="manifest.json?<?php echo rawurlencode($VERSION); ?>" />
		<meta name="theme-color" content="#4242EA" />
		<!-- Twitter/social media cards -->
		<meta name="twitter:card" content="summary" />
		<meta name="twitter:title" content="<?php echo I18n::_('Encrypted note on %s', I18n::_($NAME)) ?>" />
		<meta name="twitter:description" content="<?php echo I18n::_('Visit this link to see the note. Giving the URL to anyone allows them to access the note, too.') ?>" />
		<meta name="twitter:image" content="<?php echo I18n::encode($BASEPATH); ?>img/apple-touch-icon.png" />
		<meta property="og:title" content="<?php echo I18n::_($NAME); ?>" />
		<meta property="og:site_name" content="<?php echo I18n::_($NAME); ?>" />
		<meta property="og:description" content="<?php echo I18n::_('Visit this link to see the note. Giving the URL to anyone allows them to access the note, too.') ?>" />
		<meta property="og:image" content="<?php echo I18n::encode($BASEPATH); ?>img/apple-touch-icon.png" />
		<meta property="og:image:type" content="image/png" />
		<meta property="og:image:width" content="180" />
		<meta property="og:image:height" content="180" />
	</head>
	<body role="document" data-compression="<?php echo rawurlencode($COMPRESSION); ?>" class="pursuit-theme d-flex flex-column h-100">
		<div id="passwordmodal" tabindex="-1" class="modal fade" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-body">
						<form id="passwordform" role="form">
							<div class="mb-3">
								<label for="passworddecrypt"><svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#eye" /></svg> <?php echo I18n::_('Please enter the password for this document:') ?></label>
								<div class="input-group">
									<input id="passworddecrypt" type="password" class="form-control input-password" placeholder="<?php echo I18n::_('Enter password') ?>" required="required" />
									<button class="btn btn-outline-secondary toggle-password" type="button" title="<?php echo I18n::_('Show password'); ?>" aria-label="<?php echo I18n::_('Show password'); ?>">
										<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#eye" /></svg>
									</button>
								</div>
							</div>
							<button type="submit" class="btn btn-success btn-block"><svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#power" /></svg> <?php echo I18n::_('Decrypt') ?></button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div id="loadconfirmmodal" tabindex="-1" class="modal fade" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title"><?php echo I18n::_('This secret message can only be displayed once. Would you like to see it now?') ?></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo I18n::_('Close') ?>"></button>
					</div>
					<div class="modal-body text-center">
						<button id="loadconfirm-open-now" type="button" class="btn btn-success" data-bs-dismiss="modal"><?php echo I18n::_('Yes, see it') ?></button>
					</div>
				</div>
			</div>
		</div>
<?php
if ($QRCODE) :
?>
		<div id="qrcodemodal" tabindex="-1" class="modal fade" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title"><?php echo I18n::_('QR code') ?></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo I18n::_('Close') ?>"></button>
					</div>
					<div class="modal-body">
						<div class="mx-auto" id="qrcode-display"></div>
					</div>
				</div>
			</div>
		</div>
<?php
endif;
if ($EMAIL) :
?>
		<div id="emailconfirmmodal" tabindex="-1" class="modal fade" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title"><?php echo I18n::_('Recipient may become aware of your timezone, convert time to UTC?') ?></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo I18n::_('Close') ?>"></button>
					</div>
					<div class="modal-body row">
						<div class="col-xs-12 col-md-6">
							<button id="emailconfirm-timezone-current" type="button" class="btn btn-danger"><svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#clock" /></svg> <?php echo I18n::_('Use Current Timezone') ?></button>
						</div>
						<div class="col-xs-12 col-md-6 text-right">
							<button id="emailconfirm-timezone-utc" type="button" class="btn btn-success"><svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#globe" /></svg> <?php echo I18n::_('Convert To UTC') ?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php
endif;
?>
		<nav class="navbar navbar-expand-lg bg-body-tertiary text-nowrap mb-3">
			<div class="container-fluid">
				<a class="reloadlink navbar-brand" href="">
					<img alt="" src="img/pursuit-profile-purple.png?<?php echo rawurlencode($VERSION); ?>-pursuit-15" width="48" height="48" />
					<span>Pursuit <em>Secrets</em></span>
				</a>
				<div id="navbar" class="pursuit-settings-panel">
					<ul class="navbar-nav pursuit-settings-list me-auto gap-2 align-items-stretch">
						<li id="loadingindicator" class="navbar-text hidden me-auto">
							<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#clock" /></svg>
							<?php echo I18n::_('Loading…'), PHP_EOL; ?>
						</li>
						<li class="nav-item d-flex flex-lg-row flex-column">
							<button id="retrybutton" type="button" class="reloadlink hidden btn btn-primary d-flex justify-content-center align-items-center gap-1">
								<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#repeat" /></svg> <?php echo I18n::_('Retry'), PHP_EOL; ?>
							</button>
						</li>
						<li class="nav-item pursuit-option--actions d-flex flex-lg-row flex-column gap-2">
							<button id="newbutton" type="button" class="hidden btn btn-secondary flex-fill d-flex justify-content-center align-items-center gap-1">
								<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#file-earmark" /></svg> <?php echo I18n::_('Start over'), PHP_EOL; ?>
							</button>
							<button id="clonebutton" type="button" class="hidden btn btn-secondary flex-fill d-flex justify-content-center align-items-center gap-1">
								<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#copy" /></svg> <?php echo I18n::_('Clone'), PHP_EOL; ?>
							</button>
							<button id="rawtextbutton" type="button" class="hidden btn btn-secondary flex-fill d-flex justify-content-center align-items-center gap-1">
								<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#filetype-txt" /></svg> <?php echo I18n::_('Raw text'), PHP_EOL; ?>
							</button>
							<button id="downloadtextbutton" type="button" class="hidden btn btn-secondary flex-fill d-flex justify-content-center align-items-center gap-1">
								<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#download" /></svg> <?php echo I18n::_('Save document'), PHP_EOL; ?>
							</button>
<?php
if ($EMAIL) :
?>

							<button id="emaillink" type="button" class="hidden btn btn-secondary flex-fill d-flex justify-content-center align-items-center gap-1">
								<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#envelope" /></svg> <?php echo I18n::_('Email'), PHP_EOL; ?>
							</button>
<?php
endif;
if ($QRCODE) :
?>
							<button id="qrcodelink" type="button" data-bs-toggle="modal" data-bs-target="#qrcodemodal" class="hidden btn btn-secondary flex-fill d-flex justify-content-center align-items-center gap-1">
								<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#qr-code" /></svg> <?php echo I18n::_('QR code'), PHP_EOL; ?>
							</button>
<?php
endif;
?>
						</li>
						<li id="expiration" class="nav-item pursuit-option pursuit-option--expiration d-flex hidden">
							<label for="pasteExpiration" class="form-label my-auto me-1"><?php echo I18n::_('Expires in'); ?></label>
							<select id="pasteExpiration" name="pasteExpiration" class="form-select">
<?php
foreach ($EXPIRE as $key => $value) :
?>
								<option value="<?php echo $key; ?>"<?php
    if ($key === $EXPIREDEFAULT) :
?> selected="selected"<?php
    endif;
?>><?php echo $value; ?></option>
<?php
endforeach;
?>
							</select>
						</li>
						<li class="nav-item pursuit-option pursuit-option--burn">
							<div id="burnafterreadingoption" class="navbar-text form-check hidden">
								<input class="form-check-input" type="checkbox" id="burnafterreading" name="burnafterreading"<?php
if ($BURNAFTERREADINGSELECTED) :
?> checked="checked"<?php
endif;
?> />
								<label class="form-check-label" for="burnafterreading">
									<?php echo I18n::_('Delete after it’s opened'), PHP_EOL; ?>
								</label>
							</div>
						</li>
<?php
if ($DISCUSSION) :
?>
						<li class="nav-item">
							<div id="opendiscussionoption" class="navbar-text form-check hidden">
								<input class="form-check-input" type="checkbox" id="opendiscussion" name="opendiscussion"<?php
	if ($OPENDISCUSSION) :
?> checked="checked"<?php
	endif;
?> />
								<label class="form-check-label" for="opendiscussion">
									<?php echo I18n::_('Open discussion'), PHP_EOL; ?>
								</label>
							</div>
						</li>
<?php
endif;
if ($PASSWORD) :
?>
						<li class="nav-item pursuit-option pursuit-option--password">
							<div id="password" class="navbar-form hidden">
								<div class="input-group">
									<input type="password" id="passwordinput" placeholder="<?php echo I18n::_('Add a password (optional)'); ?>" aria-label="<?php echo I18n::_('Add a password (optional)'); ?>" class="form-control input-password" size="23" />
									<button class="btn btn-outline-secondary toggle-password" type="button" title="<?php echo I18n::_('Show password'); ?>" aria-label="<?php echo I18n::_('Show password'); ?>">
										<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#eye" /></svg>
									</button>
								</div>
							</div>
						</li>
<?php
endif;
if ($FILEUPLOAD) :
?>
						<li id="attach" class="nav-item pursuit-option pursuit-option--attach hidden dropdown">
							<a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false"><?php echo I18n::_('Attach a file'); ?></a>
							<ul class="dropdown-menu px-2">
								<li id="filewrap">
									<div>
										<input type="file" id="file" name="file" class="form-control" multiple />
									</div>
									<div id="dragAndDropFileName" class="dragAndDropFile"><?php echo I18n::_('alternatively drag & drop a file or paste an image from the clipboard'); ?></div>
								</li>
								<li id="customattachment" class="hidden d-flex flex-column px-3"></li>
								<li>
									<a id="fileremovebutton" href="#" class="dropdown-item">
										<?php echo I18n::_('Remove attachment'), PHP_EOL; ?>
									</a>
								</li>
							</ul>
						</li>
<?php
endif;
?>
						<li id="formatter" class="nav-item pursuit-option pursuit-option--formatter d-flex hidden">
							<label for="pasteFormatter" class="form-label my-auto me-1"><?php echo I18n::_('Format'); ?></label>
							<select id="pasteFormatter" name="pasteFormatter" class="form-select">
<?php
    foreach ($FORMATTER as $key => $value) :
?>
								<option value="<?php echo $key; ?>"<?php
        if ($key === $FORMATTERDEFAULT) :
?> selected="selected"<?php
        endif;
?>><?php echo $value; ?></option>
<?php
    endforeach;
?>
							</select>
						</li>
					</ul>
					<ul class="navbar-nav pursuit-global-controls gap-2">
<?php
if (!empty($LANGUAGESELECTION)) :
?>
						<li id="language" class="nav-item dropdown">
							<a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">
								<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#flag" /></svg> <?php echo $LANGUAGES[$LANGUAGESELECTION][0], PHP_EOL; ?>
							</a>
							<ul class="dropdown-menu dropdown-menu-end" role="menu">
<?php
    foreach ($LANGUAGES as $key => $value) :
?>
								<li>
									<a href="#" class="dropdown-item" data-lang="<?php echo $key; ?>">
										<?php echo $value[0]; ?> (<?php echo $value[1]; ?>)
									</a>
								</li>
<?php
    endforeach;
?>
							</ul>
						</li>
<?php
endif;
?>
<?php
if (!empty($TEMPLATESELECTION)) :
?>
						<li id="template" class="nav-item dropdown">
							<a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-1" data-bs-toggle="dropdown" role="button" aria-expanded="false">
								<?php echo I18n::_('Theme'); ?>: <?php echo $TEMPLATESELECTION, PHP_EOL; ?>
							</a>
							<ul class="dropdown-menu dropdown-menu-end" role="menu">
<?php
    foreach ($TEMPLATES as $value) :
?>
								<li>
									<a href="#" class="dropdown-item" data-template="<?php echo $value; ?>">
										<?php echo $value; ?>
									</a>
								</li>
<?php
    endforeach;
?>
							</ul>
						</li>
<?php
endif;
?>
					</ul>
				</div>
			</div>
		</nav>
		<main>
			<section class="container-fluid mt-2">
				<header class="pursuit-intro">
					<div class="pursuit-intro__title">
						<p class="pursuit-intro__eyebrow">Private sharing</p>
						<h1>Share a secret</h1>
					</div>
					<p class="pursuit-intro__note">Don't share secrets, tokens, or sensitive data in Slack. Share a disappearing message instead.</p>
<?php
if (!empty($NOTICE)) :
?>
					<div role="note" class="pursuit-intro__notice">
						<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#info-circle" /></svg>
						<?php echo I18n::encode($NOTICE), PHP_EOL; ?>
					</div>
<?php
endif;
?>
				</header>
				<div id="remainingtime" role="alert" class="hidden alert alert-info">
					<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#fire" /></svg>
				</div>
<?php
if ($FILEUPLOAD) :
?>
				<div id="attachment" class="hidden"></div>
<?php
endif;
?>
				<div id="status" role="alert" class="d-flex align-items-center gap-2 alert alert-<?php echo $ISDELETED ? 'success' : 'info'; echo empty($STATUS) ? ' hidden' : '' ?>">
					<div>
						<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#info-circle" /></svg>
						<?php echo I18n::encode($STATUS), PHP_EOL; ?>
					</div>
<?php
if ($ISDELETED) :
?>
					<button type="button" class="btn btn-secondary d-flex justify-content-center align-items-center gap-1 ms-auto" id="new-from-alert">
						<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#repeat" /></svg>
						<?php echo I18n::_('Start over'), PHP_EOL; ?>
					</button>
<?php
endif;
?>
				</div>
				<div id="errormessage" role="alert" class="<?php echo empty($ERROR) ? 'hidden' : '' ?> alert alert-danger">
					<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#exclamation-triangle" /></svg>
					<?php echo I18n::encode($ERROR), PHP_EOL; ?>
				</div>
				<noscript>
					<div id="noscript" role="alert" class="alert alert-warning">
						<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#exclamation-circle" /></svg>
						<?php echo I18n::_('JavaScript is required for %s to work. Sorry for the inconvenience.', I18n::_($NAME)), PHP_EOL; ?>
					</div>
				</noscript>
				<div id="oldnotice" role="alert" class="hidden alert alert-danger">
					<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#exclamation-triangle" /></svg>
					<?php echo I18n::_('%s requires a modern browser to work.', I18n::_($NAME)), PHP_EOL; ?>
					<a href="https://www.mozilla.org/firefox/">Firefox</a>,
					<a href="https://www.opera.com/">Opera</a>,
					<a href="https://www.google.com/chrome">Chrome</a>…<br />
					<span class="small"><?php echo I18n::_('For more information <a href="%s">see this FAQ entry</a>.', 'https://github.com/PrivateBin/PrivateBin/wiki/FAQ#why-does-it-show-me-the-error-privatebin-requires-a-modern-browser-to-work'); ?></span>
				</div>
<?php
if ($HTTPWARNING) :
?>
				<div id="httpnotice" role="alert" class="hidden alert alert-danger">
					<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#exclamation-triangle" /></svg>
					<?php echo I18n::_('This website is using an insecure connection! Please only use it for testing.'); ?><br />
					<span class="small"><?php echo I18n::_('For more information <a href="%s">see this FAQ entry</a>.', 'https://github.com/PrivateBin/PrivateBin/wiki/FAQ#why-does-it-show-me-an-error-about-an-insecure-connection'); ?></span>
				</div>
				<div id="insecurecontextnotice" role="alert" class="hidden alert alert-danger">
					<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#exclamation-triangle" /></svg>
					<?php echo I18n::_('Your browser may require an HTTPS connection to support the WebCrypto API. Try <a href="%s">switching to HTTPS</a>.', $HTTPSLINK), PHP_EOL; ?>
				</div>
<?php
endif;
?>
				<div id="pastesuccess" class="hidden">
					<div class="nav justify-content-between mb-2">
						<button id="copyLink" type="button" class="btn btn-secondary d-flex justify-content-center align-items-center gap-1">
							<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#copy" /></svg> <?php echo I18n::_('Copy link') ?>
						</button>
						<a href="#" id="deletelink" class="btn btn-secondary d-flex justify-content-center align-items-center gap-1">
							<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#trash" /></svg>
							<span></span>
						</a>
					</div>
					<div role="alert" class="alert alert-success">
						<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#check" /></svg>
						<div id="pastelink"></div>
					</div>
<?php
if (!empty($URLSHORTENER)) :
?>
					<p>
						<button id="shortenbutton" data-shortener="<?php echo I18n::encode($URLSHORTENER); ?>"
								<?php if ($SHORTENBYDEFAULT) : ?>
								data-autoshorten="true"
								<?php endif; ?>
								type="button" class="btn btn-primary btn-block d-flex justify-content-center align-items-center gap-1"
						>
							<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#send" /></svg> <?php echo I18n::_('Shorten URL'), PHP_EOL; ?>
						</button>
					</p>
					<div role="alert" class="alert alert-danger">
						<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#exclamation-circle" /></svg>
						<?php if ($SHORTENBYDEFAULT) : ?>
							<?php echo I18n::_('URL shortener is enabled by default.'), PHP_EOL; ?>
						<?php endif; ?>
						<?php echo I18n::_('URL shortener may expose your decrypt key in URL.'), PHP_EOL; ?>
					</div>
<?php
endif;
?>
				</div>
			</section>
			<section class="container-fluid pursuit-workspace">
				<article class="pursuit-workspace__card">
					<ul id="editorTabs" class="nav nav-tabs hidden">
						<li role="presentation" class="nav-item me-1"><a class="nav-link active" role="tab" id="messageedit" href="#"><?php echo I18n::_('Editor'); ?></a></li>
						<li role="presentation" class="nav-item me-1"><a class="nav-link" role="tab" id="messagepreview" href="#"><?php echo I18n::_('Preview'); ?></a></li>
					</ul>
					<div id="placeholder" class="col-md-12 hidden"><?php echo I18n::_('+++ no document text +++'); ?></div>
					<div id="attachmentPreview" class="col-md-12 text-center hidden"></div>
					<h6 id="copyShortcutHint" class="col-md-12 nav justify-content-between align-items-center mb-2 hidden">
						<small id="copyShortcutHintText" class="d-none d-md-inline">
							<?php
								echo I18n::_("To copy the text press the copy button or use the clipboard shortcut <kbd>%s</kbd>+<kbd>c</kbd>", strtolower(I18n::getCopyHotkey()))
							?>
						</small>
						<button type="button" id="copyShortcutHintBtn" class="btn btn-secondary ms-auto">Copy text</button>
					</h6>
					<div id="prettymessage" class="card col-md-12 hidden">
						<pre id="prettyprint" class="card-body col-md-12 prettyprint linenums:1"></pre>
					</div>
					<div id="plaintext" class="col-md-12 hidden"></div>
					<p class="col-md-12 pursuit-editor"><textarea id="message" name="message" cols="80" rows="25" aria-label="<?php echo I18n::_('Message'); ?>" placeholder="Write or paste your message here…" tabindex="1" class="form-control hidden"></textarea></p>
					<div class="pursuit-compose-controls">
						<div id="pursuit-settings-slot" class="pursuit-settings-slot"></div>
						<div class="pursuit-create-row">
							<p>Create a secret link and send it privately.</p>
							<button id="sendbutton" type="button" tabindex="2" class="hidden btn btn-primary d-flex justify-content-center align-items-center gap-1">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
									<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
									<path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
								</svg> Create link
							</button>
						</div>
					</div>
					<p class="visually-hidden" aria-hidden="true">
						<input id="messagetab" type="checkbox" checked="checked" tabindex="-1" />
					</p>
				</article>
			</section>
			<section class="container-fluid">
				<div id="discussion" class="hidden">
					<h4><?php echo I18n::_('Discussion'); ?></h4>
					<div id="commentcontainer"></div>
				</div>
			</section>
			<section class="container-fluid">
				<div id="noscript" role="alert" class="alert alert-info noscript-hide">
					<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#exclamation-circle" /></svg>
					<?php echo I18n::_('Loading…'); ?><br />
					<span class="small"><?php echo I18n::_('In case this message never disappears please have a look at <a href="%s">this FAQ for information to troubleshoot</a>.', 'https://github.com/PrivateBin/PrivateBin/wiki/FAQ#why-does-the-loading-message-not-go-away'); ?></span>
				</div>
			</section>
		</main>
		<footer class="container-fluid mt-auto">
			<div class="row">
				<p id="aboutbox" class="col-12">
					Pursuit cannot read the messages you share here. Powered by the open-source <a href="https://privatebin.info/" rel="noopener noreferrer">PrivateBin</a> project.
				</p>
			</div>
		</footer>
		<div id="serverdata" class="hidden" aria-hidden="true">
			<div id="templates">
				<article id="commenttemplate" class="comment px-2 pb-3">
					<div class="commentmeta">
						<span class="nickname">name</span>
						<span class="commentdate">0000-00-00</span>
					</div>
					<div class="commentdata">c</div>
					<button class="btn btn-secondary btn-sm"><?php echo I18n::_('Reply'); ?></button>
				</article>
				<p id="commenttailtemplate" class="comment px-2 pb-3">
					<button class="btn btn-secondary btn-sm"><?php echo I18n::_('Add comment'); ?></button>
				</p>
				<div id="replytemplate" class="reply hidden">
					<input type="text" id="nickname" class="form-control my-2" title="<?php echo I18n::_('Optional nickname…'); ?>" placeholder="<?php echo I18n::_('Optional nickname…'); ?>" />
					<textarea id="replymessage" class="replymessage form-control" cols="80" rows="7"></textarea><br />
					<div id="replystatus" role="alert" class="statusmessage hidden alert">
						<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#info-circle" /></svg>
					</div>
					<button id="replybutton" class="btn btn-secondary btn-sm"><?php echo I18n::_('Post comment'); ?></button>
				</div>
				<div id="attachmenttemplate" role="alert" class="hidden alert alert-info">
					<svg width="16" height="16" fill="currentColor" aria-hidden="true"><use href="img/bootstrap-icons.svg#download" /></svg>
					<a class="alert-link"><?php echo I18n::_('Download attachment'); ?><span></span></a>
				</div>
			</div>
		</div>
<?php
if ($FILEUPLOAD) :
?>
		<div id="dropzone" class="hidden" tabindex="-1" aria-hidden="true"></div>
<?php
endif;
?>
	</body>
</html>
