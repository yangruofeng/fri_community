<?php

class errorCodesEnum extends Enum
{
    const UNKNOWN_ERROR = 0;        // 没有定义的错误
    const NO_ERROR = 200;  // 无任何错误
    const SIGN_ERROR = 1001;  // 签名错误
    const DATA_LACK = 1002; // 参数缺乏
    const INVALID_PARAM = 1003;  // 非法参数
    const INVALID_TOKEN = 1004;  // 非法token
    const USER_EXIST = 1005;  // 用户已存在
    const PHONE_USED = 1006;  // 电话已使用
    const PHONE_VERIFIED = 1007; // 电话已验证
    const DATA_EXPIRED = 1008;  // 数据过期
    const MEMBER_NOT_EXIST = 1009;  // member不存在
    const NO_LOGIN = 1010;  //没有登录
    const SESSION_EXPIRED = 10001;  // session 过期
    const DB_ERROR = 10002;        // 数据库操作失败
    const UNEXPECTED_DATA = 10003; // 错误数据
    const NOT_SUPPORTED = 10004;
    const UNIMPLEMENTED = 10005;
    const API_FAILED = 10006;    // api错误
    const NOT_PERMITTED = 10007;
    const LOGIN_NULLIFIED = 10008;
    const DATA_INCONSISTENCY = 10009;   // 数据不一致
    const DATA_DUPLICATED = 10010;  // 数据已经存在
    const REQUEST_FLOOD = 10011;        // 请求过于频繁
    const CONFIG_ERROR = 10012; // 配置错误

    // 业务相关的CODE
    const SMS_CODE_ERROR = 11013; // 短信验证码错误
    const NO_LOAN_PRODUCT = 11014;  // 没有贷款产品
    const LOAN_PRODUCT_UNSHELVE = 11015; // 产品已下架(历史版本)
    const LOAN_PRODUCT_NX = 11016;  // 非产品的执行版本
    const NO_LOAN_INTEREST = 11017;  // 没有设置利率信息
    const NO_INSURANCE_ITEM = 11018; // 没有保险产品投保项
    const NO_INSURANCE_PRODUCT = 11019;  // 没有保险产品
    const INSURANCE_PRODUCT_NX = 11020; // 非执行版本
    const CREATE_INSURANCE_CONTRACT_FAIL = 11021;  // 创建保险合同失败
    const WITHDRAW_AMOUNT_INVALID = 11022; // 取现金额非法
    const OUT_OF_PER_WITHDRAW = 11023;  // 单次取现超额
    const OUT_OF_DAY_WITHDRAW = 11024; // 当日取现超额
    const NO_LOAN_ACCOUNT = 11025;  // 不存在贷款账户
    const NO_BIND_ACE_ACCOUNT = 11026;  // 没有绑定ACE账号
    const OUT_OF_ACCOUNT_CREDIT = 11027;  // 超出信用额度
    const NO_CONTRACT = 11028;  // 没有合同信息
    const TWO_PWD_DIFFER = 11029; // 两次密码不一致
    const PASSWORD_ERROR = 11030;  // 密码错误
    const NO_MESSAGE = 11031;  // 消息不存在
    const REPAYMENT_UN_MATCH_LOAN_TIME = 11032; // 贷款时间周期和还款方式不匹配
    const ACE_ACCOUNT_NOT_EXIST = 11033;  // ACE账户不存在
    const NO_PASSPORT = 11034;  // 没有登陆通行令牌
    const PASSPORT_EXPIRED = 11035;  // 登陆通行令牌失效
    const NO_ACCOUNT_HANDLER = 11036;  // 没有操作账户
    const UNDER_COOL_TIME = 11037;  // 冷却时间内，稍后再试
    const ACCOUNT_NOT_VALID = 11038;  // 账号格式不符合
    const PASSWORD_NOT_STRONG = 11039; // 密码格式错误或强度不够
    const EMAIL_BEEN_REGISTERED = 11040;  // 邮箱已被注册
    const FUNCTION_CLOSED = 11041;  // 后台功能关闭了
    const OUT_OF_CREDIT_BALANCE = 11042;  // 超出信用余额
    const INVALID_PHONE_NUMBER = 11043;  // 非法电话号码
    const INVALID_EMAIL = 11044;  // 非法邮箱
    const CAN_NOT_CANCEL_CONTRACT = 11045;  // 合同进行中，不能取消
    const LOAN_CONTRACT_CAN_NOT_REPAYMENT = 11046;  // 贷款合同未执行，不能还款
    const INSUFFICIENT_REPAYMENT_CAPACITY = 11047;  // 还款能力不足
    const APPROVING_CAN_NOT_DELETE = 11048;  // 审核中，不能删除
    const AMOUNT_TOO_LITTLE = 11049;  // 金额太小
    const INVALID_PERIOD_NUM = 11050;  // 不合理的期数数量
    const NO_CURRENCY_EXCHANGE_RATE = 11051;  // 没有设置汇率
    const NOT_SUPPORT_PREPAYMENT = 11052;  // 该合同不支持提前还款
    const NOT_CERTIFICATE_ID = 11053;  // 没有认证身份证
    const ID_SN_ERROR = 11054;  // 身份证号错误
    const ID_SN_HAS_CERTIFICATED = 11055;  // 身份证号已经被认证
    const SAME_PASSWORD = 11056;  // 新、旧密码一样
    const NOT_SET_TRADING_PASSWORD = 11057;  // 没有设置交易密码
    const MEMBER_UN_GRANT_CREDIT = 11058;  // 还未授信
    const INVALID_AMOUNT = 11059;  // 不合理的数量
    const NOT_ACE_MEMBER = 11060;  // 不是ACE的member
    const SMS_CODE_SEND_FAIL = 11061;  // 验证码发送失败
    const USER_NOT_EXISTS = 11062;  // 用户不存在
    const USER_LOCKED = 11063;  // 用户被锁定
    const HAVE_HANDLED = 11064;  // 已经处理了
    const HAVE_CANCELED = 11065;  // 已经取消
    const UN_MATCH_OPERATION = 11066;  // 不匹配操作
    const BANK_ALREADY_BOUND = 11067;  // 银行卡已经绑定了
    const NO_LOGIN_ACCESS = 11068;  // 账号没有登录权限
    const HANDLING_LOCKED = 11069;  // 处理锁定中，不能执行其他操作
    const CONTRACT_BEEN_PAID_OFF = 11070; // 合同已还清
    const CONTRACT_BEEN_WRITTEN_OFF = 11071;  // 合同已核销

