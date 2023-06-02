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
        <td>
            <a href="<?=$requestUrl ?>">
                <?=$requestUrl ?>
            </a>
        </td>
    </tr>
    </tbody>
</table>
