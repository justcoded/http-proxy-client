<?php

declare(strict_types=1);

namespace App\Sockets;

use Closure;
use Evenement\EventEmitterInterface;
use Evenement\EventEmitterTrait;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ratchet\RFC6455\Messaging\CloseFrameChecker;
use Ratchet\RFC6455\Messaging\Frame;
use Ratchet\RFC6455\Messaging\FrameInterface;
use Ratchet\RFC6455\Messaging\MessageBuffer;
use Ratchet\RFC6455\Messaging\MessageInterface;
use React\Socket\ConnectionInterface;
use UnderflowException;

class WebSocket implements EventEmitterInterface
{
    use EventEmitterTrait;

    protected Closure $close;

    public function __construct(
        protected ConnectionInterface $stream,
        public readonly ResponseInterface $response,
        public readonly RequestInterface $request,
    ) {
        $self = $this;

        $this->close = function ($code = null, $reason = null) use ($self) {
            static $sent = false;

            if ($sent) {
                return;
            }

            $sent = true;

            $self->emit('close', [$code, $reason, $self]);
        };

        $reusableUAException = new UnderflowException();

        $streamer = new MessageBuffer(
            new CloseFrameChecker(),
            function (MessageInterface $msg) {
                $this->emit('message', [$msg, $this]);
            },
            function (FrameInterface $frame) use (&$streamer) {
                switch ($frame->getOpcode()) {
                    case Frame::OP_CLOSE:
                        $frameContents = $frame->getPayload();

                        $reason = '';
                        $code = unpack('n', substr($frameContents, 0, 2));
                        $code = reset($code);

                        if (($frameLen = strlen($frameContents)) > 2) {
                            $reason = substr($frameContents, 2, $frameLen);
                        }

                        $closeFn = $this->close;
                        $closeFn($code, $reason);

                        $this->stream->end(
                            $streamer
                                ->newFrame($frame->getPayload(), true, Frame::OP_CLOSE)
                                ->maskPayload()
                                ->getContents(),
                        );
                        break;
                    case Frame::OP_PING:
                        $this->emit('ping', [$frame, $this]);
                        $this->send($streamer->newFrame($frame->getPayload(), true, Frame::OP_PONG));
                        break;
                    case Frame::OP_PONG:
                        $this->emit('pong', [$frame, $this]);
                        break;
                    default:
                        $this->close(Frame::CLOSE_PROTOCOL);
                        break;
                }
            },
            false,
            function () use ($reusableUAException) {
                return $reusableUAException;
            }
        );

        $stream->on('data', [$streamer, 'onData']);

        $stream->on('close', function () {
            $close = $this->close;
            $close(Frame::CLOSE_ABNORMAL, 'Underlying connection closed');
        });

        $stream->on('error', function ($error) {
            $this->emit('error', [$error, $this]);
        });

        $stream->on('drain', function () {
            $this->emit('drain');
        });
    }

    public function send($msg): bool
    {
        if ($msg instanceof MessageInterface) {
            foreach ($msg as $frame) {
                $frame->maskPayload();
            }
        } else {
            if (! $msg instanceof Frame) {
                $msg = new Frame($msg);
            }
            $msg->maskPayload();
        }

        return $this->stream->write($msg->getContents());
    }

    public function close($code = 1000, $reason = ''): void
    {
        $frame = new Frame(pack('n', $code) . $reason, true, Frame::OP_CLOSE);
        $frame->maskPayload();
        $this->stream->write($frame->getContents());

        $closeFn = $this->close;
        $closeFn($code, $reason);

        $this->stream->end();
    }

    public function pause(): void
    {
        $this->stream->pause();
    }

    public function resume(): void
    {
        $this->stream->resume();
    }
}