    const BALANCE_NOT_ENOUGH = 11100;  // 余额不足
    const BILL_NOT_EXIST = 11101;  // 账单不存在

    // API相关错误代码
    const API_ERROR_ACE_BASE = 20000;   // ACE API错误CODE的基础编号
}


class globalOrderStateEnum extends Enum
{
    const ORDER_STATE_CANCEL = "0";  // 取消
    const ORDER_STATE_CREATED = '1'; // 创建
    const ORDER_STATE_PENDING_PAY = "10";  // 待支付
    const ORDER_STATE_PAYING = '11';  // 正在支付
    const ORDER_STATE_PAID = "20";  // 已支付
    const ORDER_STATE_SUCCESS = "40";  // 完成
}

class treeKeyTypeEnum extends Enum
{
    const ADDRESS = 'region';  // 地址key
}

class addressCategoryEnum extends Enum
{
    const MEMBER_RESIDENCE_PLACE = 'residence_place';
}

class partnerEnum extends Enum
{
    // 合作伙伴code
    const ACE = 'ace';
}

class memberSourceEnum extends Enum
{
    const ONLINE = 0;  // 网络
    const CLIENT = 1;  // 柜台
    const THIRD = 10;  // 第三方

}

class memberStateEnum extends Enum
{
    const CANCEL = 0;  // 注销
    const CREATE = 1;  // 创建
    const CHECKED = 10;  // 已检查
    const LOCKING = 20;  //锁定
    const VERIFIED = 100;  // 已验证
}

