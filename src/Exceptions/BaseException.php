<?php /** @noinspection PhpUndefinedClassInspection */

namespace Foris\LaExtension\Exceptions;

use Throwable;
use Illuminate\Support\Facades\Log;
use Foris\LaExtension\Http\Facade\Response;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

/**
 * Class Exception
 */
class BaseException extends \Exception
{
    /**
     * 异常携带数据
     *
     * @var array
     */
    protected $data = [];

    /**
     * Exception constructor.
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     * @param array          $data
     */
    public function __construct(
        $message = "",
        $code = null,
        Throwable $previous = null,
        $data = []
    ) {
        $this->setData($data);
        parent::__construct($message, $code, $previous);
    }

    /**
     * 设置异常数据
     *
     * @param array $data
     * @return $this
     */
    public function setData($data = [])
    {
        $this->data = $data;
        return $this;
    }

    /**
     * 获取异常数据
     *
     * @return array
     */
    public function getData()
    {
        if (config('app-ext.show_raw_exception_message', false)) {
            $data = [
                'code' => $this->getCode(),
                'message' => $this->getMessage(),
                'file' => $this->getFile(),
                'line' => $this->getLine(),
                'trace' => explode("\n", $this->getTraceAsString())
            ];

            if (!empty($this->getPrevious())) {
                $data['trace'] = explode("\n", $this->getPrevious()->getTraceAsString());
            }

            $this->data = array_merge($this->data, ['exception' => $data]);
        }

        return $this->data;
    }

    /**
     * 获取默认调用的响应方法
     *
     * @return string
     */
    protected function getDefaultResponseMethod()
    {
        return 'exception';
    }

    /**
     * 获取默认响应状态码
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function getDefaultResponseCode()
    {
        return config('app-ext.api_response_code.exception', FoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * 异常信息转换为请求响应结果
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        $code = empty($this->getCode()) ? $this->getDefaultResponseCode() : $this->getCode();
        $method = $this->getDefaultResponseMethod();
        $message = config('app-ext.show_raw_exception_message', false) ? $this->getMessage() : '系统正在开小差，请稍后重新尝试哦~';

        return Response::$method($message, $this->getData(), $code);
    }

    /**
     * 自定义report
     *
     * @return void
     */
    public function report()
    {
        $context = [
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
            'data' => $this->data,
            'request' => [
                'url' => request()->url(),
                'input' => request()->input(),
                'ip' => request()->getClientIp(),
            ],
            'exception' => $this,
        ];

        Log::error($this->getMessage(), $context);
    }
}
