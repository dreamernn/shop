<?php
namespace Common;

class JwtException extends \Exception {

    const REFRESH_VERIFY_FAIL = 1001;
    const EXPIRED             = 1002;
    const VERIFY_DATA_ERROR   = 1003;
    const VERIFY_DECODE_ERROR = 1004;
    const PARSE_ERROR         = 1005;
    const REFRESH_VERIFYTOKEN_FAIL = 1006;
}