class newMemberCheckStateEnum extends Enum
{
    const CREATE = 0;   // 新创建
    const LOCKED = 10;  // 锁定
    const CLOSE = 11;   //关闭
    const ALLOT = 20;   //分配给co
    const PASS = 100;  // 通过验证
}

class operateTypeEnum extends Enum
{
    const NEW_CLIENT = 'new_client';
    const REQUEST_LOAN = 'request_loan';
    const CERTIFICATION_FILE = 'certification_file';
}

class creditProcessEnum extends Enum
{
    // 信用激活过程
    const FINGERPRINT = 'fingerprint';  // 指纹录入
    const AUTHORIZED_CONTRACT = 'authorized_contract';  // 授权合同
}

class creditEventTypeEnum extends Enum
{
    const GRANT = 'grant';
    const CREDIT_LOAN = 'credit_loan';
}

class smsTaskType extends Enum
{
    const VERIFICATION_CODE = "VerificationCode";
    const PIN_CODE = "PinCode";
    const WALLET_CHANGED = "WalletChanged";
    const LUCKY_NOTICE = 'LuckyNotice';
    const TOPUP_NOTICE = 'TopupNotice';
}

class smsTaskState extends Enum
{
    const NONE = 0;
    const CREATE = 1;
    const SENDING = 10;
    const SEND_FAILED = 11;
    const SEND_SUCCESS = 20;
    const CANCEL = 30;
}


class phoneCodeCDEnum extends Enum
{
    const CD = 60;  // 发送短信验证码的冷却时间(s)
}

class emailCoolTimeEnum extends Enum
{
    const CD = 60;
}

class memberLoginTypeEnum extends Enum
{
    const LOGIN_CODE = 1;
    const PHONE = 2;
    const EMAIL = 3;
}

class insuranceProductStateEnum extends Enum
{
    const TEMP = 10;
    const ACTIVE = 20;
    const INACTIVE = 30;
    const HISTORY = 40;
}

class objGuidTypeEnum extends Enum
{
    const CLIENT_MEMBER = 1;
    const UM_USER = 2;
    const SITE_BRANCH = 3;
    const PARTNER = 4;
    const BANK_ACCOUNT = 5;
    const SHORT_LOAN = 6;
    const LONG_LOAN = 7;
    const SHORT_DEPOSIT = 8;
    const LONG_DEPOSIT = 9;
    const GL_ACCOUNT = 10;
}

class appTypeEnum extends Enum
{
    const MEMBER_APP = 'smarithiesak-member';
    const OPERATOR_APP = 'credit_officer';
}

class loanAccountTypeEnum extends Enum
{
    const MEMBER = 0;
    const PARTNER = 10;
    const DEALER = 20;
    const LEGAL = 30;
}

class insuranceAccountTypeEnum extends Enum
{
    const MEMBER = 0;
    const PARTNER = 10;
    const DEALER = 20;
    const LEGAL = 30;
}

class schemaStateTypeEnum extends Enum
{
    const CANCEL = -1;  // 取消
    const CREATE = 0;
    const GOING = 10;  // 开始执行
    const FAILURE = 11;
    const COMPLETE = 100;

}

class loanContractStateEnum extends Enum
{
    const CANCEL = -1;
    const CREATE = 0; // 新建，待审核
    const PENDING_APPROVAL = 10;
    const REFUSED = 11;  // 审核拒绝
    const PENDING_DISBURSE = 20;  // 待放款，进入执行状态
    const PROCESSING = 30;
    const PAUSE = 90;  // 执行异常，人工介入
    const COMPLETE = 100;
    const WRITE_OFF = 101;  // 注销
}

class dueDateTypeEnum extends Enum
{
    // 还款日类型 0 固定日期 1 每周 2 每月 3 每年
    const FIXED_DATE = 0;
    const PER_WEEK = 1;
    const PER_MONTH = 2;
    const PER_YEAR = 3;
    const PER_DAY = 4;  // 每天
}

