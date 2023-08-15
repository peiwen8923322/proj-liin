DROP TABLE suppliers;
CREATE TABLE IF NOT EXISTS suppliers(
    seq BIGINT NOT NULL AUTO_INCREMENT COMMENT '流水編號'
    , formcode CHAR(10) NOT NULL PRIMARY KEY COMMENT '表單編號'
    , formstate TINYINT NOT NULL DEFAULT 15 COMMENT '表單狀況'
    , creator VARCHAR(20) NOT NULL DEFAULT 'SYSTEM' COMMENT '建立者'
    , creatdate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT '建立日期'
    , modifier VARCHAR(20) NOT NULL DEFAULT 'SYSTEM' COMMENT '修改者'
    , modifydate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT '修改日期'

    , splrcode CHAR(10) NOT NULL COMMENT '供應商代碼'
    , splrapl VARCHAR(300) NOT NULL COMMENT '供應商名稱'
    , splrunicode VARCHAR(10) COMMENT '供應商統一編號'
    , splrtel VARCHAR(20) COMMENT '供應商電話號碼'
    , splrfax VARCHAR(20) COMMENT '供應商傳真號碼'
    , splrlia VARCHAR(20) COMMENT '供應商聯絡人'
    , splrmbl VARCHAR(20) COMMENT '聯絡人手機號碼'
    , splraddr VARCHAR(300) COMMENT '供應商地址'
    , splremail VARCHAR(100) COMMENT '聯絡人Email'
    , memo VARCHAR(300) COMMENT '備註'

    , INDEX splr_seq_idx(seq)
    , INDEX splr_splrapl_idx(splrapl)
) COMMENT '供應商檔';

DESC suppliers;