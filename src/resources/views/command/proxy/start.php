<?php
/**
 * @var string $channelUuid
 * @var string $webhookUrl
 * @var string $forwardUrl
 */

?>

<div class="m-1">
    <p class="text-green-600 font-bold">
        <?= config('app.name') ?> [<?= config('app.version') ?>]
    </p>

    <dl>
        <dt>Channel UUID:</dt>
        <dd><?= $channelUuid ?></dd>
        <dt>Webhook URL:</dt>
        <dd><?= $webhookUrl ?></dd>
        <dt>Forward URL:</dt>
        <dd><?= $forwardUrl ?></dd>
    </dl>
</div>