class insuranceContractStateEnum extends Enum
{
    const CANCEL = -1;
    const CREATE = 0;  // 待审核
    const PENDING_APPROVAL = 10;
    const REFUSED = 11;  // 审核拒绝
    const PENDING_RECEIPT = 20;  // 待收款
    const PROCESSING = 30;  // 进行中
    const PAUSE = 90;  // 执行异常，人工介入
    const COMPLETE = 100;
    const WRITE_OFF = 101;  // 注销
}

class contractPrefixSNEnum extends Enum
{
    const LOAN = 1;
    const INSURANCE = 2;
}

class memberAccountHandlerTypeEnum extends Enum
{
    const CASH = 0;
    const BANK = 10;
    const PARTNER_ASIAWEILUY = 21;
    const PARTNER_LOAN = 22;
    const PASSBOOK = 30;
}


class accountHandlerStateEnum extends Enum
{
    const ACTIVE = 10;
    const HISTORY = 20;
}

class certSourceTypeEnum extends Enum
{
    const MEMBER = 0;  // 会员自提交
    const CLIENT = 1;  // 柜台提交
    const OPERATOR = 2;  // operator app（业务员提交）
}

class certificationTypeEnum extends Enum
{
    const ID = 1; //身份证
    const FAIMILYBOOK = 2; //户口本
    const PASSPORT = 3; //护照
    const HOUSE = 4; //房屋资产证明
    const CAR = 5; //汽车资产证明
    const WORK_CERTIFICATION = 6; // 工作证明
    //const CIVIL_SERVANT = 7;  // 公务员证明
    const GUARANTEE_RELATIONSHIP = 8;  // 担保人信息
    const LAND = 9;  // 土地
    const RESIDENT_BOOK = 10;  // 居住证
    const MOTORBIKE = 11;  // 摩托车

}

class certImageKeyEnum extends Enum
{
    const ID_HANDHELD = 'id_handheld';  // 手持身份证照片
    const ID_FRONT = 'id_front';   // 身份证正面
    const ID_BACK = 'id_back';  // 身份证背面

    const FAMILY_BOOK_FRONT = 'family_book_front';  // 户口本正面
    const FAMILY_BOOK_BACK = 'family_book_back';  // 户口本背面
    const FAMILY_BOOK_HOUSEHOLD = 'family_book_household';  // 户主页

    const RESIDENT_BOOK_FRONT = 'resident_book_front';  // 居住证正面
    const RESIDENT_BOOK_BACK = 'resident_book_back';  // 居住证背面

    const WORK_CARD = 'work_card';                      // 工作卡或证明
    const WORK_EMPLOYMENT_CERTIFICATION = 'work_employment_certification';  // 雇佣证明

    const FAMILY_RELATION_CERT_PHOTO = 'family_relation_cert_photo';  // 家庭关系人证件照

    const MOTORBIKE_PHOTO = 'motorbike_photo';  // 摩托车照片
    const MOTORBIKE_CERT_FRONT = 'motorbike_cert_front';  // 摩托车证件正面
    const MOTORBIKE_CERT_BACK = 'motorbike_cert_back';  // 摩托车证件背面

    const CAR_FRONT = 'car_front';                  // 汽车前面照片
    const CAR_BACK = 'car_back';                    // 汽车后面照片
    const CAR_CERT_FRONT = 'car_cert_front';        // 汽车证件正面
    const CAR_CERT_BACK = 'car_cert_back';          // 汽车证件背面

    const HOUSE_PROPERTY_CARD = 'house_property_card';  // 房屋产权证
    const HOUSE_RELATIONSHIPS_CERTIFY = 'house_relationships_certify';  // 房屋关系证明
    const HOUSE_FRONT = 'house_front';               // 房屋正面图
    const HOUSE_SIDE_FACE = 'house_side_face';     // 房屋侧面图
    const HOUSE_FRONT_ROAD = 'house_front_road';    // 房屋门前马路图
    const HOUSE_INSIDE = 'house_inside';           // 房内图

    const LAND_PROPERTY_CARD = 'land_property_card';   // 土地产权证
    const LAND_TRADING_RECORD = 'land_trading_record';   // 土地交易记录表


}

