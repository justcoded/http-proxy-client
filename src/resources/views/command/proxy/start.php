<div class="m-1">
    <div class="text-green-600 font-bold">
        <?= config('app.name') ?>
        <?= config('app.version') ?>
    </div>

    <div class="mt-1">
        <div>
            <dl>
                <dt>Channel: </dt>
                <dd><?= $channelUuid ?></dd>
                <dt>Forward url: </dt>
                <dd><?= $forwardUrl ?></dd>
            </dl>
        </div>

        <div class="mt-1">
            <em class="text-lime-500">
                Starting client...
            </em>
        </div>
    </div>
</div>
