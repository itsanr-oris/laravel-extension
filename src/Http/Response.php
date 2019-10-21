<?php /** @noinspection PhpUndefinedClassInspection */

namespace Foris\LaExtension\Http;

use Illuminate\Support\Facades\Response as LaravelResponse;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

/**
 * Class Response
 */
class Response
{
    /**
     * 状态码定义
     */
    const CODE_SUCCESS = 0;
    const CODE_FAILURE = 1;

    /**
     * 响应格式定义
     *
     * @var        string
     */
    const FORMAT_JSON = 'json';

    /**
     * 接口响应状态码
     *
     * @var integer
     */
    protected $code = self::CODE_SUCCESS;

    /**
     * 接口响应信息
     *
     * @var string
     */
    protected $message = '';

    /**
     * 接口响应数据
     *
     * @var array
     */
    protected $data = [];

    /**
     * 响应额外选项信息
     *
     * @var array
     */
    protected $options = [];

    /**
     * 构造函数
     *
     * @param int    $code
     * @param string $message
     * @param array  $data
     * @param array  $options
     */
    public function __construct(
        $code = null,
        $message = '',
        $data = [],
        $options = []
    ) {
        $code = $code ?? config('app-ext.api_response_code.success', self::CODE_SUCCESS);
        $this->setCode($code)->setMessage($message)->setData($data)->setOptions($options);
    }

    /**
     * 设置响应状态码
     *
     * @param int $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * 获取响应状态码
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 设置响应消息
     *
     * @param string $message
     * @return $this
     */
    public function setMessage($message = '')
    {
        $this->message = $message;
        return $this;
    }

    /**
     * 获取响应消息
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * 设置响应数据
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
     * 获取响应数据
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 设置响应选项信息
     *
     * @param array $options
     * @return $this
     */
    public function setOptions($options = [])
    {
        $this->options = $options;
        return $this;
    }

    /**
     * 获取响应选项信息
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * 结果转数组
     *
     * @return array
     */
    public function data()
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }

    /**
     * 返回laravel框架识别的response
     *
     * @param array $options
     * @return \Illuminate\Http\JsonResponse
     */
    public function toLaResponse($options = [])
    {
        $options = array_merge($this->options, $options);
        $format = $options['format'] ?? self::FORMAT_JSON;

        $method = sprintf('to%sResponse', Str::ucfirst(Str::camel($format)));
        return method_exists($this, $method) ? $this->{$method}($options) : $this->toJsonResponse($options);
    }

    /**
     * 获取json响应结果
     *
     * @param array $options
     * @return \Illuminate\Http\JsonResponse
     */
    public function toJsonResponse($options = [])
    {
        return LaravelResponse::json(
            $this->data(),
            $options['http_status'] ?? FoundationResponse::HTTP_OK,
            $options['headers'] ?? [],
            $options['encoding_options'] ?? 0
        );
    }

    /**
     * 快速响应成功结果
     *
     * @param array  $data
     * @param string $message
     * @param int    $code
     * @param array  $options
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success(
        $data = [],
        $message = 'success',
        $code = null,
        $options = []
    ) {
        $code = $code ?? config('app-ext.api_response_code.success', self::CODE_SUCCESS);
        return (new static($code, $message, $data, $options))->toLaResponse();
    }

    /**
     * 快速响应失败结果
     *
     * @param string $message
     * @param array  $data
     * @param int    $code
     * @param array  $options
     * @return \Illuminate\Http\JsonResponse
     */
    public static function failure(
        $message = 'failure',
        $data = [],
        $code = null,
        $options = []
    ) {
        $code = $code ?? config('app-ext.api_response_code.failure', self::CODE_FAILURE);
        return (new static($code, $message, $data, $options))->toLaResponse();
    }

    /**
     * 快速响应未授权结果
     *
     * @param string $message
     * @param array  $data
     * @param int    $code
     * @param array  $options
     * @return \Illuminate\Http\JsonResponse
     */
    public static function forbidden(
        $message = 'forbidden',
        $data = [],
        $code = null,
        $options = []
    ) {
        $options = array_merge(['http_status' => FoundationResponse::HTTP_FORBIDDEN], $options);
        $code = $code ?? config('app-ext.api_response_code.forbidden', FoundationResponse::HTTP_FORBIDDEN);
        return (new static($code, $message, $data, $options))->toLaResponse();
    }

    /**
     * 快速响应未认证结果
     *
     * @param string $message
     * @param array  $data
     * @param int    $code
     * @param array  $options
     * @return \Illuminate\Http\JsonResponse
     */
    public static function unauthorized(
        $message = 'unauthorized',
        $data = [],
        $code = null,
        $options = []
    ) {
        $options = array_merge(['http_status' => FoundationResponse::HTTP_UNAUTHORIZED], $options);
        $code = $code ?? config('app-ext.api_response_code.unauthorized', FoundationResponse::HTTP_UNAUTHORIZED);
        return (new static($code, $message, $data, $options))->toLaResponse();
    }

    /**
     * 快速响应系统异常结果
     *
     * @param string $message
     * @param array  $data
     * @param int    $code
     * @param array  $options
     * @return \Illuminate\Http\JsonResponse
     */
    public static function exception(
        $message = 'exception',
        $data = [],
        $code = null,
        $options = []
    ) {
        $options = array_merge(['http_status' => FoundationResponse::HTTP_INTERNAL_SERVER_ERROR], $options);
        $code = $code ?? config('app-ext.api_response_code.exception', FoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
        return (new static($code, $message, $data, $options))->toLaResponse();
    }

    /**
     * 快速响应404结果
     *
     * @param string $message
     * @param array  $data
     * @param int    $code
     * @param array  $options
     * @return \Illuminate\Http\JsonResponse
     */
    public static function notFound(
        $message = '404 not found',
        $data = [],
        $code = null,
        $options = []
    ) {
        $options = array_merge(['http_status' => FoundationResponse::HTTP_NOT_FOUND], $options);
        $code = $code ?? config('app-ext.api_response_code.notFound', FoundationResponse::HTTP_NOT_FOUND);
        return (new static($code, $message, $data, $options))->toLaResponse();
    }
}