class certTypeCalculateValueEnum extends Enum
{
    const ID = 1;
    const FAMILY_BOOK = 2;
    const GUARANTEE_RELATIONSHIP = 4;
    const WORK_CERT = 8;
    const CIVIL_SERVANT = 16;
    const CAR_CERT = 32;
    const HOUSE_CERT = 64;
    const LAND_CERT = 128;
    const PASSPORT = 256;
    const RESIDENT_BOOK = 512;
    const MOTORBIKE = 1024;
    const FAMILY_RELATION = 4;

}

class certStateEnum extends Enum
{
    const LOCK = -1;  // 审核中，锁定
    const CREATE = 0;
    const PASS = 10;
    const EXPIRED = 11;  // 过期
    const NOT_PASS = 100;
}

class creditLevelTypeEnum extends Enum
{
    const MEMBER = 0;
    const MERCHANT = 1;
}

class blackTypeEnum extends Enum
{
    const LOGIN = 1; //登录
    const DEPOSIT = 2; //存款
    const INSURANCE = 3; //保险
    const CREDIT_LOAN = 4; //信用贷
    const MORTGAGE_LOAN = 5; //抵押贷
}

class fileDirsEnum extends Enum
{
    // 文件夹名常量
    const CLIENT = 'client';
    const ID = 'id';
    const PASSPORT = 'passport';
    const FAMILY_BOOK = 'familybook';
    const FAMILY_RELATION = 'family_relation';
    const WORK_CERT = 'work_cert';
    const MOTORBIKE = 'motorbike';
    const HOUSE = 'house';
    const CAR = 'car';
    const LAND = 'land';
    const RESIDENT_BOOK = 'resident_book';
}

class disbursementStateEnum extends Enum
{
    const GOING = 10;
    const FAILED = 11;
    const DONE = 100;
}

class repaymentStateEnum extends Enum
{
    const GOING = 10;
    const FAILED = 11;
    const DONE = 100;
}

class loanProductStateEnum extends Enum
{
    const TEMP = 10;
    const ACTIVE = 20;
    const INACTIVE = 30;
    const HISTORY = 40;
}

class loanApplyStateEnum extends Enum
{
    // 贷款申请状态
    const LOCKED = -1;
    const CREATE = 0;  // 客人提交
    const OPERATOR_REJECT = 1;  // Operator拒绝
    const ALLOT_CO = 2;  // 指派给CO
    const CO_HANDING = 10;  // CO正在这处理
    const CO_CANCEL = 11;  // CO直接cancel了
    const CO_APPROVED = 20;  // CO check通过
    const BM_APPROVED = 30;  // BM 审核通过 权限内 -> ALL_APPROVED
    const BM_CANCEL = 31;  // BM 否决
    const HQ_APPROVED = 40;  // HQ 审核通过 -> ALL_APPROVED
    const HQ_CANCEL = 41;  // HQ否决
    const ALL_APPROVED = 50;  // 可转为contract的状态
    const ALL_APPROVED_CANCEL = 51;  // approve后被取消
    const DONE = 100;  // 已经转为合同


}


class loanApplySourceEnum extends Enum
{
    const MEMBER_APP = 'member_app';
    const OPERATOR_APP = 'operator_app';
    const PHONE = 'phone';
    const FACEBOOK = 'facebook';
    const CLIENT = 'client'; // 柜台
}

class loanRepaymentStateEnum extends Enum
{
    const START = 10;
    const FAILURE = 11;
    const SUCCESS = 100;
}

class requestRepaymentTypeEnum extends Enum
{
    const SCHEME = 'schema';
    const BALANCE = 'balance';
}

class repaymentWayEnum extends Enum
{
    const CASH = 0;
    const AUTO_DEDUCTION = 1;  // partner-> api
    const BANK_TRANSFER = 2;  // bank
}

class requestRepaymentStateEnum extends Enum
{
    const CREATE = 0;//新建
    const PROCESSING = 20;//查账中
    const FAILED = 21;//未到账
    const RECEIVED = 30;  // 只是钱到账，没更改合同
    const SUCCESS = 100;// 到账，合同已更改
}

