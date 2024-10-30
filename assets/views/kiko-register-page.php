<?php
/**
 * kiko-register-page view.
 * WordPress MVC view.
 *
 * @author 1000° Digital GmbH <https://www.1000grad.de>
* @package kiko-wp-plugin * @version 1.0.0 */ ?>

<div id="wpbody-content" class="kiko-wp-plugin-content">
  <div class="card">
		<div class="kiko-header">
			<img src="<?php echo assets_url( 'img/kiko-header.jpg', __FILE__ ) ?>"/>
		</div>
    <p>
      <?php _e('Ein Kiko Chatbot beantwortet dem Besucher der Website jederzeit die häufig gestellten Fragen. Der Chat kann als Widget oder als Teil einer Webseite eingebunden werden.', 'kiko-wp-plugin') ?>
    </p>
    <p>
      <?php _e('Mit wenigen Schritten können Sie sich für ein neues kostenfreies Chatbot-Konto (Basis-Plan) registrieren.', 'kiko-wp-plugin')?>
    </p>

    <h2><?php _e('Informationen', 'kiko-wp-plugin') ?></h2>
    <p>
      <?php _e('Bei der Registrierung ein neues Kiko-Konto mit Ihrem Chatbot angelegt. Tipps zur Inhaltspflege finden Sie unter <a href="https://support.kiko.bot/">support.kiko.bot</a>', 'kiko-wp-plugin') ?>
    </p>

    <hr/>
    <h1><?php _e('Registrierung', 'kiko-wp-plugin') ?></h1>    
    <h4 class="title"><?php _e('Neues Kiko-Konto anlegen', 'kiko-wp-plugin') ?></h4>
    <p>
      <?php _e('Falls Sie bereits ein Kiko-Konto besitzen und ein neues anlegen möchten, loggen Sie sich im Kiko-CMS vorher aus.', 'kiko-wp-plugin') ?>
    </p>
    <p>
      <?php _e('Ein paar Sekunden nach Abschluss der Registrierung können Sie diese Seite hier neu laden und die Einbindung des Chatbot konfigurieren.', 'kiko-wp-plugin') ?>
    </p>    
    <form method="GET" action="<?php echo $registerUrl ?>" target="_blank">
      <button type="submit" class="button button-primary"><?php _e('Registrieren', 'kiko-wp-plugin') ?></button>
    </form>
    <h4 class="title"><?php _e('Bestehendes Kiko-Konto verwenden', 'kiko-wp-plugin') ?></h4>
    <p>
      <?php _e('Den API-Key finden Sie nach dem <a href="https://cloud02-7c83ec0.prod.1000grad.de/admin/?#/login/">Login</a> im Kiko-CMS im eigenen Profil (Menü oben rechts - Profil - API Key).', 'kiko-wp-plugin') ?>
      <form method="POST">
        <input type="hidden" name="kiko-wp-plugin-action" value="manually-set-api-key"/>
        <input type="text" name="api-key"/>
        <button type="submit" class="button button-primary"><?php _e('API-Key speichern', 'kiko-wp-plugin') ?></button>
      </form>
    </p>
  </div>
  <div class="clear"></div>
</div>
