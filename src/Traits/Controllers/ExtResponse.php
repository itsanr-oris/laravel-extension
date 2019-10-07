<?php /** @noinspection PhpUndefinedClassInspection */

namespace Foris\LaExtension\Traits\Controllers;

use Foris\LaExtension\Http\Response;

/**
 * Trait ExtResponse
 */
trait ExtResponse
{
    /**
     * 获取response实例
     *
     * @return OriginResponse|\Illuminate\Foundation\Application|mixed
     */
    public function response()
    {
        return app('app-ext.response');
    }

    /**
     * 快速响应
     *
     * @param array  $data
     * @param string $message
     * @param int    $code
     * @param array  $options
     * @return \Illuminate\Http\JsonResponse
     */
    public function success(
        $data = [],
        $message = 'success',
        $code = Response::CODE_SUCCESS,
        $options = []
    ) {
        return $this->response()->success($data, $message, $code, $options);
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
    public function failure(
        $message = 'failure',
        $data = [],
        $code = Response::CODE_FAILURE,
        $options = []
    ) {
        return $this->response()->failure($message, $data, $code, $options);
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
    public function forbidden(
        $message = 'forbidden',
        $data = [],
        $code = Response::CODE_FAILURE,
        $options = []
    ) {
        return $this->response()->forbidden($message, $data, $code, $options);
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
    public function unauthorized(
        $message = 'unauthorized',
        $data = [],
        $code = Response::CODE_FAILURE,
        $options = []
    ) {
        return $this->response()->unauthorized($message, $data, $code, $options);
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
    public function exception(
        $message = 'exception',
        $data = [],
        $code = Response::CODE_FAILURE,
        $options = []
    ) {
        return $this->response()->exception($message, $data, $code, $options);
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
    public function notFound(
        $message = '404 not found',
        $data = [],
        $code = Response::CODE_FAILURE,
        $options = []
    ) {
        return $this->response()->notFound($message, $data, $code, $options);
    }
}