class prepaymentApplyStateEnum extends Enum
{
    // 提前还款申请的状态
    const CREATE = 0;       // 申请
    const AUDITING = 10;    //提前还款审核中
    const DISAPPROVE = 11;  //审核不通过
    const APPROVED = 20;  // 审核通过
    const PAID = 30;       // 已付款
    const PROCESSING = 31; // 查账中
    const RECEIVED = 40;  // 钱已到账，未处理合同
    const SUCCESS = 100;  // 合同处理完成
    const FAIL = 101;     // 合同处理失败
}

class prepaymentRequestTypeEnum extends Enum
{
    const PARTLY = 0;    // 部分偿还
    const FULL_AMOUNT = 1;  // 全部偿还
    const LEFT_PERIOD = 2;  // 偿还期数方式
}

class penaltyOnEnum extends Enum
{
    const OVERDUE_PRINCIPAL = 'overdue_principal';
    const PRINCIPAL_INTEREST = 'principal_interest';
    const TOTAL = 'total';
}

class singleRepaymentEnum extends Enum
{
    const DAYS_7 = '7_days';
    const DAYS_15 = '15_days';
    const MONTH_1 = '1_month';
    const MONTHS_3 = '3_months';
    const MONTHS_6 = '6_months';
    const YEAR_1 = '1_year';
}

class interestRatePeriodEnum extends Enum
{
    const YEARLY = 'yearly';
    const SEMI_YEARLY = 'semi_yearly';
    const QUARTER = 'quarter';
    const MONTHLY = 'monthly';
    const WEEKLY = 'weekly';
    const DAILY = 'daily';
}

class loanPeriodUnitEnum extends Enum
{
    const YEAR = 'year';
    const MONTH = 'month';
    const DAY = 'day';
}


class interestPaymentEnum extends Enum
{
    const SINGLE_REPAYMENT = 'single_repayment';
    const FIXED_PRINCIPAL = 'fixed_principal';
    const ANNUITY_SCHEME = 'annuity_scheme';
    const FLAT_INTEREST = 'flat_interest';
    const BALLOON_INTEREST = 'balloon_interest';
}


class memberFamilyStateEnum extends Enum
{
    const CREATE = 0;
    const INVALID = 10;  // 无效
    const REMOVE = 11;  // 解除
    const APPROVAL = 100; // 核准
}

class memberGuaranteeStateEnum extends Enum
{
    const CANCEL = -1;  // 取消
    const CREATE = 0;
    const REJECT = 11;
    const ACCEPT = 100;
}

class workStateStateEnum extends Enum
{
    const CREATE = 0;
    const APPROVING = 10;
    const INVALID = 11;  // 审核未通过
    const VALID = 20;  // 核实
    const HISTORY = 30;    // 历史
}

class assetStateEnum extends Enum
{
    const CANCEL = -1;  // 删除
    const CREATE = 0;   // 新加
    const INVALID = 11;  //无效
    const CERTIFIED = 100;  // 已认证
}

class clientTypeRateEnum extends Enum
{
    const STAFF = 'staff'; // 公司内部员工
    const GOVERNMENT = 'government';    // 政府员工
    const RIVAL_CLIENT = 'rival_client';  // 对手客户
}

class contractWriteOffTypeEnum extends Enum
{
    const SYSTEM = 10;
    const ABNORMAL = 20;
}

class writeOffStateEnum extends Enum
{
    const CREATE = 0;
    const APPROVING = 10;
    const INVALID = 11;  // 审核未通过
    const COMPLETE = 100;  // 审核通过，已核销
}

class loanDeductingPenaltiesState extends Enum
{
    const CREATE = 0;
    const PROCESSING = 10;
    const DISAPPROVE = 20;
    const APPROVE = 30;
    const USED = 40;
}

class helpCategoryEnum extends Enum
{
    const CREDIT_LOAN = 'credit_loan';
    const MORTGAGE_LOAN = 'mortgage_loan';
    const SAVINGS = 'savings';
    const INSURANCE = 'insurance';
}

class helpStateEnum extends Enum
{
    const CREATE = 0;
    const NOT_SHOW = 10;
    const SHOW = 100;
}

