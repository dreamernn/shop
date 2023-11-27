<?php
/**
 * @filesource Jwt.php
 * @brief      JWT token
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace Common;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;

class Jwt {
    const JWT_REFRESH_CHECK_KEY = 'shop_token';
    const JWT_RAND_KEY          = 'randNum';
    const JWT_KEY               = 'Y2Fuc21wMGp6eDFmeTJhZ2UzY2Nn';


    const TOKEN_ACCESS_TTL  = 30 * 24 * 3600;
    const TOKEN_REFRESH_TTL = 157680000;

    private $_jwtBuilder = null;
    private $_sha256 = null;

    private $_iisuer = 'local.shop_api.com';
    private $_audience = 'shop';
    private $_exp = 0;
    private $_nbf = 0;
    private $_iat = 0;
    private $_jwtId = 'shop0j1f2a3c';//自定义ID

    public function __construct() {
        $this->_jwtBuilder = new Builder();
        $this->_sha256     = new Sha256();
    }

    public function reset() {
        $this->_jwtBuilder = new Builder();
    }

    public function getAccessToken($tokenData) {
        return $this->encode($tokenData);
    }


    public function getRefreshToken($tokenData, $accessToken) {
        $this->reset();
        $tokenData[self::JWT_REFRESH_CHECK_KEY] = $accessToken;
        $token                                  = $this->encode($tokenData, self::TOKEN_REFRESH_TTL);

        return $token;
    }

    public function decodeRefreshToken($token, $refreshToken) {
        $refreshTokenData = $this->decodeToken($refreshToken);

        if (!isset($refreshTokenData[self::JWT_REFRESH_CHECK_KEY])) {

            throw new JwtException(JwtException::REFRESH_VERIFYTOKEN_FAIL);
        }

        if ($token != $refreshTokenData[self::JWT_REFRESH_CHECK_KEY]) {

            throw new JwtException(JwtException::REFRESH_VERIFYTOKEN_FAIL);
        }

        $tokenData = [];
        foreach ($refreshTokenData as $key => $data) {

            if ($key == self::JWT_RAND_KEY || $key == self::JWT_REFRESH_CHECK_KEY) {

                continue;
            }
            $tokenData[$key] = $data;
        }

        return $tokenData;
    }

    public function decodeTokenWithoutVerify($token) {
        $result  = null;
        $token   = $this->parserToken($token);
        $result  = $token->getClaims();
        $jwtInfo = [];
        foreach ($result as $item) {

            if (
                !($item instanceof \Lcobucci\JWT\Claim\EqualsTo)
                && !($item instanceof \Lcobucci\JWT\Claim\GreaterOrEqualsTo)
                && !($item instanceof \Lcobucci\JWT\Claim\LesserOrEqualsTo)
            ) {
                $jwtInfo[$item->getName()] = $item->getValue();
            }
        }

        return $jwtInfo;
    }


    public function isExpire($token) {

        $result = null;

        $token = $this->parserToken($token);
        $this->verifyToken($token);

        $data   = new ValidationData(time());
        $result = $token->validate($data);

        if ($result == false) {
            return false;
//            throw new JwtException(JwtException::EXPIRED);
        }

        return $result;
    }


    public function encode(array $infos, $ttl = self::TOKEN_ACCESS_TTL) {
        $this->_iat = time();//当前时间
        $this->_nbf = $this->_iat;//生效时间，此处表示立即生效
        $this->_exp = $this->_iat + $ttl;//过期时间

        $this->_jwtBuilder->setIssuer($this->_iisuer)
            ->setAudience($this->_audience)
            ->setId($this->_jwtId, false)
            ->setIssuedAt($this->_iat)
            ->setNotBefore($this->_nbf)
            ->setExpiration($this->_exp);

        $this->_jwtBuilder->set(self::JWT_RAND_KEY, rand(1000000, 9999999));
        foreach ($infos as $key => $data) {

            $this->_jwtBuilder->set($key, $data);
        }

        $this->_jwtBuilder->sign($this->_sha256, self::JWT_KEY);

        return ($this->_jwtBuilder->getToken()).'';
    }

    /*
     *  base encode
     */
    private function baseEncode(array $infos) {
        foreach ($infos as $key => $data) {
            $this->_jwtBuilder->set($key, $data);
        }
        $this->_jwtBuilder->sign($this->_sha256, self::JWT_KEY);

        return ($this->_jwtBuilder->getToken()).'';
    }

    public function decodeToken($token) {
        $result = null;
        $token  = $this->parserToken($token);
        $this->verifyToken($token);

        //verify
        $data = new ValidationData(time());
        $data->setIssuer($this->_iisuer);
        $data->setAudience($this->_audience);
        $data->setId($this->_jwtId);

        $result = $token->validate($data);
        if ($result == false) {

            throw new JwtException(JwtException::VERIFY_DATA_ERROR, JwtException::VERIFY_DATA_ERROR);
        }

        //merge date
        if ($result == true) {

            $result = $token->getClaims();

            $jwtInfo = [];
            foreach ($result as $item) {
                if (
                    !($item instanceof \Lcobucci\JWT\Claim\EqualsTo)
                    && !($item instanceof \Lcobucci\JWT\Claim\GreaterOrEqualsTo)
                    && !($item instanceof \Lcobucci\JWT\Claim\LesserOrEqualsTo)
                ) {
                    $jwtInfo[$item->getName()] = $item->getValue();
                }
            }
            $result = $jwtInfo;
        }

        return $result;
    }

    private function verifyToken($token) {
        if (!$token->verify($this->_sha256, self::JWT_KEY)) {

            throw new JwtException('VERIFY_DECODE_ERROR', JwtException::VERIFY_DECODE_ERROR);
        }
    }

    private function parserToken($token) {

        try {

            $token = (new Parser())->parse((string)$token);
        } catch (\Exception $e) {
            throw new JwtException(JwtException::PARSE_ERROR, JwtException::PARSE_ERROR);
        }

        return $token;
    }
}
