<?php
namespace Apiapp\Wxstore;
/**
 * 
 * 微信支付API异常类
 * @author widyhu
 *
 */
class WxPayException {
	public function errorMessage()
	{
		return $this->getMessage();
	}
}
