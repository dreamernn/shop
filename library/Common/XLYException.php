<?php
namespace Common;

use Exception;

/**
 * error
 * @author xiangchen.meng(xiangchen0814@cmcm.com)
 */
class XLYException extends Exception
{
    
    /*
     * defined exception
     * 1000 ~ 1999  params error
     * 2000 ~ 9999  data error
     */
    const ERROR_CODE_PARAMETER_WRONG = 1000;
    const ERROR_MESSAGE_PARAMETER_WRONG = 'Parameter is wrong';

    const ERROR_EMAIL_FORMAT_CODE = 1102;
    const ERROR_EMAIL_FORMAT_MESSAGE = 'Email format error';

    const ERROR_PHONE_FORMAT_CODE = 1103;
    const ERROR_PHONE_FORMAT_MESSAGE = 'Phone number format error';

    const USER_NEED_LOGIN_ERROR_CODE = 2001;
    const USER_NEED_LOGIN_ERROR_MESSAGE = 'Please log in again!';

    const USER_NEED_REDIRECT_ERROR_CODE = 2002;
    const USER_NEED_REDIRECT_ERROR_MESSAGE = 'Redirect！';

    const USER_INFO_ERROR_CODE = 2003;
    const USER_INFO_ERROR_MESSAGE = 'Invalid user information';

    const CART_ADD_ERROR_CODE = 3001;
    const CART_ADD_ERROR_MESSAGE = 'That item already exists in the shopping cart';

    const ORDER_ADD_ERROR_CODE = 4001;
    const ORDER_ADD_ERROR_MESSAGE = 'add order error';

    const PRODUCT_EDIT_ERROR_CODE = 4001;
    const PRODUCT_EDIT_ERROR_MESSAGE = 'update the product error';



}
