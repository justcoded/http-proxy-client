<?php
/**
 * @var RequestData $requestData
 * @var CarbonInterface $timestamp
 * @var ResponseInterface $response
 * @var string $forwardedInSeconds
 * @var string $requestUrl
 */

use App\Proxy\RequestData;
use Carbon\CarbonInterface;
use Psr\Http\Message\ResponseInterface;

$errorResponse = $response->getStatusCode() >= 400;
?>

<table class="mt-1">
    <thead>
    <tr>
        <th>Request origin</th>
        <th>Forwarded in (seconds)</th>
        <th>Forwarding status</th>
        <th>Webhook URL</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?= $requestData->method ?> <?= $requestData->headers['host'] ?></td>
        <td><?= $forwardedInSeconds ?></td>
        <td><?= $response->getStatusCode() ?> <?= $response->getReasonPhrase() ?></td>
        4
        <td>
            <a href="<?= $requestUrl ?>">
                <?= $requestUrl ?>
            </a>
        </td>
    </tr>
    <?php if ($errorResponse): ?>
        <tr>
            <td colspan="4">
                <pre class="text-red-500"><?= $response->getBody() ?></pre>
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
