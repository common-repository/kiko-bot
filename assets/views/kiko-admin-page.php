<?php
/**
 * kiko-admin-page view.
 * WordPress MVC view.
 *
 * @author 1000° Digital GmbH <https://www.1000grad.de>
 * @package kiko-wp-plugin
 * @version 1.0.0
 */
?>

<div id="wpbody-content" class="kiko-wp-plugin-content">
	<div class="card">
		<div class="kiko-header">
			<img src="<?php echo assets_url( 'img/kiko-header.jpg', __FILE__ ) ?>"/>
		</div>
    <h2><?php _e('Informationen', 'kiko-wp-plugin') ?></h2>
    <p>
      <?php _e('Im Kiko-CMS können Sie Chatbot-Inhalte aus einer Vorlage laden (Start - Vorlagen - Vorlagenpaket importieren) und Antworten hinzufügen.', 'kiko-wp-plugin') ?>
    </p>
    <p>
      CMS:
      <a href="<?php echo $kikoCmsUrl ?>" target="_blank"><?php echo $kikoCmsUrl ?></a>
    </p>
    <p>
      Support:
      <a href="https://support.kiko.bot/" target="_blank">https://support.kiko.bot</a>
    </p>
		<h2><?php _e('Einstellungen', 'kiko-wp-plugin') ?></h2>
		<p>
      <?php _e('Mit dem Chat-Modus <b>widget</b> erscheint ein anklickbares Chatbot-Icon unten rechts auf jeder Webseite. Mit <b>innerHtml</b> und einem Shortcode kann der Webchat stattdessen innerhalb einer oder mehrerer Seiten platziert werden.', 'kiko-wp-plugin') ?>
    </p>
		<p>
			<form method="POST">
				<input type="hidden" name="kiko-wp-plugin-action" value="settings-form-submitted"/>
				<ul>
						<li>
							<label for="chat-integration-type"><?php _e('Chat-Modus: ', 'kiko-wp-plugin') ?></label>
							<select name="chat-integration-type">
								<?php foreach($chatIntegrationTypes as $chatIntegrationType) { ?>
									<option <?php echo $formData['chat-integration-type'] === $chatIntegrationType ? 'selected' : '' ?> value="<?php echo $chatIntegrationType ?>"><?php echo $chatIntegrationType ?></option>
								<?php } ?>
							</select>
							<?php if($formData['chat-integration-type'] === 'innerHtml') { ?>
								<p><?php _e('Mit dem Shortcode <b>[kiko-wp-plugin-chat]</b> kann genau ein Webchat auf einer Wordpress-Seite platziert werden.', 'kiko-wp-plugin') ?></p>
								<p><?php _e('Styles des Webchats können dabei im Shortcode hinzugefügt werden. <b>[kiko-wp-plugin-chat style="width:500px; height: 400px;"]</b>', 'kiko-wp-plugin') ?></p>
							<?php } ?>
						</li>
						<li>
							<label for="bot-language"><?php _e('Chatbot-Sprache: ', 'kiko-wp-plugin') ?></label>
							<select name="bot-language">
								<?php foreach($botLanguages as $botLanguage) { ?>
									<option <?php echo $formData['bot-language'] === $botLanguage ? 'selected' : '' ?> value="<?php echo $botLanguage ?>"><?php echo $botLanguage ?></option>
								<?php } ?>
							</select>
						</li>
				</ul>
				<button type="submit" class="button button-primary"><?php _e('Speichern', 'kiko-wp-plugin') ?></button>
			</form>
		</p>
		<hr/>
		<h2><?php _e('Plugin zurücksetzen', 'kiko-wp-plugin') ?></h2>
		<p><?php _e('Durch Klick des nachfolgenden Button wird das Plugin auf die ursprünglichen Einstellungen zurückgesetzt. Achtung, Sie müssen sich anschließend neu beim Kiko-CMS anmelden.', 'kiko-wp-plugin') ?></p>
		<form method="POST">
			<input type="hidden" name="kiko-wp-plugin-action" value="kiko-plugin-reset"/>
			<button type="submit" class="button button-secondary"><?php _e('Einstellungen zurücksetzen', 'kiko-wp-plugin') ?></button>
		</form>
	</div>
	<div class="clear"></div>
</div>