class pointEventEnum extends Enum
{
    const ADD = 'add';
    const AUDIT = 'audit';
    const VERIFY = 'verify';
}

class currencyEnum extends Enum
{
    const USD = "USD";
    const KHR = "KHR";
    const CNY = "CNY";
    const VND = 'VND';
    const THB = 'THB';
}

//用户定义enum start
class userDefineEnum extends Enum
{
    const MORTGAGE_TYPE = 'mortgage_type';
    const GUARANTEE_TYPE = 'guarantee_type';
    const LOAN_USE = 'loan_use';
    const GENDER = 'gender';
    const OCCUPATION = 'occupation';
    const FAMILY_RELATIONSHIP = 'family_relationship';
    const GUARANTEE_RELATIONSHIP = 'guarantee_relationship';  // Guarantee Relationship
    const MARITAL_STATUS = 'marital_status';
//    const BANK_CODE = 'bank_code';
}

//用户定义enum end

class trxTypeEnum extends Enum
{
    const DEC = -1;  // 减
    const INVALID = 0;  // 无效
    const INC = 1;  // 加
}

class apiStateEnum extends Enum
{
    const CANCELLED = 0;
    const CREATED = 10;
    const STARTED = 20;
    const PENDING_CHECK = 30;
    const FINISHED = 40;
}

class nationalityEnum extends Enum
{
    const CAMBODIA = 'cambodia';
    const CHINESE = 'chinese';
}

class refBizTypeEnum extends Enum
{
    // API 外部业务类型
    const LOAN = 'loan';
    const INSURANCE = 'insurance';
    const SAVINGS = 'savings';
}

class limitKeyEnum extends Enum
{
    const LIMIT_LOAN = 'limit_loan';
    const LIMIT_DEPOSIT = 'limit_deposit';
    const LIMIT_EXCHANGE = 'limit_exchange';
    const LIMIT_WITHDRAW = 'limit_withdraw';
    const LIMIT_TRANSFER = 'limit_transfer';
}

class passbookTypeEnum extends Enum
{
    const ASSET = "asset";      // 资产类
    const DEBT = "debt";        // 负债类
    const EQUITY = "equity";    // 所有着权益类
    const PROFIT = "profit";    // 损益类 - 收入
    const COST = "cost";        // 成本类
    const COMMON = "common";    // 共同类
}

class passbookObjTypeEnum extends Enum
{
    // 储蓄账户对象类型
    const CLIENT_MEMBER = 'client_member';
    const UM_USER = 'um_user';
    const BRANCH = 'branch';
    const GL_ACCOUNT = 'gl_account';
    const BANK = 'bank';
    const PARTNER = 'partner';
}

class passbookStateEnum extends Enum
{
    const CANCEL = -1;
    const ACTIVE = 100;
    const FREEZE = 10;
}

class passbookTradingStateEnum extends Enum
{
    const CREATE = 0;
    const DONE = 100;
}

class passbookTradingTypeEnum extends Enum
{
    const BANK_TO_BRANCH = 'bank_to_branch';
    const BANK_TO_HEADQUARTER  = 'bank_to_headquarter';
    const INIT_BRANCH = 'init_branch';
    const BRANCH_TO_BANK = 'branch_to_bank';
    const BRANCH_TO_CASHIER = 'branch_to_cashier';
    const BRANCH_TO_HEADQUARTER = 'branch_to_headquarter';
    const RECEIVE_CAPITAL = 'receive_capital';
    const CASHIER_TO_BRANCH = 'cashier_to_branch';
    const DEPOSIT_BY_BANK = 'deposit_by_bank';
    const DEPOSIT_BY_CASH = 'deposit_by_cash';
    const DEPOSIT_BY_PARTNER = 'deposit_by_partner';
    const TRANSFER = 'transfer';
    const WITHDRAW_BY_BANK = 'withdraw_by_bank';
    const WITHDRAW_BY_CASH = 'withdraw_by_cash';
    const WITHDRAW_BY_PARTNER = 'withdraw_by_partner';
    const HEADQUARTER_TO_BANK = 'headquarter_to_bank';
    const HEADQUARTER_TO_BRANCH = 'headquarter_to_branch';
    const DISBURSE_LOAN = 'disburse_loan';
    const LOAN_PREPAYMENT = 'loan_prepayment';
    const LOAN_REPAYMENT = 'loan_repayment';
}

