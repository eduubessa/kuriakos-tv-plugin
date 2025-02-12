<div class="wrap">
    <h1>Kuriakos TV - Definições</h1>
    <hr/>
    <form method="post" action="options.php" novalidate="novalidate">
        <?php

        settings_fields('ktv_bot_options');
        do_settings_sections('ktv_bot_settings');

        ?>
        <h4>Telegram bot</h4>
        <table class="form-table" role="presentation">
            <tbody>
            <tr>
                <th scope="row"><label for="username">Nome do Bot</label></th>
                <td><input name="telegram_bot_name" type="text" id="telegram_bot_name" value="" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="username">API Token</label></th>
                <td><input name="telegram_bot_token" type="text" id="telegram_bot_token" value="" class="regular-text"></td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p></form>
</div>