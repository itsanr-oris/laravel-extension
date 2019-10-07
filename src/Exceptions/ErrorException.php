<?php

namespace Foris\LaExtension\Exceptions;

use Throwable;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

/**
 * Class ErrorException
 */
class ErrorException extends BaseException
{
    /**
     * ErrorException constructor.
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     * @param array          $data
     */
    public function __construct(
        string $message = "",
        int $code = FoundationResponse::HTTP_INTERNAL_SERVER_ERROR,
        Throwable $previous = null,
        array $data = []
    ) {
        parent::__construct($message, $code, $previous, $data);
    }

    /**
     * 获取异常数据
     *
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        $previous = $this->getPrevious();

        if ($previous
            && env('APP_DEBUG', false)) {
            $data['previous'] = get_class($previous);
        }

        return $data;
    }
}