class passbookAccountFlowStateEnum extends Enum
{
    const CREATE = 0;
    const OUTSTANDING = 90;
    const DONE = 100;
}

class userPositionEnum extends Enum
{
    const CREDIT_OFFICER = 'credit_officer';
    const TELLER = 'teller';
    const CHIEF_TELLER = 'chief_teller';
    const BRANCH_MANAGER = 'branch_manager';
    const OPERATOR = 'operator';
}

class authTypeEnum extends Enum
{
    const BACK_OFFICE = 'back_office';
    const COUNTER = 'counter';
}

/**
 * Class incomingTypeEnum
 * 收入类型
 */
class incomingTypeEnum extends Enum
{
    /**
     * 年费
     */
    const ANNUAL_FEE = "annual_fee_incoming";

    /**
     * 利息
     */
    const INTEREST = "interest_incoming";

    /**
     * 管理费
     */
    const ADMIN_FEE = "admin_fee_incoming";

    /**
     * 手续费
     */
    const LOAN_FEE = "loan_fee_incoming";

    /**
     * 运营费
     */
    const OPERATION_FEE = "operation_fee_incoming";

    /**
     * 保险费
     */
    const INSURANCE_FEE = "insurance_fee_incoming";

    /**
     * 逾期罚金
     */
    const OVERDUE_PENALTY = "overdue_penalty_incoming";

    /**
     * 提前还款违约金
     */
    const PREPAYMENT_PENALTY = "prepayment_penalty_incoming";
}

/**
 * Class businessTypeEnum
 * 业务类型
 */
class businessTypeEnum extends Enum
{
    /**
     * 信用贷
     */
    const CREDIT_LOAN = "credit_loan";
}

/**
 * Class systemAccountCodeEnum
 * 系统账户代码枚举
 */
class systemAccountCodeEnum extends Enum
{
    const HQ_CIV = "hq_civ";
    const HQ_CAPITAL = "hq_capital";
    const HQ_INIT = "hq_init";
    const RECEIVABLE_LOAN_INTEREST = "receivable_loan_interest";
    const EXCHANGE_SETTLEMENT = "exchange_settlement";  // 换汇结算户
    const FEE_INCOMING = "fee_incoming";  // 手续费收入
    const FINANCIAL_EXPENSES = "financial_expenses";  // 财务费用
}

/**
 * Class accountingDirectionEnum
 * 会计记账方向
 */
class accountingDirectionEnum extends Enum
{
    /**
     * 借方
     * 对于资产类、费用类账户，借加
     * 对于负债、所有者权益、收入类账户，借减
     */
    const DEBIT = 0;

    /**
     * 贷方
     * 对于负债、所有者权益、收入类账户，贷加
     * 对于资产类、费用类账户，贷减
     */
    const CREDIT = 1;
}

class withdrawStateEnum extends Enum
{
    const CREATE = 0;
    const DONE = 100;
    const FAIL = 101;
}

class depositStateEnum extends Enum
{
    const CREATE = 0;
    const DONE = 100;
    const FAIL = 101;
}

class bizSceneEnum extends Enum
{
    const APP_MEMBER = "app_member";
    const APP_CO = "app_co";
    const COUNTER = "counter";
    const BACK_OFFICE = "back_office";
}

class bizCheckTypeEnum extends Enum
{
    const TRADING_PASSWORD = 'trading_password';
}

class bizCodeEnum extends Enum
{
    const MEMBER_WITHDRAW_TO_PARTNER = 'member_withdraw_to_partner';
    const MEMBER_TRANSFER_TO_MEMBER = 'member_transfer_to_member';
    const MEMBER_DEPOSIT_BY_PARTNER = 'member_deposit_by_partner';
}

class complaintAdviceEnum extends Enum
{
    const CREATE = 1;
    const HANDLE = 2;
    const CHECKED = 3;

}